<?php

function verifyTutorProfile() {
    require_once __DIR__ . '/../../models/User.php';
    $user = new User();

    if(!isset($_SESSION)) {
        session_start(); 
    }

    if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Tutor' && $user->profileApproved($_SESSION['username']) !== 1) {
        $_SESSION['error'] = "Your profile has not been approved yet."; 
        header('Location: profile.php');
        exit();
    }
}

?>
