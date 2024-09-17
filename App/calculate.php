<?php
namespace App;
require_once __DIR__ . '/CalculateController.php';

use App\CalculateController;

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
// Обработка запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CalculateController();
    $controller->calculate();
}
