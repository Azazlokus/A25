<?php
namespace App\Application\Services;


use App\Domain\Users\UserEntity;
use App\Infrastructure\sdbh;

class AdminService {

    /** @var UserEntity */
    public $user;

    /** @var sdbh */
    public $db;

    public function __construct()
    {
        $this->user = new UserEntity();
        $this->db = new sdbh();
    }

    public function addNewProduct($name, $price)
    {
        try {
            $name = $this->db->escape_string($name);
            $price = (float)$price;

            $query = "INSERT INTO a25_products (NAME, PRICE) VALUES ('$name', '$price')";
            $result = $this->db->make_query($query);

            if (is_numeric($result) && $result > 0) {
                return ['success' => true, 'message' => 'Продукт успешно добавлен.'];
            } else {
                return ['success' => false, 'message' => 'Ошибка при добавлении продукта.'];
            }
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }
}
