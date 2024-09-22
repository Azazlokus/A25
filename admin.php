<?php
require_once 'vendor/autoload.php';

use App\Domain\Users\UserEntity;

$user = new UserEntity();
if (!$user->isAdmin) die('Доступ закрыт');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="row row-header">
        <div class="col-8" id="count">
            <img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
            <h1>Панель администратора</h1>
        </div>
        <div class="col-4">
            <div class="d-flex align-items-center">
                <input type="text" class="form-control" id="searchName" name="searchName"
                       placeholder="Поиск продукта по названию" required>
                <button type="button" id="searchButton" class="btn btn-primary ms-2">Поиск</button>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-12">
            <h2>Добавить новый продукт</h2>
            <form id="addProductForm">
                <div class="mb-3">
                    <label for="productName" class="form-label">Название продукта</label>
                    <input type="text" class="form-control" id="productName" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="productPrice" class="form-label">Цена</label>
                    <input type="number" class="form-control" id="productPrice" name="price" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Тарифы</label>
                    <div id="tariffsContainer">
                    </div>
                    <button type="button" class="btn btn-secondary" id="addTariffBtn">Добавить тариф</button>
                </div>

                <button type="submit" class="btn btn-primary">Добавить продукт</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <h3>Статистика продуктов</h3>
            <p>Количество продуктов: <span id="productCount"></span></p>
            <p>Продукты без тарифа: <span id="productsWithoutTariff"></span></p>
            <p>Самая дорогая услуга: <span id="maxService"></span></p>
            <p>Самая дешевая услуга: <span id="minService"></span></p>
            <p>Продукт с самым ранним изменением тарифа: <span id="earliestTariffChange"></span></p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2>Список продуктов</h2>
            <table class="table" id="productsTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Цена</th>
                    <th>Тариф</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editProductForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Редактировать продукт</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editProductId" name="id">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label">Название продукта</label>
                        <input type="text" class="form-control" id="editProductName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductPrice" class="form-label">Цена</label>
                        <input type="number" class="form-control" id="editProductPrice" name="price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/js/admin/product_management.js"></script>
<script src="assets/js/admin/search_and_statistics.js"></script>
</body>
</html>
