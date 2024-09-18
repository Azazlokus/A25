<<?php
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
        <div class="col-12" id="count">
            <img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
            <h1>Панель администратора</h1>
        </div>
    </div>
    <!-- Форма для добавления нового продукта -->
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
                <button type="submit" class="btn btn-primary">Добавить продукт</button>
            </form>
        </div>
    </div>
    <!-- Таблица со списком продуктов -->
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
                    <!-- Добавьте другие поля, если необходимо -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        function loadProducts() {
            $.ajax({
                url: 'ProductController.php?action=get',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        var products = response.data;
                        var tbody = $('#productsTable tbody');
                        tbody.empty();
                        $.each(products, function(index, product) {
                            tbody.append('<tr>' +
                                '<td>' + product.ID + '</td>' +
                                '<td>' + product.NAME + '</td>' +
                                '<td>' + product.PRICE + '</td>' +
                                '<td>' + (product.TARIFF || '') + '</td>' +
                                '<td>' +
                                '<button class="btn btn-sm btn-warning edit-product" data-id="' + product.ID + '">Редактировать</button> ' +
                                '<button class="btn btn-sm btn-danger delete-product" data-id="' + product.ID + '">Удалить</button>' +
                                '</td>' +
                                '</tr>');
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при загрузке продуктов.');
                }
            });
        }

        // Загрузка списка продуктов при загрузке страницы
        loadProducts();

        // Обработка отправки формы добавления продукта
        $('#addProductForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: 'ProductController.php?action=add',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        alert(response.message);
                        $('#addProductForm')[0].reset();
                        loadProducts();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при добавлении продукта.');
                }
            });
        });

        // Обработка клика по кнопке удаления продукта
        $(document).on('click', '.delete-product', function() {
            var id = $(this).data('id');
            if(confirm('Вы уверены, что хотите удалить этот продукт?')) {
                $.ajax({
                    url: 'ProductController.php?action=delete',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            alert(response.message);
                            loadProducts();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Ошибка при удалении продукта.');
                    }
                });
            }
        });

        // Обработка клика по кнопке редактирования продукта
        $(document).on('click', '.edit-product', function() {
            var id = $(this).data('id');
            // Получаем данные продукта для заполнения формы редактирования
            $.ajax({
                url: 'ProductController.php?action=getProduct',
                type: 'GET',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        var product = response.data;
                        $('#editProductId').val(product.ID);
                        $('#editProductName').val(product.NAME);
                        $('#editProductPrice').val(product.PRICE);
                        // Открываем модальное окно
                        $('#editProductModal').modal('show');
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при получении данных продукта.');
                }
            });
        });

        // Обработка отправки формы редактирования продукта
        $('#editProductForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: 'ProductController.php?action=update',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        alert(response.message);
                        $('#editProductModal').modal('hide');
                        loadProducts();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Ошибка при обновлении продукта.');
                }
            });
        });
    });
</script>
</body>
</html>
