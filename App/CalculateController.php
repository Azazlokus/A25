<?php

namespace App;
require_once  './../vendor/autoload.php';

use App\Infrastructure\CurrencyExchange;
use App\Infrastructure\DataAdapter;
use DateTime;
use Exception;

class CalculateController
{
    private $dataAdapter;
    private $priceCalculator;
    private $currencyExchanger;

    public function __construct()
    {
        $this->dataAdapter = new DataAdapter();
        $this->priceCalculator = new PriceCalculator();
        $this->currencyExchanger = new CurrencyExchange();
    }

    public function calculate()
    {
        $productId = $this->getPostValue('product', 0);
        $selectedServices = $this->getPostValue('services', []);
        $startDate = $this->getPostValue('start_date', null);
        $endDate = $this->getPostValue('end_date', null);
        if (!$productId || !$startDate || !$endDate) {
            echo json_encode(['error' => 'Пожалуйста, заполните все поля']);
            exit;
        }
        $product = $this->dataAdapter->getProductById($productId);
        $days = $this->calculateDays($startDate, $endDate);
        if (!$product) {
            echo "Ошибка, товар не найден!";
            return;
        }

        $pricePerDay = $this->priceCalculator->getPricePerDay($product, $days);
        $totalPrice = $this->priceCalculator->calculateTotalPrice($product, $days, $selectedServices);
        $priceInYuan = $this->currencyExchanger->convertRubTo($totalPrice, 'CNY');
        $daysLabel = $this->getCorrectDayLabel($days);

        if (empty($selectedServices)) {
            $response = [
                "totalPrice" => $totalPrice,
                "details" => "Выбрано: $days $daysLabel. Тариф: {$pricePerDay}р/сутки. Дополнительные услуги не выбраны. Цена в юанях: {$priceInYuan} ¥"
            ];
        } else {
            $servicePrice = $this->priceCalculator->calculateServicesPrice($selectedServices, 1);
            $response = [
                "totalPrice" => $totalPrice,
                "details" => "Выбрано: $days $daysLabel. Тариф: {$pricePerDay}р/сутки + {$servicePrice}р/сутки за дополнительные услуги. Цена в юанях: {$priceInYuan} ¥"
            ];
        }

        echo json_encode($response);
    }

    private function getCorrectDayLabel($days): string
    {
        if ($days % 10 == 1 && $days % 100 != 11) {
            return 'день';
        } elseif ($days % 10 >= 2 && $days % 10 <= 4 && ($days % 100 < 10 || $days % 100 >= 20)) {
            return 'дня';
        } else {
            return 'дней';
        }
    }

    /**
     * @throws Exception
     */
    private function calculateDays($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $start->diff($end);
        return $interval->days;
    }
    private function getPostValue($key, $default)
    {
        return $_POST[$key] ?? $default;
    }
}