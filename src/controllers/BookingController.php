<?php
// Require the Booking model
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Mailer.php';
require_once __DIR__ . '/../models/User.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Balog\MyLanguageTutor\Models\Mailer;

require_once __DIR__ . '/../../vendor/autoload.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * BookingController Class
 * 
 * Manages the booking operations.
 */
class BookingController {

    // Variable to hold the Booking model
    private $bookingModel;

    /**
     * Constructor
     * 
     * Initializes the Booking model.
     */
    public function __construct() {
        $this->bookingModel = new Booking();
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
     * Manage Availability
     * 
     * Handles the different booking and availability management operations.
     * Operations include updating timezone, getting current timezone, booking a slot,
     * and updating availability.
     */
    public function manageAvailability() {
        // Check if it's a POST request
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            if(isset($_POST['action'])) {
                $action = $_POST['action'];

                switch($action) {
                    case 'updateTimezone':
                        $this->updateTimezone();
                        break;
                    case 'getCurrentTimezone':
                        $this->getCurrentTimezone();
                        break;
                    case 'bookSlot':
                        $this->bookSlot();
                        break;
                    default:
                        $this->updateAvailability();
                        break;
                }
            } else {
                $this->updateAvailability();
            }
        } else if($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Content-Type: application/json');

            try {
                $tutorId = $_SESSION['user_id'];
                $availability = $this->bookingModel->getAvailability($tutorId);
                echo json_encode(['status' => 'success', 'availability' => $availability]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            require_once '../views/tutor/manage-availability.php';
        }
    }

    /**
     * Update Timezone
     * 
     * Updates the timezone of a specific tutor.
     */
    private function updateTimezone() {
        try {
            $tutorId = $_SESSION['user_id'];
            $timezone = $_POST['timezone'];
            $this->bookingModel->updateTimezone($tutorId, $timezone);
            echo json_encode(['status' => 'success', 'message' => 'Timezone updated successfully']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get Current Timezone
     * 
     * Fetches the current timezone of a specific tutor.
     */
    private function getCurrentTimezone() {
        try {
            $tutorId = $_SESSION['user_id'];
            $timezone = $this->bookingModel->getTimezone($tutorId);
            echo json_encode(['status' => 'success', 'timezone' => $timezone]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update Availability
     * 
     * Updates the availability of a specific tutor.
     */
    private function updateAvailability() {
        if (!isset($_SESSION)) {
            session_start();
        }
    
        try {
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
                throw new Exception("Required session variables not set.");
            }
    
            $tutorId = $_SESSION['user_id'];
            $username = $_SESSION['username'];
            $availability = $_POST['availability'];
    
            if (isset($_SESSION['user_data'])) {
                $userData = $_SESSION['user_data'];
            } else {
                throw new Exception("User data not found in session.");
            }
    
            $email = $userData['email'];
            $firstName = $userData['first_name'];
            $lastName = $userData['last_name'];
            $fullName = $firstName . ' ' . $lastName;
    
            $this->bookingModel->updateAvailability($tutorId, $availability, $username);
    
            if (!empty($email) && !empty($fullName)) {
                $mailer = new Mailer();
                $subject = "Availability Update Confirmation";
    
                $message_content = "
                    <div style='font-family: Arial, sans-serif;'>
                        <p>Dear {$fullName},</p>
                        <p>Your availability has been successfully updated.</p>
                        <p>To view the changes, please:</p>
                        <a href='https://mylanguagetutor.ca/src/views/tutor/manage-availability' style='padding: 12px 30px; font-size: 20px; display: inline-block; cursor: pointer; border: none; border-radius: 7px; text-align: center; background-color: #84d19f; color: #ffffff; text-decoration: none;'>Login To View Changes</a>
                        <p>If you did not make this change or if you have any questions, please contact our support team.</p>
                        <p>Best regards,<br>My Language Tutor Team</p>
                    </div>
                ";
    
                $body = $this->getEmailContentWithTemplate($subject, $message_content);
                $altBody = strip_tags($message_content);
    
                $mailer->sendEmail($email, $fullName, $subject, $body, $altBody);
            }
    
            echo json_encode(['status' => 'success', 'message' => 'Availability updated successfully']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Book Slot
     * 
     * Books a specific slot for a student with a specific tutor.
     */
    private function bookSlot() {
        if (!isset($_SESSION)) {
            session_start();
        }
    
        $username = $_SESSION['username'];
        $tutorUsername = $_POST['tutorUsername'];
        $dateTime = $_POST['dateTime'];
        $zoomLink = $_POST['meetingURL'];
        $language = $_POST['language'];
    
        // Initialize the user object
        $user = new User();
    
        // Fetch the student's details
        if (isset($_SESSION['user_data'])) {
            $userData = $_SESSION['user_data'];
        } else {
            throw new Exception("User data not found in session.");
        }
    
        // Fetch the tutor's details
        $tutor = $user->getTutorByUsername($tutorUsername);
        if ($tutor === null) {
            throw new Exception("No tutor found with username {$tutorUsername}");
        }
    
        try {
            $this->bookingModel->bookSlot($username, $tutorUsername, $dateTime, $zoomLink, $language);
            
            // Send email to student
            $studentEmail = $userData['email'];
            $studentFullName = $userData['first_name'] . ' ' . $userData['last_name'];
            $this->sendBookingConfirmationEmail($studentEmail, $studentFullName, $tutorUsername, $dateTime, $zoomLink, $language);
            
            // Send email to tutor
            $tutorEmail = $tutor['email'];
            $tutorFullName = $tutor['first_name'] . ' ' . $tutor['last_name'];
            $this->sendBookingConfirmationEmail($tutorEmail, $tutorFullName, $tutorUsername, $dateTime, $zoomLink, $language, true);
            
            echo json_encode(['status' => 'success', 'message' => 'Booking confirmed! Emails have been sent to both student and tutor.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
            return;
        }
    
        try {
            // Increase classes_used value by 1
            $this->bookingModel->incrementClassesUsed($username);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'error' => 'Booking succeeded, but there was an error incrementing classes used: ' . $e->getMessage()]);
        }
    }
    
    private function sendBookingConfirmationEmail($email, $fullName, $tutorUsername, $dateTime, $zoomLink, $language, $isTutor=false) {
        if (!empty($email) && !empty($fullName)) {
            $mailer = new Mailer();
    
            if ($isTutor) {
                $subject = "New Booking Alert";
                $message_content = "
                    <div style='font-family: Arial, sans-serif;'>
                        <p>Dear {$fullName},</p>
                        <p>You have a new booking scheduled with {$tutorUsername}. Here are the details:</p>
                        <ul>
                            <li><strong>Date & Time:</strong> {$dateTime}</li>
                            <li><strong>Language:</strong> {$language}</li>
                        </ul>
                        <p>Please ensure you're prepared and punctual for the session. In case of any issues or need to reschedule, kindly reach out to the student or our support team.</p>
                        <p>Best regards,<br>My Language Tutor Team</p>
                    </div>
                ";
            } else {
                $subject = "Booking Confirmation";
                $message_content = "
                    <div style='font-family: Arial, sans-serif;'>
                        <p>Dear {$fullName},</p>
                        <p>We're pleased to confirm your booking with {$tutorUsername}. Here are the details of your upcoming session:</p>
                        <ul>
                            <li><strong>Date & Time:</strong> {$dateTime}</li>
                            <li><strong>Language:</strong> {$language}</li>
                        </ul>
                        <p>If you encounter any issues or need to reschedule, please reach out to your tutor or contact our support team for assistance.</p>
                        <p>Best regards,<br>My Language Tutor Team</p>
                    </div>
                ";
            }
    
            $body = $this->getEmailContentWithTemplate($subject, $message_content);
            $altBody = strip_tags($message_content);
    
            $mailer->sendEmail($email, $fullName, $subject, $body, $altBody);
        }
    }
    
    
    
    
    /**
     * Get Tutor Availability By Username
     * 
     * Fetches the availability of a specific tutor.
     * 
     * @param string $username     The username of the tutor
     * 
     * @return array $availability The availability of the tutor
     */
    public function getTutorAvailabilityByUsername() {
        header('Content-Type: application/json');
        try {
            $username = $_GET['username'];
            $availability = $this->bookingModel->getAvailabilityByUsername($username);
            echo json_encode(['status' => 'success', 'availability' => $availability]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get Tutor Timezone By Username
     * 
     * Fetches the timezone of a specific tutor.
     * 
     * @param string $username     The username of the tutor
     * 
     * @return string $timezone    The timezone of the tutor
     */
    public function getTutorTimezoneByUsername() {
        header('Content-Type: application/json');
        try {
            $username = $_GET['username'];
            $timezone = $this->bookingModel->getTutorTimezoneByUsername($username);
            echo json_encode(['status' => 'success', 'timezone' => $timezone]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get Booked Slots
     * 
     * Fetches the booked slots of a specific tutor.
     * 
     * @param string $username     The username of the tutor
     * 
     * @return array $bookedSlots  The booked slots of the tutor
     */
    public function getBookedSlots() {
        header('Content-Type: application/json');
        try {
            $tutor_username = $_GET['tutor_username'];
            $bookedSlots = $this->bookingModel->getBookedSlots($tutor_username);
            echo json_encode(['status' => 'success', 'bookedSlots' => $bookedSlots]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

// Get the BookingController action from the GET request
$action = $_GET['action'] ?? null;

// Initialize the BookingController
$bookingController = new BookingController();

// Determine the action to take based on the provided action
if ($action === 'getTutorTimezoneByUsername') {
    $bookingController->getTutorTimezoneByUsername();
} elseif ($action === 'getTutorAvailabilityByUsername') {
    $bookingController->getTutorAvailabilityByUsername();
} elseif ($action === 'getBookedSlots') {
    $bookingController->getBookedSlots();
} else {
    // Default to managing availability if no specific action is provided
    $bookingController->manageAvailability();
}

?>
