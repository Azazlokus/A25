<?php
namespace App\Infrastructure;

use App\Infrastructure\sdbh;

class DataAdapter
{
    private $db;

    public function __construct()
    {
        $this->db = new sdbh();
    }

    public function getProducts()
    {
        $query = "SELECT * FROM a25_products";
        $result = $this->db->make_query($query);
        return $result;
    }

    public function getProductById($id)
    {
        $id = $this->db->escape_string($id);
        $query = "SELECT * FROM a25_products WHERE ID = '$id' LIMIT 1";
        $result = $this->db->make_query($query);
        return $result[0] ?? null;
    }

    public function getServices()
    {
        $result = $this->db->mselect_rows('a25_settings', ['set_key' => 'services'], 0, 1, 'id');
        if (!empty($result)) {
            return unserialize($result[0]['set_value']);
        }
        return [];
    }

    public function getPrice($productId)
    {
        $product = $this->getProductById($productId);
        return $product['PRICE'] ?? null;
    }
}