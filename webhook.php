<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/Plan.php';
require_once '../models/AnnualFee.php';
require_once __DIR__ . '/../models/Mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Balog\MyLanguageTutor\Models\Mailer;

require_once __DIR__ . '/../../vendor/autoload.php';
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

class PlanController
{
    private $planModel;
    private $annualFeeModel;
    private $planPrices;

    public function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->planPrices = [
            "Casual Learner" => $_ENV['CASUAL_LEARNER_PRICE'],
            "Beginner's Bundle" => $_ENV['BEGINNERS_BUNDLE_PRICE'],
            "Intermediate Pack" => $_ENV['INTERMEDIATE_PACK_PRICE'],
            "Master Class Package" => $_ENV['MASTER_CLASS_PACKAGE_PRICE'],
        ];

        $this->planModel = new Plan();
        $this->annualFeeModel = new AnnualFee();
    }

    /**
     * Email Template
     */
    function getEmailContentWithTemplate($subject, $message_content)
    {
        $template = file_get_contents(__DIR__ . '/general_email_template.html');
        $template = str_replace('{subject}', $subject, $template);
        $template = str_replace('{message_content}', $message_content, $template);
        $template = str_replace('{current_year}', date('Y'), $template);
        return $template;
    }

    public function processRequest()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'checkActiveSubscription':
                    $this->checkActiveSubscription();
                    break;
                case 'processPayment':
                default:
                    $this->processPayment();
                    break;
            }
        } else {
            $this->processPayment();
        }
    }

    private function checkActiveSubscription()
    {
        $username = $_SESSION['username'];

        $latestSubscription = $this->planModel->getUserLatestSubscription($username);

        $hasActiveSubscription = $latestSubscription && ($latestSubscription['number_of_classes'] > 0 && $latestSubscription['number_of_classes'] != $latestSubscription['classes_used']);

        http_response_code(200);

        echo json_encode(['hasActiveSubscription' => $hasActiveSubscription]);
    }

    public function generateRandomOrderID($length = 16) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    

    public function processPayment()
    {
        try {
            if (!isset($_SESSION)) {
                session_start();
            }

            // Retrieve user data from the session
            $username = $_SESSION['username'];
            $userData = $_SESSION['user_data'];
            $fullName = $userData['first_name'] . ' ' . $userData['last_name'];
            $email = $userData['email'];

            // Decode the received JSON data
            $data = json_decode(file_get_contents('php://input'), true);

            // Generate or use provided Order ID
            $orderId = $data['orderID'] ?? $this->generateRandomOrderID();
            $planName = $data['planName'];
            $userPaid = $data['planPrice'];
            $selectedHours = $data['selectedHours'] ?? 1;

            // Check for existing subscriptions
            $latestSubscription = $this->planModel->getUserLatestSubscription($username);
            if ($latestSubscription && ($latestSubscription['number_of_classes'] > 0 && $latestSubscription['number_of_classes'] != $latestSubscription['classes_used'])) {
                http_response_code(400);
                echo json_encode(['error' => 'You still have remaining classes in your current subscription. You cannot purchase a new plan until you have used up all your classes.']);
                return;
            }

            // Validate the plan name and price
            if (!array_key_exists($planName, $this->planPrices)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid plan name']);
                return;
            }

            $expectedPrice = $this->planPrices[$planName];
            if ($planName === "Casual Learner") {
                $expectedPrice *= $selectedHours;
            }
            if ($expectedPrice != $userPaid) {
                http_response_code(400);
                echo json_encode(['error' => 'Price mismatch']);
                return;
            }

            // Stripe checkout session creation
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'cad',
                        'product_data' => ['name' => $planName],
                        'unit_amount' => $userPaid * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://localhost:8000/src/views/student/success.html?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'https://mylanguagetutor.ca/',
            ]);

            // Determine the number of classes based on the selected plan
            $numberOfClasses = $this->determineNumberOfClasses($planName, $selectedHours);

            // Generate receipt and process the subscription in the database
            $receipt = $this->planModel->generateReceipt($orderId, $planName, $userPaid, $numberOfClasses);
            $this->planModel->processSubscription($orderId, $planName, $userPaid, $numberOfClasses, $receipt, $username);

            // Send email notification about successful payment
            $mailer = new Mailer();
            $subject = "Subscription Payment Received";
            $message_content = "
                Dear {$fullName},<br><br>
                Thank you for your subscription payment.<br><br>
                Plan: {$planName}<br>
                Amount Paid: {$userPaid}<br>
                Number of Classes: {$numberOfClasses}<br>
                Order ID: {$orderId}<br><br>
                Should you have any questions, please don't hesitate to contact our support team.<br><br>
                Best regards,<br>
                My Language Tutor Team
            ";
            $body = $this->getEmailContentWithTemplate($subject, $message_content);
            $altBody = strip_tags($message_content);
            $mailer->sendEmail($email, $fullName, $subject, $body, $altBody);

            // Send the checkout session ID in response
            http_response_code(200);
            echo json_encode(['checkoutSessionId' => $checkout_session->id]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Stripe API error: ' . $e->getMessage()]);
            error_log('Exception in processPayment: ' . $e->getMessage());
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
            error_log('Exception in processPayment: ' . $e->getMessage());
        }
    }


    // New function to determine the number of classes
    private function determineNumberOfClasses($planName, $selectedHours)
    {
        $numberOfClasses = $selectedHours;
        if ($planName === "Beginner's Bundle") {
            $numberOfClasses = 5;
        } elseif ($planName === "Intermediate Pack") {
            $numberOfClasses = 10;
        } elseif ($planName === "Master Class Package") {
            $numberOfClasses = 20;
        }
        return $numberOfClasses;
    }
}

$planController = new PlanController();
$planController->processRequest();
