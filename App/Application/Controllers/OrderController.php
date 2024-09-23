<?php
namespace App\Application\Controllers;

use App\Infrastructure\EmailSender;

class OrderController
{
    public function sendOrder()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phoneNumber = $_POST['phone_number'] ?? null;
            $orderData = $_POST['order_data'] ?? null;

            if (empty($phoneNumber) || empty($orderData)) {
                echo json_encode(['success' => false, 'error' => 'Некорректные данные']);
                exit;
            }

            $message = "Поступила новая заявка.\n\n";
            $message .= "Номер телефона: " . $phoneNumber . "\n";
            $message .= "Данные заказа:\n";
            $message .= print_r($orderData, true);

            $emailSender = new EmailSender();

            if ($emailSender->send('Новая заявка с сайта', $message)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при отправке письма']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
        }
    }
}
