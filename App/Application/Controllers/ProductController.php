<?php
namespace App\Application\Controllers;


require_once '../../../vendor/autoload.php';

use App\Application\Services\AdminService;
use App\Domain\Users\UserEntity;

class ProductController
{
    private AdminService $adminService;
    private UserEntity $user;

    public function __construct()
    {
        $this->adminService = new AdminService();
        $this->user = new UserEntity();

        // Проверяем, является ли пользователь администратором
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
            case 'delete':
                $this->deleteProduct();
                break;
            case 'update':
                $this->updateProduct();
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

        $result = $this->adminService->addNewProduct($name, $price);

        echo json_encode($result);
    }

    public function getProducts()
    {
        $result = $this->adminService->getProducts();

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
}

$controller = new ProductController();
$controller->handleRequest();
