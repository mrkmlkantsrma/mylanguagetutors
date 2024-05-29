<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/groupClass.php';
require_once __DIR__ . '/../models/Mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Balog\MyLanguageTutor\Models\Mailer;

require_once __DIR__ . '/../../vendor/autoload.php';
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

class GroupClassController {

    private $groupClassModel;

    public function __construct() {
        $this->groupClassModel = new GroupClass();
    }

    public function getEmailContentWithTemplate($subject, $messageContent) {
        $template = file_get_contents(__DIR__ . '/general_email_template.html');
        
        // Ensure the template has been successfully loaded.
        if ($template === false) {
            throw new Exception("Unable to load email template.");
        }
    
        // Replace the placeholders with actual content
        $template = str_replace('{subject}', $subject, $template);
        $template = str_replace('{message_content}', $messageContent, $template);
        $template = str_replace('{current_year}', date('Y'), $template);
    
        return $template;
    }

    public function create() {
        // Handle file upload for cover image
        $target_dir = "../../uploads/groupClasses/";
    
        // Check if directory exists, if not, create it
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                echo "Error: Failed to create directory.";
                exit;
            }
        }
    
        // Check if file is set and there are no upload errors
        if (!isset($_FILES["class_cover_image"])) {
            echo "Error: No file was uploaded.";
            exit;
        }
    
        if ($_FILES["class_cover_image"]["error"] != 0) {
            echo "Error: There was an error uploading the file. Error code: " . $_FILES["class_cover_image"]["error"];
            exit;
        }
    
        $target_file = $target_dir . basename($_FILES["class_cover_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
        // Check if file is an actual image
        $check = getimagesize($_FILES["class_cover_image"]["tmp_name"]);
        if ($check === false) {
            echo "Error: File is not an image.";
            exit;
        }
    
        // Check file size
        if ($_FILES["class_cover_image"]["size"] > 5000000) { // 5MB
            echo "Error: Your file is too large. It should be less than 5MB.";
            exit;
        }
    
        // Allow only JPG, JPEG, PNG files
        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
            echo "Error: Only JPG, JPEG, & PNG files are allowed.";
            exit;
        }
    
        // Attempt to move the uploaded file
        if (!move_uploaded_file($_FILES["class_cover_image"]["tmp_name"], $target_file)) {
            echo "Error: There was an issue moving the uploaded file to the target directory.";
            exit;
        }
    
        $cover_image_path = basename($_FILES["class_cover_image"]["name"]);
    
        // Retrieve tutor email from the hidden input field in the form
        $tutorEmail = isset($_POST['tutor_email']) ? $_POST['tutor_email'] : null;
    
        // Check if the tutor email was provided
        if (!$tutorEmail) {
            echo "Error: Tutor email not provided.";
            exit;
        }
    
        // Attempt to insert into database
        try {
            if ($this->groupClassModel->addClass($_POST['title'], $_POST['description'], $_POST['first_day'], $_POST['first_time'], $_POST['second_day'], $_POST['second_time'], $_POST['number_of_classes'], $_POST['duration'], $_POST['tutor'], $_POST['pricing'], $cover_image_path)) {
        
                // Prepare email content
                $subject = "New Group Class Assignment Notification";
                $messageContent = <<<HTML
                    <h1>New Class Assignment: {$_POST['title']}</h1>
                    <p>Dear tutor,</p>
                    <p>We are pleased to inform you that you have been assigned as the tutor for a new group class. Please find the details of the class below:</p>
                    <ul>
                        <li><strong>Title:</strong> {$_POST['title']}</li>
                        <li><strong>Description:</strong> {$_POST['description']}</li>
                        <li><strong>First Session Day:</strong> {$_POST['first_day']}</li>
                        <li><strong>First Session Time:</strong> {$_POST['first_time']}</li>
                        <li><strong>Second Session Day:</strong> {$_POST['second_day']}</li>
                        <li><strong>Second Session Time:</strong> {$_POST['second_time']}</li>
                        <li><strong>Number of Sessions:</strong> {$_POST['number_of_classes']}</li>
                        <li><strong>Duration of Each Session:</strong> {$_POST['duration']}</li>
                    </ul>
                    <p>Please confirm your acceptance of this assignment by replying to this email.</p>
                    <p>We look forward to your valued participation and contribution to the success of this class.</p>
                HTML;
        
                $fullEmailContent = $this->getEmailContentWithTemplate($subject, $messageContent);
        
                // Initialize the mailer and send the email
                $mailer = new Mailer();
                $mailer->sendEmail($tutorEmail, $_POST['tutor'], $subject, $fullEmailContent, strip_tags($messageContent));
        
                // Store success message in session and redirect
                $_SESSION['success_msg'] = "The group class was created successfully, and the tutor has been notified via email.";
                header('Location: ../../src/views/admin/group-classes.php');
                exit; 
            } else {
                echo "Error: Failed to add the class to the database.";
                exit;
            }
        } catch (Exception $e) {
            echo "Database Error: " . $e->getMessage();
            exit;
        }
        
    }

    public function enrollInClass() {
        // Assuming you have already set up $_POST handling
        $classId = $_POST['classId'];
        $pricing = $_POST['pricing'];
        $studentId = $_SESSION['student_id']; // or however you store the student's ID

        // Validate classId and pricing here...

        // Create Stripe Checkout Session
        try {
            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'cad',
                        'product_data' => ['name' => 'Group Class Enrollment'],
                        'unit_amount' => $pricing * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://localhost:8000/src/views/student/my-lessons.php?status=success&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'https://mylanguagetutor.ca/',
                'metadata' => [
                    'classId' => $classId,
                    'studentId' => $studentId
                ],
            ]);

            echo json_encode(['checkoutSessionId' => $checkout_session->id]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo json_encode(['error' => 'Stripe API error: ' . $e->getMessage()]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function handlePaymentSuccess($sessionId) {
        try {
            // Retrieve the Stripe session
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $classId = $session->metadata['classId'];
            $studentId = $session->metadata['studentId'];
            $amountPaid = $session->amount_total; // Assuming this is in the smallest currency unit, e.g. cents

            // Instantiate your GroupClass model
            $groupClassModel = new GroupClass();

            // Use 'paid' as the status for successful payment
            $status = 'paid';

            // Process the enrollment
            $result = $groupClassModel->enrollStudent($studentId, $classId, date('Y-m-d H:i:s'), $amountPaid / 100, $status);

            return $result;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return ['error' => 'Stripe API Error: ' . $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => 'General Error: ' . $e->getMessage()];
        }
    }
        
    public function getAllGroupClasses() {
        try {
            return $this->groupClassModel->getAllGroupClasses();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    

    public function edit($class_id) {
        try {
            $class = $this->groupClassModel->getGroupClassById($class_id);
            
            if (!$class) {
                echo "Class not found!";
                return;
            }
            
            include '../../src/views/admin/edit-group-class.php';
            
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function update() {
        $target_dir = "../../uploads/groupClasses/";
    
        // Check if directory exists, if not, create it
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
    
        // Assume there's a hidden input with the current image path. If no new image is provided, this will be used.
        $cover_image_path = $_POST['current_cover_image']; 
    
        if (isset($_FILES["class_cover_image"]) && $_FILES["class_cover_image"]["error"] == 0) {
            $target_file = $target_dir . basename($_FILES["class_cover_image"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
            // Check if file is an actual image
            $check = getimagesize($_FILES["class_cover_image"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
    
            // Check file size
            if ($_FILES["class_cover_image"]["size"] > 5000000) { // 5MB
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }
    
            // Allow only JPG, JPEG, PNG files
            if($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
                echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
                $uploadOk = 0;
            }
    
            // Attempt to move the uploaded file
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["class_cover_image"]["tmp_name"], $target_file)) {
                    $cover_image_path = basename($_FILES["class_cover_image"]["name"]);
                } else {
                    echo "Sorry, there was an error uploading your file.";
                    return;
                }
            }
        }
    
        // Update the database, outside of the file check as you're updating more than just the image
        try {
            if ($this->groupClassModel->updateGroupClass($_POST['class_id'], $_POST['title'], $_POST['description'], $_POST['first_day'], $_POST['first_time'], $_POST['second_day'], $_POST['second_time'], $_POST['number_of_classes'], $_POST['duration'], $_POST['tutor'], $_POST['pricing'], $cover_image_path)) {
                $_SESSION['success_msg'] = "The group class was updated successfully.";
                header('Location: ../../src/views/admin/group-classes.php');
                exit;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function draft($class_id) {
        try {
            return $this->groupClassModel->draftClass($class_id);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
    public function delete($class_id) {
        try {
            return $this->groupClassModel->deleteClass($class_id);
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function enrollStudent($data) {
        try {
            $enrollmentResult = $this->groupClassModel->enrollStudent($data['student_id'], $data['class_id'], $data['date_enrolled'], $data['amount_paid'], $data['status']);
            if ($enrollmentResult) {
                return true;
            } else {
                // If there's a failure but no exception, this provides a generic error message
                throw new Exception('There was a problem processing the enrollment.');
            }
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function enrollStudentManual($data) {
        try {
            // Enroll the student using the model function
            $enrollmentResult = $this->groupClassModel->enrollStudent(
                $data['student_id'],
                $data['class_id'],
                $data['date_enrolled'],
                $data['amount_paid'],
                'Manual'
            );
    
            if ($enrollmentResult) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Student manually enrolled successfully']);
            } else {
                // Handle failure case
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'There was a problem with manual enrollment.']);
            }
        } catch (Exception $e) {
            // Catch any exceptions and return an error
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
    

    public function getEnrolledStudentsByClassId($class_id) {
        try {
            $students = $this->groupClassModel->getEnrolledStudentsByClassId($class_id);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'data' => $students]);
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function saveZoomMeeting($class_id, $zoom_link, $scheduled_date, $scheduled_time) {
        try {
            // Get the last meeting details
            $lastMeeting = $this->getZoomMeetingsByClassId($class_id);
    
            // Instantiate a new DateTime for the new meeting
            $newMeetingDateTime = new DateTime($scheduled_date . ' ' . $scheduled_time);
            $currentDateTime = new DateTime();
    
            // Check if there's a last meeting
            if ($lastMeeting) {
                $lastMeetingDetails = end($lastMeeting); // Get the last meeting in the array
    
                $lastMeetingDateTime = new DateTime($lastMeetingDetails['scheduled_date'] . ' ' . $lastMeetingDetails['scheduled_time']);
    
                // Check if the last meeting date and time has already passed
                if ($lastMeetingDateTime >= $currentDateTime) {
                    throw new Exception("The last meeting has not yet occurred. You cannot schedule a new meeting before the last one occurs.");
                }
            }
    
            // If there is no last meeting or it has already occurred, attempt to save the new meeting details
            $saveResult = $this->groupClassModel->addZoomMeeting($class_id, $zoom_link, $scheduled_date, $scheduled_time);
    
            if (!$saveResult) {
                throw new Exception("Unable to save Zoom meeting details.");
            }
    
            // If the details are saved successfully, attempt to send the emails
            $emailResult = $this->sendMeetingEmailToStudents($class_id, $zoom_link, $scheduled_date, $scheduled_time);
    
            if (!$emailResult) {
                // If emailResult is false, it means sending emails failed
                throw new Exception("Unable to send email to all students. No students are enrolled or an error occurred during sending.");
            }
    
            // Return associative array if both operations are successful
            return ['status' => 'success', 'message' => 'Meeting saved and emails sent successfully'];
    
        } catch (Exception $e) {
            // Return associative array in case of exception
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }    
    
    public function sendMeetingEmailToStudents($class_id, $zoom_link, $scheduled_date, $scheduled_time) {
        try {
            // Fetch all enrolled students
            $students = $this->groupClassModel->getEnrolledStudentsByClassId($class_id);
            if (empty($students)) {
                throw new Exception("No enrolled students found for this class.");
            }
            
            // Initialize the mailer
            $mailer = new Mailer();
    
            // Loop through each student and send an email
            foreach ($students as $student) {
                $subject = "New Zoom Meeting Scheduled";
                $messageContent = "Dear {$student['full_name']},<br><br>
                    A new group class meeting has been scheduled.<br>
                    Date: {$scheduled_date}<br>
                    Time: {$scheduled_time}<br>
                    Zoom Link: <a href='{$zoom_link}'>Join Meeting</a><br><br>
                    Regards,<br>
                    Your Team";
    
                // Generate the full email content using the template
                $fullEmailContent = $this->getEmailContentWithTemplate($subject, $messageContent);
    
                // Send the email using the full content with the template
                $mailer->sendEmail($student['email'], $student['full_name'], $subject, $fullEmailContent, strip_tags($messageContent));
            }
    
            return true;
        } catch (Exception $e) {
            // Handle exception (e.g., log the error and/or send an error response)
            echo $e->getMessage();
            return false;
        }
    }    
    

    public function getZoomMeetingsByClassId($class_id) {
        try {
            $meetings = $this->groupClassModel->getZoomMeetingsByClassId($class_id);
            return $meetings;
        } catch (Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function getAllStudents() {
        return $this->groupClassModel->getAllStudents();
    }
}

$controller = new GroupClassController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $controller->create();
        
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
        $controller->update();
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'draft') {
        if ($controller->draft($_POST['class_id'])) {
            $_SESSION['success_msg'] = "The class was drafted successfully.";
        } else {
            $_SESSION['error_msg'] = "Error drafting the class.";
        }
        header('Location: ../../src/views/admin/group-classes.php');
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        if ($controller->delete($_POST['class_id'])) {
            $_SESSION['success_msg'] = "The class was deleted successfully.";
        } else {
            $_SESSION['error_msg'] = "Error deleting the class.";
        }
        header('Location: ../../src/views/admin/group-classes.php');
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'enroll_student') {
        $data = [
            'student_id' => $_POST['student_id'],
            'class_id' => $_POST['class_id'],
            'date_enrolled' => $_POST['date_enrolled'],
            'amount_paid' => $_POST['amount_paid'],
            'status' => $_POST['status']
        ];
        $result = $controller->enrollStudent($data);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Enrolled successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to enroll']);
        }
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'enroll_student_manual') {
        $data = [
            'student_id' => $_POST['student_id'],
            'class_id' => $_POST['class_id'],
            'date_enrolled' => $_POST['date_enrolled'],
            'amount_paid' => $_POST['amount_paid'],
            'status' => $_POST['status'] // This might be different or handled within the function for manual enrollment
        ];
        $controller->enrollStudentManual($data); // Call the manual enrollment function
        exit;
    }
    

    if (isset($_POST['action']) && $_POST['action'] == 'save_zoom_meeting') {
        $class_id = $_POST['class_id'] ?? null;
        $zoom_link = $_POST['zoom_link'] ?? '';
        $scheduled_date = $_POST['scheduled_date'] ?? '';
        $scheduled_time = $_POST['scheduled_time'] ?? '';
    
        // Instantiate the controller or include necessary files if it hasn't been done
        // $controller = new YourControllerClass();
    
        // Call the function and store the returned associative array
        $result = $controller->saveZoomMeeting($class_id, $zoom_link, $scheduled_date, $scheduled_time);
    
        // Encode the associative array to JSON and echo it
        echo json_encode($result);
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'enrollInClass') {
        // This will handle the AJAX request made from the frontend
        $controller->enrollInClass();
        exit; // Important to prevent further script execution
    }

} else {
    // If it's a GET request and you have an ID parameter, show the edit form
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['class_id'])) {
        $controller->edit($_GET['class_id']);
    }

    if (isset($_GET['action']) && $_GET['action'] == 'get_zoom_meetings' && isset($_GET['class_id'])) {
        $classId = $_GET['class_id'];
        $meetings = $controller->getZoomMeetingsByClassId($classId);
        echo json_encode($meetings);
        exit;
    }

    if (isset($_GET['action']) && $_GET['action'] == 'get_all_students') {
        $students = $controller->getAllStudents();
        echo json_encode($students);
        exit;
    }

    // This would be a good place to handle a 'list' or 'index' action to fetch all classes
    elseif (isset($_GET['action']) && $_GET['action'] == 'list') {
        $allClasses = $controller->getAllGroupClasses();
        // At this point, you can include the view which lists the group classes
        include '../../src/views/admin/group-classes.php';
    }

    elseif (isset($_GET['action']) && $_GET['action'] == 'get_enrolled_students' && isset($_GET['class_id'])) {
        $classId = $_GET['class_id'];
        $controller->getEnrolledStudentsByClassId($classId);
        // No need to call exit here, it's already being called inside the function
    }
}
