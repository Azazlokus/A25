<?php

namespace App;

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