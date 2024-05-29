<?php
session_start();
require_once 'src/controllers/UserController.php';
require_once 'src/models/User.php';

$user = new User();

// Rest of the code...

$user = new User();

if (isset($_GET['username'])) {
    $username = $_GET['username'];

    $userDetails = $user->getUserByUsername($username);
    if ($userDetails) {
        $newToken = bin2hex(random_bytes(50));
        $newExpiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        $user->updateUserTokenAndExpiry($userDetails['id'], $newToken, $newExpiry);
        sendVerificationEmail($userDetails['email'], $userDetails['id'], $newToken, $newExpiry);

        $_SESSION['success'] = 'Email sent, please check your email.';
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = 'Invalid username.';
        header("Location: login.php");
        exit();
    }
}

if (isset($_GET['id'], $_GET['token'], $_GET['expiry'])) {
    $id = $_GET['id'];
    $token = $_GET['token'];
    $expiry = strtotime($_GET['expiry']);

    $userDetails = $user->getUserDetails($id);

    if ($userDetails && $userDetails['token'] == $token) {
        if (time() < $expiry) {
            $user->updateUserTokenAndVerification($id);
            echo 'Your email is verified!';

            $_SESSION['username'] = $userDetails['username'];
            $_SESSION['role'] = $userDetails['role'];
            $_SESSION['user_id'] = $id;

            if($userDetails['role'] == 'Student') {
                header("Location: src/views/student/profile");
            } else if($userDetails['role'] == 'Tutor') {
                header("Location: src/views/tutor/profile");
            } else if($userDetails['role'] == 'Admin') {
                header("Location: src/views/admin/profile");
            }
            exit();
        } else {
            $newToken = bin2hex(random_bytes(50));
            $newExpiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

            $user->updateUserTokenAndExpiry($id, $newToken, $newExpiry);
            sendVerificationEmail($userDetails['email'], $id, $newToken, $newExpiry);
            echo 'The verification token was expired. A new one has been sent to your email address.';
        }
    } else {
        echo 'Invalid verification link';
    }
} else {
    echo 'Invalid verification link';
}
?>
