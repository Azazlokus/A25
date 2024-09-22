<?php

namespace App\Infrastructure\Repositories;
use App\Application\Controllers\ProductController;
use App\Infrastructure\sdbh;
use App\Infrastructure\sdbh_dead_replicas;
use App\Infrastructure\sdbh_exception;
use Exception;

class ProductRepository
{
    private $db;

    public function __construct()
    {
        $this->db = new sdbh();
    }

    /**
     * Получает все продукты из базы данных.
     * @return array
     */
    public function getAllProducts(): array
    {
        $query = "SELECT * FROM a25_products";
        $result = $this->db->make_query($query);

        return $result;
    }

    /**
     * Получает продукты, у которых не задано поле TARIFF.
     * @return array
     */
    public function getProductsNoTariff(): array
    {
        $query = "SELECT * FROM a25_products WHERE TARIFF IS NULL OR TARIFF = ''";
        $result = $this->db->make_query($query);

        return $result;
    }



}
