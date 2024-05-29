<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/adminUsers.php';
require_once __DIR__ . '/../models/Mailer.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Balog\MyLanguageTutor\Models\Mailer;

require_once __DIR__ . '/../../vendor/autoload.php';

// Create a new instance of the AdminUsers class
$adminUsers = new AdminUsers();

if (isset($_GET['action']) && $_GET['action'] === 'getTutorAvailability') {
    getTutorAvailabilityByUsername();
}
// You can add other action checks for other functions as needed.


// Fetch all the users including  their latest subscription
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 25;  // Display 25 results per page
// Get the search and sort criteria from the GET request
$role_filter = isset($_GET['role_filter']) ? $_GET['role_filter'] : '';
$subscription_filter = isset($_GET['subscription_filter']) ? $_GET['subscription_filter'] : '';
$search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';

// Fetch the users based on the criteria
$users = $adminUsers->getAllUsersWithLatestSubscription($page, $limit, $role_filter, $subscription_filter, $search_term);

$totalUsers = $adminUsers->getTotalUsersCount();

// Fetch all the tutors
$tutorsPage = isset($_GET['tutors_page']) ? (int)$_GET['tutors_page'] : 1;
$tutorsLimit = 25;  // Display 25 results per page

// Get the search criteria from the GET request
$tutorsSearchTerm = isset($_GET['tutors_search_term']) ? $_GET['tutors_search_term'] : '';

// Fetch the tutors based on the criteria
$allTutors = $adminUsers->getAllTutors($tutorsPage, $tutorsLimit, $tutorsSearchTerm);

$totalTutorsCount = $adminUsers->getTotalTutorsCount();


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

function getTutorAvailabilityByUsername() {
    header('Content-Type: application/json');

    $username = $_GET['username'];
    error_log("Controller: Getting availability for username: " . $username);
    
    try {
        $adminUsers = new AdminUsers();
        $availability = $adminUsers->getAvailabilityByUsername($username);

        // Comment out or remove the var_dump
        // var_dump(['status' => 'success', 'availability' => $availability]);

        echo json_encode(['status' => 'success', 'availability' => $availability]);
    } catch (Exception $e) {
        http_response_code(500);
        error_log("Controller: Exception - " . $e->getMessage());

        // Comment out or remove the var_dump
        // var_dump(['status' => 'error', 'message' => $e->getMessage()]);

        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}






// If we're viewing a profile, fetch additional details for that user
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    
    // Fetching additional details for the user
    $activePlan = $adminUsers->getActivePlanByUsername($username);
    $totalClasses = $adminUsers->getCompletedClassesCountByUsername($username);
    $annualAccessFee = $adminUsers->getLatestAnnualAccessFeeExpireDateByUsername($username);
}

// Tutor profile
if (isset($_POST['update_tutor'], $_POST['tutor_status'], $_POST['email'])) {
    
    $tutor_status = $_POST['tutor_status'];
    $email = $_POST['email'];

    if ($tutor_status === 'unapprove') {
        $adminUsers->updateTutorAccountStatus($email, 2);
        
        // Sending email notification about the unapproved status
        $mailer = new Mailer();
        $subject = "Tutor Profile Status Update";
        $message_content = "
            Dear Tutor,<br><br>
            Unfortunately, your profile has been unapproved. If you think this is an error, please contact our support team.<br><br>
            Best regards,<br>
            My Language Tutor Team
        ";
    } else if ($tutor_status === 'approve') {
        $adminUsers->updateTutorAccountStatus($email, 1);
        
        // Sending email notification about the approved status
        $mailer = new Mailer();
        $subject = "Congratulations! Tutor Profile Approved";
        $message_content = "
            Dear Tutor,<br><br>
            Great news! Your tutor profile has been approved. You can now start teaching on our platform.<br><br>
            Best regards,<br>
            My Language Tutor Team
        ";
    }

    // Construct email and send
    $body = getEmailContentWithTemplate($subject, $message_content); // Direct function call without $this
    $altBody = strip_tags($message_content);
    $mailer->sendEmail($email, "Tutor", $subject, $body, $altBody);
    
    // You can set a session message here to notify of successful update
    $_SESSION['message'] = "User status updated successfully.";

    // Redirect back to the previous page or the specified fallback page
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "tutor-profile.php?email=" . urlencode($email);

    header("Location: " . $referer);
    exit();
}

// Leson Details

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 25;  // Display 25 results per page

$bookings = $adminUsers->getBookings($page, $limit);
$totalBookings = $adminUsers->getTotalBookingsCount();

$bookingsHistory = $adminUsers->getBookingsHistory($page, $limit);
$totalBookingsHistory = $adminUsers->getTotalBookingsHistoryCount();

