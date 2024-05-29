<?php

require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/WithdrawalRequest.php';
require_once __DIR__ . '/../models/Plan.php';

require_once __DIR__ . '/../models/Mailer.php';
// require_once __DIR__ . '/../models/User.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Balog\MyLanguageTutor\Models\Mailer;

require_once __DIR__ . '/../../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class LessonController
{
    private $lessonModel;
    private $withdrawalRequestModel;

    public function __construct() 
    {
        $this->lessonModel = new Lesson();
        $this->withdrawalRequestModel = new WithdrawalRequest();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
            $this->handlePostRequest();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->handleGetRequest();
        }
    }

    private function handlePostRequest()
    {
        switch ($_POST['action']) {
            case 'submit_for_review':
                $this->submitLessonForReview($_POST['lesson_id']);
                break;

            case 'approve':
                $this->approveLesson($_POST['lesson_id']);
                break;

            case 'review':
                $starRating = $_POST['star_rating'];
                $review = $_POST['review'];
                $this->reviewLesson($_POST['lesson_id'], $starRating, $review);
                break;

            case 'saveWithdrawalEmail':
                $username = $_POST['username'];
                $paymentEmail = $_POST['paymentEmail'];
                $this->saveWithdrawalEmail($username, $paymentEmail);
                break;

            case 'withdraw':
                $this->createWithdrawal();
                break;

            case 'handleRequestExtension':
                $bookingId = $_POST['booking_id'];
                $newDateTime = $_POST['new_date_time_request'];
                $reason = $_POST['new_date_time_request_reason'];
                $this->handleRequestExtension($bookingId, $newDateTime, $reason);
                break;

            case 'approve_request':
                $bookingId = $_POST['booking_id'];
                $newDateTime = $_POST['new_date_time_request'];
                $username = $_SESSION['username'];
                $this->handleApproveRequest($bookingId, $newDateTime, $username);
                break;

            case 'decline_request':
                $bookingId = $_POST['booking_id'];
                $username = $_SESSION['username'];
                $this->handleDeclineRequest($bookingId, $username);
                break;
            case 'cancel_class':
                $this->cancelClass($_POST['lesson_id']);
                break;
        }
    }

    private function handleGetRequest()
    {
        if (isset($_GET['action']) && $_GET['action'] == 'approve') {
            $this->approveLesson($_GET['lesson_id']);
        } elseif (isset($_GET['booking_id'])) {
            if ($_SESSION['role'] == 'Tutor') {
                $this->displayLessonActivities();
            } elseif ($_SESSION['role'] == 'Student') {
                $this->displayStudentLessonActivities();
            }
        }
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


    public function displayTutorBookedLessons()
    {
        $username = $_SESSION['username'];
        $lessonsPerPage = 15;
        $pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($pageNumber - 1) * $lessonsPerPage;

        $bookedLessons = $this->lessonModel->getBookedLessonsByTutor($username, $offset, $lessonsPerPage);

        // Update each booking with its latest request status and requestor's username
        foreach ($bookedLessons as &$booking) {
            $latestRequest = $this->lessonModel->getLatestRequestStatus($booking['id']);
            $booking['latest_request_status'] = $latestRequest['status'] ?? null;
            $booking['requested_by'] = $latestRequest['requested_by'] ?? null;
        }

        $GLOBALS['totalLessons'] = $this->lessonModel->getBookedLessonsCount($username);
        $GLOBALS['totalPages'] = ceil($GLOBALS['totalLessons'] / $lessonsPerPage);
        $GLOBALS['currentPage'] = $pageNumber;

        $GLOBALS['offset'] = $offset;
        $GLOBALS['lessonsPerPage'] = $lessonsPerPage;

        if (count($bookedLessons) > 0) {
            $GLOBALS['zoomLink'] = $bookedLessons[0]['zoom_link'];
            $GLOBALS['studentName'] = $bookedLessons[0]['username'];
            $GLOBALS['nextLessonDateTime'] = $bookedLessons[0]['class_date_time'];
        }

        $GLOBALS['bookedLessons'] = $bookedLessons;

        require_once __DIR__ . '/../views/tutor/my-lessons.php';
    }

    public function displayTutorBookedLessonsForSupport()
    {
        $username = $_SESSION['username'];
        $lessonsPerPage = 15;
        $pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($pageNumber - 1) * $lessonsPerPage;

        $bookedLessons = $this->lessonModel->getBookedLessonsByTutor($username, $offset, $lessonsPerPage);

        // Update each booking with its latest request status and requestor's username
        foreach ($bookedLessons as &$booking) {
            $latestRequest = $this->lessonModel->getLatestRequestStatus($booking['id']);
            $booking['latest_request_status'] = $latestRequest['status'] ?? null;
            $booking['requested_by'] = $latestRequest['requested_by'] ?? null;
        }

        $GLOBALS['totalLessons'] = $this->lessonModel->getBookedLessonsCount($username);
        $GLOBALS['totalPages'] = ceil($GLOBALS['totalLessons'] / $lessonsPerPage);
        $GLOBALS['currentPage'] = $pageNumber;

        $GLOBALS['offset'] = $offset;
        $GLOBALS['lessonsPerPage'] = $lessonsPerPage;

        if (count($bookedLessons) > 0) {
            $GLOBALS['zoomLink'] = $bookedLessons[0]['zoom_link'];
            $GLOBALS['studentName'] = $bookedLessons[0]['username'];
            $GLOBALS['nextLessonDateTime'] = $bookedLessons[0]['class_date_time'];
        }

        $GLOBALS['bookedLessons'] = $bookedLessons;

        require_once __DIR__ . '/../views/tutor/help-support.php';
    }



    public function submitLessonForReview($lessonId)
    {
        $this->lessonModel->updateLessonStatus($lessonId, "SUBMITTED");
        header("Location: ../../../../../my-language-tutor/src/views/tutor/my-lessons.php");
        exit();
    }

    public function displayTutorLessonHistory()
    {
        $username = $_SESSION['username'];
        $lessonsPerPage = 15;
        $pageNumber = isset($_GET['history_page']) ? $_GET['history_page'] : 1;
        $offset = ($pageNumber - 1) * $lessonsPerPage;

        $lessonHistory = $this->lessonModel->getLessonHistory($username, $offset, $lessonsPerPage);
        $GLOBALS['totalHistoryLessons'] = $this->lessonModel->getLessonHistoryCount($username);
        $GLOBALS['totalHistoryPages'] = ceil($GLOBALS['totalHistoryLessons'] / $lessonsPerPage);
        $GLOBALS['currentHistoryPage'] = $pageNumber;

        $GLOBALS['lessonHistory'] = $lessonHistory;
    }

    public function displayStudentBookings()
    {
        $username = $_SESSION['username'];
        $lessonsPerPage = 15;
        $GLOBALS['lessonsPerPage'] = $lessonsPerPage;
        $pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($pageNumber - 1) * $lessonsPerPage;

        $bookings = $this->lessonModel->getStudentBookings($username, $offset, $lessonsPerPage);

        foreach ($bookings as $key => $booking) {
            $latestRequest = $this->lessonModel->getLatestRequestStatus($booking['id']);
            $bookings[$key]['latest_request_status'] = $latestRequest['status'] ?? null;
            $bookings[$key]['requested_by'] = $latestRequest['requested_by'] ?? null;
        }

        $GLOBALS['totalBookings'] = $this->lessonModel->getStudentBookingsCount($username);
        $GLOBALS['totalPages'] = ceil($GLOBALS['totalBookings'] / $lessonsPerPage);
        $GLOBALS['currentPage'] = $pageNumber;

        $GLOBALS['bookings'] = $bookings;

        require_once __DIR__ . '/../views/student/my-lessons.php';
    }

    public function displayStudentBookingsForSupport()
    {
        $username = $_SESSION['username'];
        $lessonsPerPage = 15;
        $GLOBALS['lessonsPerPage'] = $lessonsPerPage;
        $pageNumber = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($pageNumber - 1) * $lessonsPerPage;

        $bookings = $this->lessonModel->getStudentBookings($username, $offset, $lessonsPerPage);

        foreach ($bookings as $key => $booking) {
            $latestRequest = $this->lessonModel->getLatestRequestStatus($booking['id']);
            $bookings[$key]['latest_request_status'] = $latestRequest['status'] ?? null;
            $bookings[$key]['requested_by'] = $latestRequest['requested_by'] ?? null;
        }

        $GLOBALS['totalBookings'] = $this->lessonModel->getStudentBookingsCount($username);
        $GLOBALS['totalPages'] = ceil($GLOBALS['totalBookings'] / $lessonsPerPage);
        $GLOBALS['currentPage'] = $pageNumber;

        $GLOBALS['bookings'] = $bookings;

        require_once __DIR__ . '/../views/student/help-support.php';
    }

    public function displayStudentLessonHistory()
    {
        $username = $_SESSION['username'];
        $lessonsPerPage = 15;
        $pageNumber = isset($_GET['history_page']) ? $_GET['history_page'] : 1;
        $offset = ($pageNumber - 1) * $lessonsPerPage;

        $lessonHistory = $this->lessonModel->getStudentLessonHistory($username, $offset, $lessonsPerPage);
        $GLOBALS['totalHistoryLessons'] = $this->lessonModel->getStudentLessonHistoryCount($username);
        $GLOBALS['totalHistoryPages'] = ceil($GLOBALS['totalHistoryLessons'] / $lessonsPerPage);
        $GLOBALS['currentHistoryPage'] = $pageNumber;

        $GLOBALS['lessonHistory'] = $lessonHistory;
    }

    public function approveLesson($lesson_id)
    {
        $this->lessonModel->updateLessonStatus($lesson_id, "COMPLETED");

        // Save earning data
        $earningDate = date("Y-m-d"); 
        $activity = "Earning";
        $studentUsername = $_SESSION['username']; 
        $tutorUsername = $_GET['tutor_username']; 
        $amount = 21; // This can be adjusted as needed

        $this->lessonModel->saveEarnings($tutorUsername, $amount);

        $this->lessonModel->saveEarningData($earningDate, $activity, $studentUsername, $tutorUsername, $amount);
        
        header("Location: ../../../../../my-language-tutor/src/views/student/my-lessons.php");
        exit();
    }

    public function reviewLesson($lessonId, $starRating, $review)
    {
        $this->lessonModel->updateLessonReview($lessonId, $starRating, $review);
        header("Location: ../../../../../my-language-tutor/src/views/student/my-lessons.php");
        exit();
    }

    public function handleRequestExtension()
    {
        echo "Entered handleRequestExtension";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $bookingId = $_POST['booking_id'];
            $newDateTimeRequest = $_POST['new_date_time_request'];
            $newDateTimeRequestReason = $_POST['new_date_time_request_reason'];

            // Get the username and role from the session
            $username = $_SESSION['username'];
            $userRole = $_SESSION['role'];

            // Check if there's an active pending request for this booking
            $pendingRequest = $this->lessonModel->checkPendingRequest($bookingId);
            
            if ($pendingRequest) {
                $_SESSION['error_message'] = "There's already a pending request for this booking.";
            } else {
                // No active requests, so proceed with saving the new request
                $result = $this->lessonModel->saveExtensionRequest($bookingId, $newDateTimeRequest, $newDateTimeRequestReason, $username);

                if ($result) {
                    // Send the extension request email to the other party
                    $this->sendExtensionRequestEmail($bookingId, $newDateTimeRequest, $newDateTimeRequestReason, $username, $userRole);
                    $_SESSION['message'] = "Extension request sent successfully!";
                } else {
                    $_SESSION['error_message'] = "Failed to send extension request.";
                }
            }
        }

        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            // Fallback to default redirection based on the user role
            if ($userRole === 'Tutor') {
                header("Location: ../../../../../my-language-tutor/src/views/tutor/my-lessons.php");
            } else {
                header("Location: ../../../../../my-language-tutor/src/views/student/my-lessons.php");
            }
        }
        exit;
    }

    private function sendExtensionRequestEmail($bookingId, $newDateTimeRequest, $newDateTimeRequestReason, $username, $userRole) {
        $mailer = new Mailer();
    
        // Identify who to send the email to based on the role of the person making the request
        if ($userRole == 'Tutor') {
            $recipientEmail = $this->lessonModel->getStudentEmailByBookingId($bookingId);
            $recipientName = "Student";
            $requester = "Your Tutor {$username}";
        } else {
            $recipientEmail = $this->lessonModel->getTutorEmailByBookingId($bookingId);
            $recipientName = "Tutor";
            $requester = "Your Student {$username}";
        }
    
        if ($recipientEmail) {
            $subject = "Lesson Extension Request from {$requester}";
            $message_content = "
                <div style='font-family: Arial, sans-serif;'>
                    <p>Dear {$recipientName},</p>
                    <p>{$requester} has requested an extension your upcoming class. Here are the details:</p>
                    <ul>
                        <li><strong>Requested Date & Time:</strong> {$newDateTimeRequest}</li>
                        <li><strong>Reason for Extension:</strong> {$newDateTimeRequestReason}</li>
                    </ul>
                    <p>Please review this request and take the necessary actions. Reach out to the requester or our support team for any clarification.</p>
                    <p>Best regards,<br>My Language Tutor Team</p>
                </div>
            ";
    
            $body = $this->getEmailContentWithTemplate($subject, $message_content);
            $altBody = strip_tags($message_content);
    
            $mailer->sendEmail($recipientEmail, $recipientName, $subject, $body, $altBody);
        }
    }
    

    public function displayLessonActivities() {
        // Check if the booking_id is set in the URL
        if(isset($_GET['booking_id'])) {
            $bookingId = $_GET['booking_id'];
            
            // Fetch the booking info
            $bookingInfo = $this->lessonModel->getBookingInfo($bookingId);
            if($bookingInfo) {
                $GLOBALS['bookingInfo'] = $bookingInfo;
            } else {
                // You can set an error message or handle it differently if booking info isn't found
                $_SESSION['error_message'] = "Booking not found!";
            }
            
            // Fetch all requested changes
            $requestedChanges = $this->lessonModel->getAllRequestedChanges($bookingId);
            $GLOBALS['requestedChanges'] = $requestedChanges;
    
            // Load the lesson-activities.php view to display the data
            require_once __DIR__ . '/../views/tutor/lesson-activities.php';
        } else {
            // Handle the error (you can redirect the user or display an error message)
            echo "Error: booking_id not set.";
            // Or redirect back to another page, for instance:
            // header("Location: error_page.php");
            // exit();
        }
    }

    public function displayStudentLessonActivities() {
        // Check if the booking_id is set in the URL
        if(isset($_GET['booking_id'])) {
            $bookingId = $_GET['booking_id'];
            
            // Fetch the booking info
            $bookingInfo = $this->lessonModel->getBookingInfo($bookingId); // Change the model function to fetch student-specific data
            if($bookingInfo) {
                $GLOBALS['bookingInfo'] = $bookingInfo;
            } else {
                $_SESSION['error_message'] = "Booking not found!";
            }
            
            // Fetch all student's requested changes
            $requestedChanges = $this->lessonModel->getAllRequestedChanges($bookingId); // Adjust the function to fetch student-specific data
            $GLOBALS['requestedChanges'] = $requestedChanges;

            // Load the student-lesson-activities.php view to display the data
            require_once __DIR__ . '/../views/student/lesson-activities.php';
        } else {
            echo "Error: booking_id not set.";
            // Or redirect back to another page
            // header("Location: error_page.php");
            // exit();
        }
    }


    public function handleApproveRequest($bookingId, $newDateTimeRequest, $username) {
        // Approve the request and then record the decision
        $this->lessonModel->approveRequest($bookingId, $newDateTimeRequest);
        $this->lessonModel->recordRequestDecision($bookingId, 'approved', $username);

        $this->sendApprovalNotification($bookingId, $newDateTimeRequest, $username);
    
        // Redirect to the same page after the operation with the booking_id from the session
        header("Location: " . $_SERVER['PHP_SELF'] . "?booking_id=" . $_SESSION['current_booking_id']);
        exit();  // Ensure that no further code is executed after the redirect
    }

    private function sendApprovalNotification($bookingId, $newDateTimeRequest, $username) {
        $mailer = new Mailer();
        $requesterEmail = $this->lessonModel->getRequesterEmailByBookingId($bookingId);
        $subject = "Extension Request Approved";
        $message_content = "
            <div style='font-family: Arial, sans-serif;'>
                <p>Dear {$username},</p>
                <p>Your extension request for Booking ID: {$bookingId} has been approved.</p>
                <p>New Date & Time: {$newDateTimeRequest}.</p>
                <p>Best regards,<br>My Language Tutor Team</p>
            </div>
        ";
        
        $body = $this->getEmailContentWithTemplate($subject, $message_content);
        $altBody = strip_tags($message_content);
        $mailer->sendEmail($requesterEmail, $username, $subject, $body, $altBody);
    }    
    
    public function handleDeclineRequest($bookingId, $username) {
        $this->lessonModel->recordRequestDecision($bookingId, 'declined', $username);
        
        $this->sendDeclineNotification($bookingId, $username);
        // Redirect to the same page after the operation with the booking_id from the session
        header("Location: " . $_SERVER['PHP_SELF'] . "?booking_id=" . $_SESSION['current_booking_id']);
        exit();  // Ensure that no further code is executed after the redirect
    }

    private function sendDeclineNotification($bookingId, $username) {
        $mailer = new Mailer();
        $requesterEmail = $this->lessonModel->getRequesterEmailByBookingId($bookingId);
        $subject = "Extension Request Declined";
        $message_content = "
            <div style='font-family: Arial, sans-serif;'>
                <p>Your extension request for Booking ID: {$bookingId} has been declined.</p>
                <p>Best regards,<br>My Language Tutor Team</p>
            </div>
        ";
        
        $body = $this->getEmailContentWithTemplate($subject, $message_content);
        $altBody = strip_tags($message_content);
        $mailer->sendEmail($requesterEmail, $username, $subject, $body, $altBody);
    }
    

    // Cancel Class

    public function cancelClass($lessonId)
    {
        $this->lessonModel->updateLessonStatus($lessonId, "CANCELLED");
        
        // Fetch the user role and username from the session
        $userRole = $_SESSION['role'];
        $username = $_SESSION['username'];
        
        // If the user role is 'Student', adjust the classes_used and number_of_classes
        if ($userRole === 'Student') {
            $planModel = new Plan();
            $planModel->adjustClassesAfterCancellationByUsername($username);
        }

        $this->sendCancellationNotification($lessonId, $username);
        
        // Redirect based on user role
        if ($userRole === 'Tutor') {
            header("Location: ../../../../../my-language-tutor/src/views/tutor/my-lessons.php");
        } else if ($userRole === 'Student') {
            header("Location: ../../../../../my-language-tutor/src/views/student/my-lessons.php");
        } else {
            // Optional: Handle cases where the role isn't 'Tutor' or 'Student' (if any)
            header("Location: path_to_some_default_page");
        }
        exit();
    }

    private function sendCancellationNotification($lessonId, $username) {
        $mailer = new Mailer();
        list($studentEmail, $tutorEmail) = $this->lessonModel->getEmailsByLessonId($lessonId);
        $subject = "Class Cancelled";
        $message_content = "
            <div style='font-family: Arial, sans-serif;'>
                <p>Your class with lesson ID: {$lessonId} has been cancelled.</p>
                <p>Best regards,<br>My Language Tutor Team</p>
            </div>
        ";
        
        $body = $this->getEmailContentWithTemplate($subject, $message_content);
        $altBody = strip_tags($message_content);
    
        // Notify the student
        $mailer->sendEmail($studentEmail, "Student", $subject, $body, $altBody);
    
        // Notify the tutor
        $mailer->sendEmail($tutorEmail, "Tutor", $subject, $body, $altBody);
    }
    

    // Earning

    public function showEarnings() {
        // Get the tutor username from session
        $tutorUsername = $_SESSION['username'];
    
        // Get the earning and withdrawal data
        $earningData = $this->lessonModel->getEarningDataByTutor($tutorUsername);
        $withdrawalData = $this->lessonModel->getWithdrawalDataByTutor($tutorUsername);
    
        // Merge the data
        $combinedData = array_merge($earningData, $withdrawalData);
    
        // Sort data by date (you might want to use a custom function for this)
        usort($combinedData, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });        
    
        // Set global variables for the view
        $GLOBALS['earningData'] = $combinedData;
    
        // Include the view
        require_once __DIR__ . '/../views/tutor/earnings-payment.php';
    }
    

    public function getTutorEarnings($tutorUsername)
    {
        $earnings = $this->lessonModel->getEarningsByUsername($tutorUsername);
        return $earnings;
    }

    public function saveWithdrawalEmail() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['paymentEmail'])) {
            $username = $_SESSION['username'];
            $email = $_POST['paymentEmail'];
            
            // Use the lessonModel's method to save the email
            $result = $this->lessonModel->saveWithdrawalEmail($username, $email);
    
            if ($result) {
                $_SESSION['message'] = "Email successfully saved!";
                header("Location: ../../src/views/tutor/earnings-payment.php");
                exit();
            } else {
                $_SESSION['error'] = "There was a problem saving the email. Please try again.";
                header("Location: ../../src/views/tutor/earnings-payment.php");
                exit();
            }
        }
    }
    
    
    public function getWithdrawalEmailController() {
        $username = $_SESSION['username'];
        $email = $this->lessonModel->getWithdrawalEmail($username);
    
        // Save email in session if it's not null
        if($email) {
            $_SESSION['withdrawal_email'] = $email;
        }
    
        return $email;
    }

    // Withdrawal request

    public function createWithdrawal()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_SESSION['username'];
        $requestedAmount = $_POST['availableEarnings'];
        $paypalEmail = $_POST['paypalEmail'];

        $result = $this->withdrawalRequestModel->createWithdrawalRequest($username, $requestedAmount, $paypalEmail);
        
        if ($result) {
            $deductResult = $this->withdrawalRequestModel->deductEarnings($username, $requestedAmount);

            if ($deductResult) {
                // Now, let's send the payout.
                $payoutResult = $this->withdrawalRequestModel->sendPayout($paypalEmail, $requestedAmount);

                if ($payoutResult) {
                    $_SESSION['message'] = "Withdrawal request successfully created and money sent!";
                    // Send email notification to the tutor
                    $this->sendWithdrawalEmail($username, $paypalEmail, $requestedAmount);
                    header("Location: ../../src/views/tutor/earnings-payment.php");
                    exit();
                } else {
                    $this->logError("Error in sending payout for user: $username, Amount: $requestedAmount");
                    $_SESSION['error'] = "Money could not be sent to PayPal. Please contact support.";
                    header("Location: ../../src/views/tutor/earnings-payment.php");
                    exit();
                }
            } else {
                $this->logError("Error in deducting earnings for user: $username, Amount: $requestedAmount");
                $_SESSION['error'] = "Withdrawal request was created, but there was an error updating your available earnings.";
                header("Location: ../../src/views/tutor/earnings-payment.php");
                exit();
            }
        } else {
            $this->logError("Error in creating withdrawal request for user: $username, Amount: $requestedAmount");
            $_SESSION['error'] = "There was a problem creating the withdrawal request. Please try again.";
            header("Location: ../../src/views/tutor/earnings-payment.php");
            exit();
        }
    }
}

