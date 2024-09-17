<?php
namespace App;

require_once 'Infrastructure/sdbh.php';
require_once 'Infrastructure/CurrencyExchange.php';


use App\Infrastructure\CurrencyExchange;
use App\Infrastructure\sdbh;

class ProductRepository
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = new sdbh();
    }

    public function getProductById($productId)
    {

        $product = $this->dbh->make_query("SELECT * FROM a25_products WHERE ID = $productId");
        return $product ? $product[0] : null;
    }
}

class PriceCalculator
{
    public function calculateTotalPrice($product, $days, $selectedServices)
    {
        $price = $product['PRICE'];
        $tariff = $product['TARIFF'];

        $productPrice = $this->calculateProductPrice($product, $days);
        $servicesPrice = $this->calculateServicesPrice($selectedServices, $days);

        return $productPrice + $servicesPrice;
    }

    private function calculateProductPrice($product, $days)
    {
        $productPrice = $this->getPricePerDay($product, $days);

        return $productPrice * $days;
    }

    public function calculateServicesPrice($selectedServices, $days)
    {
        $servicesPrice = 0;
        foreach ($selectedServices as $service) {
            $servicesPrice += (float)$service * $days;
        }
        return $servicesPrice;
    }
    public function getPricePerDay($product, $days)
    {
        $basePrice = $product['PRICE'];
        $tariffs = unserialize($product['TARIFF']);

        if (!is_array($tariffs)) {
            return $basePrice;
        }

        ksort($tariffs);

        $productPrice = $basePrice;

        foreach ($tariffs as $dayCount => $tariffPrice) {
            if ($days >= $dayCount) {
                $productPrice = $tariffPrice;
            }
        }

        return $productPrice;
    }

}

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
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
// Обработка запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CalculateController();
    $controller->calculate();
}
