<?php



if (session_status() === PHP_SESSION_NONE) {

    session_start();

}



require_once __DIR__ . '/../models/User.php';

require_once __DIR__ . '/../models/Mailer.php';



use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;

use Balog\MyLanguageTutor\Models\Mailer;



require_once __DIR__ . '/../../vendor/autoload.php';



$user = new User();



// Verication OTP



function sendVerificationEmail($email, $userId, $token, $expiry)

    {

        $mailer = new Mailer();

        

        $subject = "Verify Your Email";

        $verification_link = "https://mylanguagetutor.ca/verify.php?id=$userId&token=$token&expiry=$expiry";

        $message_content = "Click the link to verify your email:<br><a href='{$verification_link}'>Verify Email</a><br><br>If you can't click the link, copy and paste the URL below:<br>{$verification_link}";

        

        $template = file_get_contents(__DIR__ . '/general_email_template.html');

        $template = str_replace('{subject}', $subject, $template);

        // $template = str_replace('{logo_url}', 'logo.png', $template); // Change this to your logo path

        $template = str_replace('{message_content}', $message_content, $template);

        $template = str_replace('{current_year}', date('Y'), $template);



        try {

            $mailer->sendEmail($email, $userId, $subject, $template, strip_tags($message_content));

            return 'Email has been sent';

        } catch (Exception $e) {

            echo 'Message could not be sent. Mailer Error: ', $mailer->getErrorInfo();

        }

    }



    function getEmailContentWithTemplate($subject, $message_content) {

        $template = file_get_contents(__DIR__ . '/general_email_template.html');

        $template = str_replace('{subject}', $subject, $template);

        $template = str_replace('{message_content}', $message_content, $template);

        $template = str_replace('{current_year}', date('Y'), $template);

        return $template;

    }

    



// Password Reset



if (isset($_POST['forgotPassword'])) {

    $email = $_POST['email'];



    $token = $user->generateResetToken($email);

    if ($token) {

        $mailer = new Mailer();

        $subject = "Password Reset Request";



        $reset_link = "https://mylanguagetutor.ca/new-password.php?token=$token";

        $message_content = "Please click the link below to reset your password:<br><a href='{$reset_link}'>Reset Password</a><br><br>If you can't click the link, copy and paste the URL below:<br>{$reset_link}";

        

        $body = getEmailContentWithTemplate($subject, $message_content);

        $altBody = "Reset your password by pasting this link into your browser: {$reset_link}";  // added https



        try {

            $mailer->sendEmail($email, $userId, $subject, $body, $altBody);  // ensure $userId is correctly set

            $_SESSION['success'] = 'A password reset link has been sent to your email.';

        } catch (Exception $e) {

            $_SESSION['errors']['reset'] = "Message could not be sent. Mailer Error: {$mailer->getErrorInfo()}";

        }

        

        header("Location: ../../forgot-password.php");

    } else {

        $_SESSION['errors']['reset'] = 'If an account exists with this email, a reset link will be sent.';

        header("Location: ../../forgot-password.php");

    }



    exit();

}





if (isset($_POST['resetPassword'])) {

    $newPassword = $_POST['newPassword'];

    $token = $_POST['token'];



    // Validate token

    $email = $user->getEmailFromToken($token); // This function should return the associated email or null if the token is invalid/expired.



    if (!$email) {

        $_SESSION['errors']['token'] = 'Invalid or expired token.';

        header("Location: ../../new-password.php?token=$token");

        exit();

    }



    // Update password in the database

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $result = $user->resetPassword($token, $newPassword);



    if ($result) {

        $user->invalidateToken($token);  // Make sure the token can't be reused.

        $_SESSION['success'] = 'Password updated successfully.';

        header("Location: ../../login.php");

    } else {

        $_SESSION['errors']['update'] = 'Error updating password. Please try again later.';

        header("Location: ../../new-password.php?token=$token");

    }



    exit();

}





// Registration



