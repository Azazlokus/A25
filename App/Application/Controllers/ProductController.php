<?php
namespace App\Application\Controllers;


require_once '../../../vendor/autoload.php';

use App\Application\Services\AdminService;
use App\Domain\Users\UserEntity;
use App\Infrastructure\sdbh_dead_replicas;

class ProductController
{
    private AdminService $adminService;
    private UserEntity $user;

    public function __construct()
    {
        $this->adminService = new AdminService();
        $this->user = new UserEntity();

        if (!$this->user->isAdmin) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Доступ запрещен.']);
            exit;
        }
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? $_POST['action'] ?? null;

        switch ($action) {
            case 'add':
                $this->addProduct();
                break;
            case 'get':
                $this->getProducts();
                break;
            case 'getProduct':
                $this->getProduct();
                break;
            case 'delete':
                $this->deleteProduct();
                break;
            case 'update':
                $this->updateProduct();
                break;
            case 'getStatistics':
                $this->getStatistics();
                break;
            case 'search':
                $this->search();
                break;
            default:
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['success' => false, 'message' => 'Неверное действие.']);
                break;
        }
    }

    public function addProduct()
    {
        $name = $_POST['name'] ?? null;
        $price = $_POST['price'] ?? null;

        $tariffDays = $_POST['tariffDays'] ?? [];
        $tariffPrices = $_POST['tariffPrice'] ?? [];
        $tariffs = [];

        if (count($tariffDays) == count($tariffPrices)) {
            for ($i = 0; $i < count($tariffDays); $i++) {
                $day = intval($tariffDays[$i]);
                $price = intval($tariffPrices[$i]);
                $tariffs[$day] = $price;
            }

            $serialized = serialize($tariffs);

            $result = $this->adminService->addNewProduct($name, $price, $serialized);
        } else {
            $result = ['success' => false, 'message' => 'Несоответствие количества дней и цен'];
        }
        echo json_encode($result);

    }


    public function getProducts()
    {
        $result = $this->adminService->getProducts();

        echo json_encode($result);
    }
    public function getProduct()
    {
        $id = $_POST['id'] ?? null;
        $result = $this->adminService->getProduct($id);

        echo json_encode($result);
    }

    public function deleteProduct()
    {
        $id = $_POST['id'] ?? null;

        $result = $this->adminService->deleteProduct($id);

        echo json_encode($result);
    }

    public function updateProduct()
    {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? null;
        $price = $_POST['price'] ?? null;

        $result = $this->adminService->updateProduct($id, $name, $price);

        echo json_encode($result);
    }
    public function getStatistics()
    {
        try {
            $result = $this->adminService->getStatistics();

            echo json_encode(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Ошибка при получении статистики.']);
        }
    }

    /**
     * @throws sdbh_dead_replicas
     */
    public function search()
    {
        $productName = $_GET['name'] ?? ''; // Получение имени продукта из запроса

        if (empty($productName)) {
            echo json_encode(['success' => false, 'message' => 'Название продукта не указано']);
            return;
        }

        $products = $this->adminService->searchProductsByName($productName);

        if (empty($products)) {
            echo json_encode(['success' => false, 'message' => 'Продукт не найден']);
        } else {
            echo json_encode(['success' => true, 'data' => $products]);
        }
    }
}
$productController = new ProductController();
$productController->handleRequest();

