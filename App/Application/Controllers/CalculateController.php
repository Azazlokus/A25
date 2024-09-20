<?php

namespace App\Application\Controllers;

use App\Application\Services\CalculationService;
use Exception;

class CalculateController
{
    private $calculationService;

    public function __construct()
    {
        $this->calculationService = new CalculationService();
    }

    public function calculate()
    {
        try {
            // Получение данных из запроса
            $productId = $this->getPostValue('product', 0);
            $selectedServices = $this->getPostValue('services', []);
            $startDate = $this->getPostValue('start_date', null);
            $endDate = $this->getPostValue('end_date', null);

            if (!$productId || !$startDate || !$endDate) {
                throw new Exception('Пожалуйста, заполните все поля');
            }

            // Вызов сервиса для расчета
            $response = $this->calculationService->calculate($productId, $selectedServices, $startDate, $endDate);

            // Отправка ответа клиенту
            echo json_encode($response);

        } catch (Exception $e) {
            $this->outputError($e->getMessage());
        }
    }

    private function getPostValue($key, $default)
    {
        return $_POST[$key] ?? $default;
    }

    private function outputError($message)
    {
        echo json_encode(['error' => $message]);
        exit;
    }
}
