<?php
namespace App\Infrastructure;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

class EmailSender
{
    private $mailer;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        $this->mailer->Port = $_ENV['MAIL_PORT'];

        $this->mailer->CharSet = 'UTF-8';

        $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);

        $this->mailer->addAddress($_ENV['MAIL_TO_ADDRESS'], $_ENV['MAIL_TO_NAME']);
    }

    public function send($subject, $message)
    {
        try {
            $this->mailer->isHTML(false);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $message;

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Ошибка отправки письма: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