if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    if (isset($_POST['register'])) 
    {

        // Registration Code Here...

        $username = $_POST['username'];

        $firstName = $_POST['firstName'];

        $lastName = $_POST['lastName'];

        $email = $_POST['email'];

        $password = $_POST['password'];

        $role = $_POST['role'];

        $profilePicture = '';



        $errors = [];



        // Validation code here...

        if ($username == "") {

            $errors['username'] = "Provide username!";

        } elseif ($user->usernameExists($username)) {

            $errors['username'] = "Username already taken!";

        }



        if ($email == "") {

            $errors['email'] = "Provide email!";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $errors['email'] = 'Please enter a valid email address!';

        } elseif ($user->emailExists($email)) {

            $errors['email'] = "Email already taken!";

        }



        if ($password == "") {

            $errors['password'] = "Provide password!";

        }



        if ($firstName == "") {

            $errors['firstName'] = "Provide first name!";

        }



        if ($lastName == "") {

            $errors['lastName'] = "Provide last name!";

        }



        if (!$errors) {

            try {

                $registration = $user->register($username, $firstName, $lastName, $email, $password, $role, $profilePicture);



                if ($registration) {

                    $userId = $registration['id'];

                    $token = $registration['token'];

                    $tokenExpiry = date("Y-m-d H:i:s", strtotime('+1 day'));



                    if (sendVerificationEmail($email, $userId, $token, $tokenExpiry)) {

                        $verificationLink = "https://mylanguagetutor.ca/verify.php?id=$userId&token=$token&expiry=$tokenExpiry";

                        $_SESSION['verificationLink'] = $verificationLink;

                        $_SESSION['success'] = 'Registration Successful. Please check your email to verify your account.';

                    }

                }

            } catch (PDOException $e) {

                $errors['database'] = $e->getMessage();

            }

        }



        $_SESSION['errors'] = $errors;

        header("Location: ../../register.php");

        exit();



        // Logout Code

        if (isset($_POST['logout'])) {

            session_destroy();

            unset($_SESSION['username']);

            unset($_SESSION['role']);

            unset($_SESSION['user_id']);

            header('location: ../../index.php');

        }

    } 
    elseif (isset($_POST['update_profile'])) {

        // Profile Update Code Here...



        $username = $_SESSION['username'];

        $firstName = $_POST['firstName'];

        $lastName = $_POST['lastName'];

        $email = $_POST['email'];

        $mobileNo = $_POST['mobileNo'];

        $country = $_POST["country"];

        $languagesSpoken = $_POST['languagesSpoken'];

        $languageAndEducationLevel = $_POST['languageAndEducationLevel'];

        $errors = [];



        // Handling the profile picture upload

        $baseDir = "../../student-uploads/";

        $userDir = $baseDir . $username . "/";



        // Create user directory if it does not exist

        if (!file_exists($userDir)) {

            mkdir($userDir, 0755, true);

        }



        // Sub-directory for profile picture

        $profilePictureDir = $userDir . "student-profile-picture/";



        // Create sub-directory if it does not exist

        if (!file_exists($profilePictureDir)) {

            mkdir($profilePictureDir, 0755, true);

        }



        $profilePicture = uploadFile($profilePictureDir, 'profilePicture', $errors);



        if (!$errors) {

            try {

                if ($user->updateProfile($username, $firstName, $lastName, $email, $mobileNo, $country, $languagesSpoken, $languageAndEducationLevel, $profilePicture)) {

                    header("Location: ../views/student/profile?success=true");

                    exit();

                }

            } catch (PDOException $e) {

                $errors['database'] = $e->getMessage();

            }

        }



        $_SESSION['errors'] = $errors;

        header("Location: ../views/student/profile?success=false");

        exit();

    } 
    elseif (isset($_POST['update_tutor_profile'])) 
    {

        // Tutor Profile Update Code Here...



        $username = isset($_SESSION['username']) ? $_SESSION['username'] : "";

        $firstName = isset($_POST['firstName']) ? $_POST['firstName'] : "";

        $lastName = isset($_POST['lastName']) ? $_POST['lastName'] : "";

        $email = isset($_POST['email']) ? $_POST['email'] : "";

        $mobileNo = isset($_POST['mobileNo']) ? $_POST['mobileNo'] : "";

        $country = isset($_POST["country"]) ? $_POST["country"] : "";

        $languagesSpoken = isset($_POST['languagesSpoken']) ? $_POST['languagesSpoken'] : "";

        $nativeLanguage = isset($_POST['nativeLanguage']) ? $_POST['nativeLanguage'] : "";

        $workingWith = isset($_POST['workingWith']) ? $_POST['workingWith'] : "";

        $levelsYouTeach = isset($_POST['levelsYouTeach']) ? $_POST['levelsYouTeach'] : "";

        $educationExperience = isset($_POST['educationExperience']) ? $_POST['educationExperience'] : "";

        $videoIntroduction = isset($_POST['videoIntroduction']) ? $_POST['videoIntroduction'] : "";



        $workingWithStr = is_array($workingWith) ? implode(',', $workingWith) : '';

        $levelsYouTeachStr = is_array($levelsYouTeach) ? implode(',', $levelsYouTeach) : '';

        $errors = [];



        // Handling the profile picture, CV and official ID upload

        $baseDir = "../../tutor-uploads/";

        $userDir = $baseDir . $username . "/";



        // Create user directory if it does not exist

        if (!file_exists($userDir)) {

            mkdir($userDir, 0755, true);

        }



        // Sub-directories for different file types

        $profilePictureDir = $userDir . "profile_picture/";

        $cvDir = $userDir . "cv/";

        $govIDDir = $userDir . "government_id/";



        // Create sub-directories if they do not exist

        if (!file_exists($profilePictureDir)) {

            mkdir($profilePictureDir, 0755, true);

        }



        if (!file_exists($cvDir)) {

            mkdir($cvDir, 0755, true);

        }



        if (!file_exists($govIDDir)) {

            mkdir($govIDDir, 0755, true);

        }



        $profilePicture = uploadFile($profilePictureDir, 'profilePicture', $errors);

        $cvTarget = uploadFile($cvDir, 'cv', $errors);

        $officialIDTarget = uploadFile($govIDDir, 'officialID', $errors);



        $profileApproved = $user->profileApproved($username);

        if($profileApproved === null || $profileApproved == 2) {

            $profileApprovedValue = 0;

        } else {

            $profileApprovedValue = $profileApproved;

        }





if (!$errors) {

    try {

        if ($user->updateTutorProfile(

            $username,

            $firstName,

            $lastName,

            $email,

            $mobileNo,

            $country,

            $languagesSpoken,

            $nativeLanguage,

            $workingWithStr,

            $levelsYouTeachStr,

            $educationExperience,

            $videoIntroduction,

            $profilePicture,

            $cvTarget,

            $officialIDTarget,

            $profileApprovedValue

        )) {

            header("Location: ../views/tutor/profile?success=true");

            exit();

        } else {

            $errors['update'] = "Failed to update the tutor profile";

        }

    } catch (PDOException $e) {

        $errors['database'] = $e->getMessage();

        error_log("Database Error: " . $e->getMessage()); // Directly log the database error

    }

}



if ($errors) { // Only log if there are errors

    error_log("Tutor Profile Update Errors: " . print_r($errors, true));

    $_SESSION['errors'] = $errors;

}



header("Location: ../views/tutor/profile?success=false");

exit();

}

}



function uploadFile($uploadDir, $fieldName, &$errors)

{

    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {

        $uploadFile = $uploadDir . basename($_FILES[$fieldName]['name']);



        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadFile)) {

            return $uploadFile;

        } else {

            $errors[$fieldName] = "Failed to upload the file";

        }

    }



    return '';

}



function getTutorReviews()

{

    global $user; 

    $username = $_SESSION['username'];

    $tutorData = $user->getTutorByUsername($username);

    return $tutorData;

}


?>

