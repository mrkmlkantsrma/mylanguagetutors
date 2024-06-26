<?php
namespace Balog\MyLanguageTutor\Models;

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class Mailer
{
    private $mail;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Adjust the path if necessary
        $dotenv->load();

        $this->mail = new PHPMailer(true);
        $this->setup();
    }

    private function setup()
    {
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['SMTP_HOST'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV['SMTP_USERNAME'];
        $this->mail->Password = $_ENV['SMTP_PASSWORD'];
        $this->mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $this->mail->Port = intval($_ENV['SMTP_PORT']); 
        $this->mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
    }

    public function sendEmail($toAddress, $toName, $subject, $body, $altBody)
    {
        try {
            $this->mail->addAddress($toAddress, $toName);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody;
            $this->mail->send();
            return "success";
        } catch (Exception $e) {
            // Log the error
            error_log("Mailer Error: " . $e->getMessage());
            return "error";
        }
    }

    public function getErrorInfo()
    {
        return $this->mail->ErrorInfo;
    }
}
