<?php
namespace App\Application\Services;

require_once  './../../../vendor/autoload.php';

use App\Domain\Users\UserEntity;
use App\Infrastructure\Repositories\ProductRepository;
use App\Infrastructure\Repositories\SettingsRepository;
use App\Infrastructure\sdbh;
use Exception;

class AdminService {

    public $user;

    public $db;
    private $productRepository;
    private $settingsRepository;

    public function __construct()
    {
        $this->user = new UserEntity();
        $this->db = new sdbh();
        $this->productRepository = new ProductRepository();
        $this->settingsRepository = new SettingsRepository();
    }

    public function addNewProduct($name, $price, $tariffs): array
    {
        try {
            $name = $this->db->escape_string($name);
            $price = (float)$price;

            $query = "INSERT INTO a25_products (NAME, PRICE, TARIFF) VALUES ('$name', '$price', '$tariffs')";
            $result = $this->db->make_query($query);

            if (is_numeric($result) && $result > 0) {
                return ['success' => true, 'message' => 'Продукт успешно добавлен.'];
            } else {
                return ['success' => false, 'message' => 'Ошибка при добавлении продукта.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }

    public function getProducts(): array
    {
        try {
            $query = "SELECT * FROM a25_products";
            $result = $this->db->make_query($query);

            if (is_array($result)) {
                return ['success' => true, 'data' => $result];
            } else {
                return ['success' => false, 'message' => 'Ошибка при получении списка продуктов.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }
    public function getProduct($id): array
    {
        try {
            $id = intval($id);

            $query = "SELECT * FROM a25_products WHERE id = {$id}";
            $result = $this->db->make_query($query);

            if (is_array($result)) {
                return ['success' => true, 'data' => $result];
            } else {
                return ['success' => false, 'message' => 'Ошибка при получении списка продуктов.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }

    public function deleteProduct($id): array
    {
        try {
            $id = (int)$id;
            $query = "DELETE FROM a25_products WHERE ID = $id";
            $result = $this->db->make_query($query);

            if (is_numeric($result) && $result > 0) {
                return ['success' => true, 'message' => 'Продукт успешно удален.'];
            } else {
                return ['success' => false, 'message' => 'Ошибка при удалении продукта.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }
    public function updateProduct($id, $name, $price)
    {
        try {
            $id = (int)$id;
            $name = $this->db->escape_string($name);
            $price = (float)$price;

            $query = "UPDATE a25_products SET NAME = '$name', PRICE = '$price' WHERE ID = $id";
            $result = $this->db->make_query($query);

            if (is_numeric($result) && $result > 0) {
                return ['success' => true, 'message' => 'Продукт успешно обновлен.'];
            } else {
                return ['success' => false, 'message' => 'Ошибка при обновлении продукта.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }
    public function getStatistics(): array
    {
        $productCount = count($this->productRepository->getAllProducts());

        $fullProductsWithoutTariff  = $this->productRepository->getProductsNoTariff();
        $productsWithoutTariffNames = array_map(function($product) {
            return $product['NAME'];
        }, $fullProductsWithoutTariff);

        $minMaxTariff = $this->getMinMaxServices();

        $earliestProduct = $this->getEarliestTariffChangeProduct();



        return [
            'productCount' => $productCount,
            'productsWithoutTariff' => $productsWithoutTariffNames,
            'minService' => $minMaxTariff['minService'],  // Самая дешевая услуга
            'maxService' => $minMaxTariff['maxService'],  // Самая дорогая услуга
            'earliestTariffChangeProduct' => $earliestProduct,
        ];
    }

    private function getEarliestTariffChangeProduct()
    {
        // Получаем все продукты из репозитория
        $products = $this->productRepository->getAllProducts();

        $earliestProduct = null;
        $earliestDays = []; // Массив для сравнения всех дней изменения тарифа

        foreach ($products as $product) {
            if (!empty($product['TARIFF'])) {
                // Десериализуем тариф
                $tariffs = unserialize($product['TARIFF']);

                // Проверяем, что тарифы корректны (являются массивом)
                if (is_array($tariffs) && count($tariffs) > 1) {
                    // Получаем ключи (дни изменения тарифов)
                    $days = array_keys($tariffs);

                    // Убираем первый элемент (нулевой день)
                    array_shift($days);

                    // Сортируем дни для правильного сравнения
                    sort($days);

                    // Если это первый продукт или найден продукт с более ранними изменениями
                    if (empty($earliestDays) || $this->compareTariffChanges($earliestDays, $days)) {
                        $earliestDays = $days;
                        $earliestProduct = $product;
                    }
                }
            }
        }

        return $earliestProduct;
    }

    private function compareTariffChanges(array $currentEarliestDays, array $newDays): bool
    {
        $length = min(count($currentEarliestDays), count($newDays));

        for ($i = 0; $i < $length; $i++) {
            if ($newDays[$i] < $currentEarliestDays[$i]) {
                return true; //
            } elseif ($newDays[$i] > $currentEarliestDays[$i]) {
                return false;
            }
        }

        return count($newDays) < count($currentEarliestDays);
    }
    public function getMinMaxServices(): array
    {
        // Получаем все настройки (услуги) из репозитория
        $settings = $this->settingsRepository->getAllSettings();

        $services = [];
        foreach ($settings as $setting) {
            // Проверяем, что это настройки услуг
            if ($setting['set_key'] === 'services') {
                // Десериализуем строку с услугами
                $services = unserialize($setting['set_value']);
                break;  // Прекращаем цикл, как только нашли нужные услуги
            }
        }

        // Если услуг нет, возвращаем null
        if (empty($services)) {
            return [
                'minService' => null,
                'maxService' => null,
            ];
        }

        // Инициализируем переменные для самой дешевой и самой дорогой услуг
        $minService = null;
        $maxService = null;

        $minPrice = PHP_INT_MAX;
        $maxPrice = PHP_INT_MIN;

        // Проходим по каждой услуге и находим самую дешевую и самую дорогую
        foreach ($services as $serviceName => $servicePrice) {
            if ($servicePrice < $minPrice) {
                $minPrice = $servicePrice;
                $minService = "$serviceName - $servicePrice";
            }

            if ($servicePrice > $maxPrice) {
                $maxPrice = $servicePrice;
                $maxService = "$serviceName - $servicePrice";
            }
        }

        return [
            'minService' => $minService,
            'maxService' => $maxService,
        ];
    }

}
$s = new AdminService();
$s->getStatistics();