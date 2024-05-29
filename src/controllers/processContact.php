<?php
// src/models/processContact.php
namespace Balog\MyLanguageTutor\Models;

require_once __DIR__ . '/../models/Mailer.php';

use Balog\MyLanguageTutor\Models\Mailer;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Prepare email details
    $to = 'mylanguagetutor@mylanguagetutor.ca';
    $subject = "New Contact Form Submission from $name";
    $body = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>New Contact Form Submission</title>
        </head>
        <body>
            <table cellspacing='0' cellpadding='0' border='0' style='width: 100%; margin: auto; max-width: 600px; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6em;'>
                <tr>
                    <td>
                        <h2 style='color: #333333;'>Contact Form Submission</h2>
                        <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                        <p><strong>Message:</strong><br/>" . nl2br(htmlspecialchars($message)) . "</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    
    // Set additional headers to specify HTML content and sender information
    // $headers = "MIME-Version: 1.0" . "\r\n";
    // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // $headers .= "From: $name <$email>" . "\r\n";
    // $headers .= "Reply-To: $email" . "\r\n";

    $mailer = new Mailer();

    // Send the email using PHPMailer
    $status = $mailer->sendEmail($to, 'MyLanguageTutor', $subject, $body);
    // Send the email
    // $status = mail($to, $subject, $body, $headers);

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

