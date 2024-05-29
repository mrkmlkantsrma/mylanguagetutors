<?php
// src/models/processContact.php
namespace Balog\MyLanguageTutor\Models;

require_once __DIR__ . '/../models/Mailer.php';

// Check if the form was submitted
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $subjectFromUser = $_POST["subject"];
    $message = $_POST["message"];
    $lessonDetail = isset($_POST["lesson"]) && $_POST["lesson"] != '' ? $_POST["lesson"] : "Not provided";

    // Prepare email details
    $toAddress = 'contact@mylanguagetutor.ca';
    $toName = 'My Language Tutor';
    $subject = "New Support Request from $name - $subjectFromUser";
    $body = "
        <h2>Support Request</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Lesson Details:</strong> $lessonDetail</p>
        <p><strong>Subject:</strong> $subjectFromUser</p>
        <p><strong>Message:</strong><br/> $message</p>
    ";
    $altBody = "Name: $name\nEmail: $email\nLesson Details: $lessonDetail\nSubject: $subjectFromUser\nMessage: $message";

    // Send the email
    $mailer = new Mailer();
    $status = $mailer->sendEmail($toAddress, $toName, $subject, $body, $altBody);

    if ($status == "success") {
        header("Location:  ../views/student/help-support.php?status=success");
        exit();  // it's a good practice to call exit() after a header redirect
    } else {
        header("Location:  ../views/student/help-support.php?status=error");
        exit();
    }
}
