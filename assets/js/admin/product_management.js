$(document).ready(function () {
    document.getElementById('addTariffBtn').addEventListener('click', function () {
        const existingDays = Array.from(document.getElementsByName('tariffDays[]')).map(input => input.value);

        const tariffsContainer = document.getElementById('tariffsContainer');
        const newTariff = document.createElement('div');
        newTariff.classList.add('row', 'mb-2');

        newTariff.innerHTML = `
            <div class="col-md-5">
                <input type="number" class="form-control" name="tariffDays[]" placeholder="Количество дней" required oninput="checkUniqueness(this)">
            </div>
            <div class="col-md-5">
                <input type="number" class="form-control" name="tariffPrice[]" placeholder="Цена тарифа" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-tariff-btn">Удалить</button>
            </div>
        `;

        tariffsContainer.appendChild(newTariff);
    });

    // Функция для проверки уникальности 'Количество дней'
    window.checkUniqueness = function (input) {
        const allDays = Array.from(document.getElementsByName('tariffDays[]')).map(input => input.value);
        if (new Set(allDays).size !== allDays.length) {
            input.setCustomValidity('Количество дней должно быть уникальным');
            input.reportValidity();
        } else {
            input.setCustomValidity('');
        }
    };

    $(document).on('click', '.remove-tariff-btn', function () {
        $(this).closest('.row').remove();
    });

    function loadProducts() {
        $.ajax({
            url: 'ProductController.php?action=get',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    var products = response.data;
                    var tbody = $('#productsTable tbody');
                    tbody.empty();
                    $.each(products, function (index, product) {
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
            error: function () {
                alert('Ошибка при загрузке продуктов.');
            }
        });
    }


    loadProducts();

    $('#addProductForm').submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: 'ProductController.php?action=add',
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    $('#addProductForm')[0].reset();
                    $('#tariffsContainer').empty();
                    loadProducts();
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Ошибка при добавлении продукта.');
            }
        });
    });

    $(document).on('click', '.delete-product', function () {
        var id = $(this).data('id');
        if (confirm('Вы уверены, что хотите удалить этот продукт?')) {
            $.ajax({
                url: 'ProductController.php?action=delete',
                type: 'POST',
                data: {id: id},
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        loadProducts();
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Ошибка при удалении продукта.');
                }
            });
        }
    });

    $(document).on('click', '.edit-product', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'ProductController.php?action=getProduct',
            type: 'GET',
            data: {id: id},
            dataType: 'json',
            success: function (response) {
                if (response.success) {
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
            error: function () {
                alert('Ошибка при получении данных продукта.');
            }
        });
    });

    $('#editProductForm').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: 'ProductController.php?action=update',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    $('#editProductModal').modal('hide');
                    loadProducts();
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Ошибка при обновлении продукта.');
            }
        });
    });
});

