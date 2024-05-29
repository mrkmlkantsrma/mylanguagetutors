<?php

// src/models/processContact.php

namespace Balog\MyLanguageTutor\Models;



require_once __DIR__ . '/../models/Mailer.php';





if ($_SERVER["REQUEST_METHOD"] == "POST") {



    // Get the form data

    $name = $_POST["name"];

    $email = $_POST["email"];

    $message = $_POST["message"];



    // Prepare email details

    $to = 'mylanguagetutor@mylanguagetutor.ca';

    $subject = "New Contact Form Submission from $name";

    $body = "

    <h2>Contact Form Submission</h2>

    <p><strong>Name:</strong> $name</p>

    <p><strong>Email:</strong> $email</p>

    <p><strong>Message:</strong><br/> $message</p>

    ";

    

    // Set additional headers to specify HTML content and sender information

    $headers = "MIME-Version: 1.0" . "\r\n";

    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    $headers .= "From: $name <$email>" . "\r\n";

    $headers .= "Reply-To: $email" . "\r\n";



    // Send the email

    $status = mail($to, $subject, $body, $headers);



    if ($status) {

        // Redirect with success message

        header("Location: ../../contacts.php?status=success");

        exit();

    } else {

        // Redirect with error message

        header("Location: ../../contacts.php?status=error");

        exit();

    }

}



