<?php
namespace App;
require_once './../vendor/autoload.php';

use App\Application\Controllers\CalculateController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CalculateController();
    $controller->calculate();
}
