<?php
namespace App;

require_once 'Infrastructure/sdbh.php';
use sdbh\sdbh;

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
        $tarif = $product['TARIFF'];

        $productPrice = $this->calculateProductPrice($price, $tarif, $days);
        $servicesPrice = $this->calculateServicesPrice($selectedServices, $days);

        return $productPrice + $servicesPrice;
    }

    private function calculateProductPrice($basePrice, $tarifSerialized, $days)
    {
        $tarifs = unserialize($tarifSerialized);

        if (!is_array($tarifs)) {
            return $basePrice * $days;
        }

        $productPrice = $basePrice;
        foreach ($tarifs as $dayCount => $tarifPrice) {
            if ($days >= $dayCount) {
                $productPrice = $tarifPrice;
            }
        }

        return $productPrice * $days;
    }

    private function calculateServicesPrice($selectedServices, $days)
    {
        $servicesPrice = 0;
        foreach ($selectedServices as $service) {
            $servicesPrice += (float)$service * $days;
        }
        return $servicesPrice;
    }
}

class CalculateController
{
    private $productRepository;
    private $priceCalculator;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->priceCalculator = new PriceCalculator();
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

        $totalPrice = $this->priceCalculator->calculateTotalPrice($product, $days, $selectedServices);
        echo $totalPrice;
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
