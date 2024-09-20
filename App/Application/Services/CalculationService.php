<?php

namespace App\Application\Services;

use App\Domain\Calculators\PriceCalculator;
use App\Infrastructure\CurrencyExchanger;
use App\Infrastructure\DataAdapter;
use DateTime;
use Exception;

class CalculationService
{
    private $dataAdapter;
    private $priceCalculator;
    private $currencyExchanger;

    public function __construct()
    {
        $this->dataAdapter = new DataAdapter();
        $this->priceCalculator = new PriceCalculator();
        $this->currencyExchanger = new CurrencyExchanger();
    }

    public function calculate($productId, $selectedServices, $startDate, $endDate)
    {
        // Получение продукта по ID
        $product = $this->getProduct($productId);

        // Расчет количества дней
        $days = $this->calculateDays($startDate, $endDate);

        // Расчет цен
        $pricingData = $this->calculatePricing($product, $days, $selectedServices);

        // Формирование деталей заказа
        $details = $this->buildDetails($pricingData, $days, $selectedServices);

        // Формирование данных заказа
        $orderData = $this->buildOrderData($days, $pricingData['totalPrice'], $pricingData['priceInYuan']);

        return [
            'totalPrice' => $pricingData['totalPrice'],
            'details' => $details,
            'orderData' => $orderData,
        ];
    }

    private function getProduct($productId)
    {
        $product = $this->dataAdapter->getProductById($productId);

        if (!$product) {
            throw new Exception('Ошибка, товар не найден!');
        }

        return $product;
    }

    private function calculateDays($startDate, $endDate)
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days;

        if ($days <= 0) {
            throw new Exception('Дата окончания должна быть позже даты начала');
        }

        return $days;
    }

    private function calculatePricing($product, $days, $selectedServices)
    {
        $pricePerDay = $this->priceCalculator->getPricePerDay($product, $days);
        $totalPrice = $this->priceCalculator->calculateTotalPrice($product, $days, $selectedServices);
        $priceInYuan = $this->currencyExchanger->convertRubTo($totalPrice, 'CNY');

        return [
            'pricePerDay' => $pricePerDay,
            'totalPrice' => $totalPrice,
            'priceInYuan' => $priceInYuan,
        ];
    }

    private function buildDetails($pricingData, $days, $selectedServices)
    {
        $daysLabel = $this->getCorrectDayLabel($days);
        $pricePerDay = $pricingData['pricePerDay'];
        $priceInYuan = $pricingData['priceInYuan'];

        if (empty($selectedServices)) {
            $details = "Выбрано: $days $daysLabel. Тариф: {$pricePerDay}р/сутки. Дополнительные услуги не выбраны. Цена в юанях: {$priceInYuan} ¥";
        } else {
            $servicePrice = $this->priceCalculator->calculateServicesPrice($selectedServices, 1);
            $details = "Выбрано: $days $daysLabel. Тариф: {$pricePerDay}р/сутки + {$servicePrice}р/сутки за дополнительные услуги. Цена в юанях: {$priceInYuan} ¥";
        }

        return $details;
    }

    private function buildOrderData($days, $totalPrice, $priceInYuan)
    {
        return "Совершен заказ. Количество дней: $days. Общая цена: $totalPrice. Цена в юанях: $priceInYuan";
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
}