private function sendWithdrawalEmail($username, $paypalEmail, $amount) {
    $mailer = new Mailer(); 
    
    // Retrieve the tutor's email address directly from the session
    $tutorEmail = $_SESSION['user_data']['email']; 

    $subject = "Withdrawal Request Processed";

    // Using the template for professional communication
    $message_content = "
        <div style='font-family: Arial, sans-serif;'>
            <h2>Dear {$username},</h2>
            <p>We are pleased to inform you that your withdrawal request for <strong>$" . number_format($amount, 2) . "</strong> has been processed successfully.</p>
            <p>The amount has been sent to your PayPal account associated with the email: <strong>{$paypalEmail}</strong>.</p>
            <p>If you have any concerns or did not receive the amount, please contact our support team.</p>
            <br>
            <p>Best Regards,</p>
            <p>My Language Tutor Team</p>
        </div>
    ";

    $mailer->sendEmail($tutorEmail, $username, $subject, $message_content, strip_tags($message_content));
}



private function logError($message) 
{
    error_log($message);
}



    
    

}

$lessonController = new LessonController();

if ($_SESSION['role'] == 'tutor') {
    $lessonController->displayTutorBookedLessons();
    $lessonController->displayTutorBookedLessonsForSupport();
    $lessonController->displayTutorLessonHistory();
    $lessonController->showEarnings();
    $lessonController->displayResolutionPage();
} elseif ($_SESSION['role'] == 'student') {
    $lessonController->displayStudentBookings();
    $lessonController->displayStudentLessonHistory();
    $lessonController->displayStudentBookingsForSupport();
}


?>
