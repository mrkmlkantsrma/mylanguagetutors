<?php

require_once '../models/User.php';

session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    $user = new User();



    $username = $_POST['username'];

    $password = $_POST['password'];



    $errors = [];



    $userExists = $user->usernameExists($username);



    if (!$userExists) {

        $errors['username'] = "Incorrect username!";

    } else {

        $isPasswordCorrect = $user->verifyPassword($username, $password);

        $isEmailVerified = $user->isEmailVerified($username);

        $isAccountActive = $user->isAccountActive($username); // Check if account is active or suspended



        if (!$isPasswordCorrect) {

            $errors['password'] = "Incorrect password!";

        } elseif (!$isEmailVerified) {

            $errors['email'] = "Please verify your email! <a href='verify.php?username=" . $username . "'>Request Verification Link</a>";

        } elseif (!$isAccountActive) { // If the account is suspended

            $errors['account'] = "Your account is suspended. Please contact support.";

        }

    }



    if (empty($errors)) {

        $role = $user->getUserRole($username);



        $_SESSION['username'] = $username;

        $_SESSION['role'] = $role;

        $_SESSION['user_id'] = $user->getId($username);



        // Redirect to initially requested page if it exists in the session

        if (isset($_SESSION['requested_page'])) {

            $redirectURL = $_SESSION['requested_page'];

            unset($_SESSION['requested_page']);

            header("Location: $redirectURL");

            exit();

        }



        if ($role == 'Student') {

            header("Location: ../views/student/my-lessons.php");

        } elseif ($role == 'Tutor') {

            if ($user->profileApproved($username)) {

                header("Location: ../views/tutor/my-lessons.php");

            } else {

                header("Location: ../views/tutor/profile.php");

            }

        } elseif ($role == 'Admin') {

            header("Location: ../views/admin/overview.php");

        } else {

            header("Location: ../../admin/index.php");

        }

        exit();

    } else {

        $_SESSION['errors'] = $errors;

        header("Location: ../../login.php");

        exit();

    }

}





if (isset($_POST['logout'])) {

    session_destroy();

    unset($_SESSION['user']);

    unset($_SESSION['role']);

    unset($_SESSION['user_id']);

    unset($_SESSION['userData']);

    unset($_SESSION['username']);

    unset($_SESSION['user_data']);

    header('location: ../../index.php');

}



?>