// Tutor Lesson details
$tutor_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$tutor_limit = 25;  // Display 25 results per page
$tutor_username = isset($_GET['username']) ? $_GET['username'] : '';  

// Fetch the current lessons for the specific tutor
$tutor_lessons = $adminUsers->getTutorLessons($tutor_username, $tutor_page, $tutor_limit);
$totalTutorLessons = $adminUsers->getTotalTutorLessonsCount($tutor_username);

// Fetch the lesson history for the specific tutor
$tutor_lesson_history = $adminUsers->getTutorLessonHistory($tutor_username, $tutor_page, $tutor_limit);
$totalTutorLessonHistory = $adminUsers->getTotalTutorLessonHistoryCount($tutor_username);



// Fetch all payment entries
$payments = $adminUsers->getAllPayments($page, $limit);
$totalPayments = $adminUsers->getTotalPaymentsCount();

// Fetch all unapproved tutors
$unapprovedTutors = $adminUsers->getUnapprovedTutors();

// Fettch Ubapproved tutors with changes requested

$requireTutorChanges = $adminUsers->getUnapprovedTutorsByChangesRequire();


// Tutor profile
if (isset($_POST['update_tutor'], $_POST['tutor_status'], $_POST['email'])) {
    
    $tutor_status = $_POST['tutor_status'];
    $email = $_POST['email'];

    if ($tutor_status === 'unapprove') {
        $adminUsers->updateTutorAccountStatus($email, 0);
        
        // Sending email notification about the unapproved status
        $mailer = new Mailer();
        $subject = "Tutor Profile Status Update";
        $message_content = "
            Dear Tutor,<br><br>
            Unfortunately, your profile has been unapproved. If you think this is an error, please contact our support team.<br><br>
            Best regards,<br>
            My Language Tutor Team
        ";
    } else if ($tutor_status === 'approve') {
        $adminUsers->updateTutorAccountStatus($email, 1);
        
        // Sending email notification about the approved status
        $mailer = new Mailer();
        $subject = "Congratulations! Tutor Profile Approved";
        $message_content = "
            Dear Tutor,<br><br>
            Great news! Your tutor profile has been approved. You can now start teaching on our platform.<br><br>
            Best regards,<br>
            My Language Tutor Team
        ";
    }

    // Construct email and send
    $body = $this->getEmailContentWithTemplate($subject, $message_content);
    $altBody = strip_tags($message_content);
    $mailer->sendEmail($email, "Tutor", $subject, $body, $altBody);
    
    // You can set a session message here to notify of successful update
    $_SESSION['message'] = "User status updated successfully.";

    // Redirect back to the user profile.
    header("Location: tutor-profile.php?email=" . urlencode($email));
    exit();
}

if (isset($_POST['update_status'], $_POST['account_status'], $_POST['username'], $_POST['email'])) {
    
    $account_status = $_POST['account_status'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $mailer = new Mailer();
    $subject = "";
    $message_content = "";

    if ($account_status === 'deactivate') {
        $result = $adminUsers->updateAccountStatus($username, 1);  // 1 for deactivated
        if (!$result) {
            echo "Failed to deactivate the account.";
            exit();
        }
        // Sending email notification about the suspended status
        $subject = "Account Suspension Notification";
        $message_content = "
            Dear user,<br><br>
            We regret to inform you that your account with My Language Tutor has been temporarily suspended due to certain policy violations. If you believe this is an error or wish to discuss the matter, please contact our support team.<br><br>
            Best regards,<br>
            My Language Tutor Team
        ";
    } else if ($account_status === 'activate') {
        $result = $adminUsers->updateAccountStatus($username, 0);  // 0 for activated
        if (!$result) {
            echo "Failed to activate the account.";
            exit();
        }
        // Sending email notification about the reactivation status
        $subject = "Account Reactivation Confirmation";
        $message_content = "
            Dear user,<br><br>
            We're pleased to inform you that your account with My Language Tutor has been reactivated. You can now continue to use our services. We apologize for any inconvenience caused.<br><br>
            Best regards,<br>
            My Language Tutor Team
        ";
    }

    // Construct email and send
    $body = getEmailContentWithTemplate($subject, $message_content); 
    $altBody = strip_tags($message_content);
    $mailer->sendEmail($email, "User", $subject, $body, $altBody);
    
    // You can set a session message here to notify of successful update
    $_SESSION['message'] = "User status updated successfully.";

    // Redirect back to the user profile.
    header("Location: view-profile.php?username=" . urlencode($username));
    exit();
}





// Fetch withdrawal request
$withdrwalRewquests = $adminUsers->getWithdrawalRequest();

// Fetch Approved request
$ApprovedWithdrawals = $adminUsers->getApprovedWithdrawals();


?>
