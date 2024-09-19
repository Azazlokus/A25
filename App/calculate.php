<?php
namespace App;
require_once './../vendor/autoload.php';

use App\Application\Controllers\CalculateController;

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
// Обработка запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CalculateController();
    $controller->calculate();
}
