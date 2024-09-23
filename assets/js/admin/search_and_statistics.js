$(document).ready(function () {
    function handleAjaxError(xhr, status, error) {
        console.error('AJAX Error:', error);
        alert('Ошибка при обработке запроса. Пожалуйста, попробуйте еще раз.');
    }

    function updateStatistics() {
        $.ajax({
            url: 'ProductController.php?action=getStatistics',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#productCount').text(response.data.productCount);
                    $('#productsWithoutTariff').text(response.data.productsWithoutTariff);
                    $('#minService').text(response.data.minService);
                    $('#maxService').text(response.data.maxService);
                    $('#earliestTariffChange').text(response.data.earliestTariffChangeProduct.NAME);
                } else {
                    alert('Ошибка при получении статистики: ' + response.message);
                }
            },
            error: handleAjaxError
        });
    }

    function searchProduct(productName) {
        if (productName.trim()) {
            $.ajax({
                url: 'ProductController.php?action=search',
                type: 'GET',
                data: {name: productName},
                dataType: 'json',
                beforeSend: function() {
                    $('#searchButton').prop('disabled', true).text('Поиск...');
                },
                success: function (response) {
                    if (response.success) {
                        alert('Продукт: ' + response.data.NAME + ' Цена: ' + response.data.PRICE);
                    } else {
                        alert('Продукт не найден.');
                    }
                },
                error: handleAjaxError,
                complete: function() {
                    $('#searchButton').prop('disabled', false).text('Поиск');
                }
            });
        } else {
            alert('Пожалуйста, введите название продукта для поиска.');
        }
    }

    $('#searchButton').click(function () {
        var productName = $('#searchName').val();
        searchProduct(productName);
    });

    updateStatistics();
});
