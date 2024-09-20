<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application\Controllers\OrderController;

$orderController = new OrderController();
$orderController->sendOrder();