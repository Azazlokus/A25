<?php

namespace App;
require_once  './../vendor/autoload.php';

use App\Infrastructure\CurrencyExchange;

class CalculateController
{
    private $productRepository;
    private $priceCalculator;
    private $currencyExchanger;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->priceCalculator = new PriceCalculator();
        $this->currencyExchanger = new CurrencyExchange();
    }

    public function calculate()
    {
        $days = $this->getPostValue('days', 0);
        $productId = $this->getPostValue('product', 0);
        $selectedServices = $this->getPostValue('services', []);

        $product = $this->productRepository->getProductById($productId);

        if (!$product) {
            echo "Ошибка, товар не найден!";
            return;
        }

        $pricePerDay = $this->priceCalculator->getPricePerDay($product, $days);
        $totalPrice = $this->priceCalculator->calculateTotalPrice($product, $days, $selectedServices);
        $priceInYuan =$this->currencyExchanger->convertRubTo($totalPrice, 'CNY');
        if (empty($selectedServices)) {
            $response = [
                "totalPrice" => $totalPrice,
                "details" => "Выбрано: $days дней. Тариф: {$pricePerDay}р/сутки. Дополнительные услуги не выбраны. Цена в юанях: {$priceInYuan} ¥"
            ];
        } else {
            $servicePrice = $this->priceCalculator->calculateServicesPrice($selectedServices, 1);
            $response = [
                "totalPrice" => $totalPrice,
                "details" => "Выбрано: $days дней. Тариф: {$pricePerDay}р/сутки + {$servicePrice}р/сутки за дополнительные услуги. Цена в юанях: {$priceInYuan} ¥"
            ];
        }
        echo json_encode($response);
    }

    private function getPostValue($key, $default)
    {
        return $_POST[$key] ?? $default;
    }
}