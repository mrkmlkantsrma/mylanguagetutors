<?php
// Include the AnnualFee model
require_once '../models/annualFee.php';
require_once __DIR__ . '/../models/Mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Balog\MyLanguageTutor\Models\Mailer;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * AnnualFeeController Class
 * 
 * Responsible for managing all operations related to annual fees
 */
class AnnualFeeController {
    private $annualFeeModel;

    /**
     * Constructor: initializes the annual fee model
     */
    public function __construct() {
        $this->annualFeeModel = new AnnualFee();
    }

    /**
     * Email Template
     */
    function getEmailContentWithTemplate($subject, $message_content) {
        $template = file_get_contents(__DIR__ . '/general_email_template.html');
        $template = str_replace('{subject}', $subject, $template);
        $template = str_replace('{message_content}', $message_content, $template);
        $template = str_replace('{current_year}', date('Y'), $template);
        return $template;
    }

    /**
     * Handles the action of getting a user's payment status
     */
    public function getUserPaymentStatusAction() {
        if (!isset($_SESSION)) {
            session_start();
        }
    
        try {
            $json_input = file_get_contents('php://input');
            $data = json_decode($json_input, true);
    
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to decode JSON: ' . json_last_error_msg());
            }
    
            $username = $data['username'];
    
            $status = $this->annualFeeModel->getUserPaymentStatus($username);
    
            if ($status) {
                http_response_code(200);
                echo json_encode($status);
            } else {
                http_response_code(200);
                echo json_encode(['status' => 'inactive']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
            error_log('Exception in getUserPaymentStatusAction: ' . $e->getMessage());
        }
    }

    /**
     * Processes the payment of the annual fee
     */
    public function processAnnualFeePayment() {
        if (!isset($_SESSION)) {
            session_start();
        }
    
        try {
            $json_input = file_get_contents('php://input');
            $data = json_decode($json_input, true);
    
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Failed to decode JSON: ' . json_last_error_msg());
            }
    
            $orderId = $data['orderID'];
            $userPaid = $data['userPaid'];
    
            $userData = $_SESSION['user_data'];
            $username = $userData['username'];
            $firstName = $userData['first_name'];
            $lastName = $userData['last_name'];
            $email = $userData['email'];
            $fullName = $firstName . ' ' . $lastName;
    
            if ($this->annualFeeModel->getAnnualFee() != $userPaid) {
                http_response_code(400);
                echo json_encode(['error' => 'Price mismatch']);
                return;
            }
    
            $result = $this->annualFeeModel->processAnnualFee($username, $orderId);
    
            if ($result) {
                // Sending the email notification about successful payment
                $mailer = new Mailer();
                $subject = "Annual Access Fee Payment Received";
    
                $message_content = "
                    Dear {$fullName},<br><br>
                    Thank you for your annual access fee payment.<br><br>
                    Amount Paid: ${userPaid}<br>
                    Order ID: {$orderId}<br><br>
                    Should you have any questions, please don't hesitate to contact our support team.<br><br>
                    Best regards,<br>
                    My Language Tutor Team
                ";
    
                $body = $this->getEmailContentWithTemplate($subject, $message_content);
                $altBody = strip_tags($message_content);
    
                $mailer->sendEmail($email, $fullName, $subject, $body, $altBody);
    
                http_response_code(200);
                echo json_encode(['message' => 'Annual fee payment successful', 'orderId' => $orderId]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Unable to process annual fee payment']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
            error_log('Exception in processAnnualFeePayment: ' . $e->getMessage());
        }
    }
    

    /**
     * Fetches the details of the plan
     */
    public function fetchPlanDetails() {
        $username = $_SESSION['username'];
    
        $activePlanName = $this->planModel->getActivePlanName($username);
        $numberOfClasses = $this->planModel->getNumberOfClasses($username);
    
        $_SESSION['planName'] = $activePlanName ?? 'No active plan';
        
        if ($numberOfClasses) {
            [$classesUsed, $totalClasses] = explode('/', $numberOfClasses);
            $_SESSION['classesUsed'] = $classesUsed;
            $_SESSION['numberOfClasses'] = $totalClasses;
        } else {
            $_SESSION['classesUsed'] = 0;
            $_SESSION['numberOfClasses'] = 0;
        }
    }
}

// Create a new instance of AnnualFeeController
$controller = new AnnualFeeController();

// Check the request method and perform the appropriate action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_GET['action']) && $_GET['action'] == 'status') {
        $controller->getUserPaymentStatusAction();
    } else {
        $controller->processAnnualFeePayment();
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Invalid request method']);
}
