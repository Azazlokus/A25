<?php

namespace App\Infrastructure\Repositories;
use App\Application\Controllers\ProductController;
use App\Infrastructure\sdbh;
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

    /**
     * Получает самую дорогую и самую дешёвую дополнительную услугу.
     * @return array
     */
    public function getMinMaxTariff(): array
    {
        $query = "SELECT MIN(TARIFF) as min_tariff, MAX(TARIFF) as max_tariff FROM a25_products";
        $result = $this->db->make_query($query);

        return $result;
    }

}
