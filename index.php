<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Infrastructure\DataAdapter;

$dataAdapter = new DataAdapter();
$services = $dataAdapter->getServices();
$products = $dataAdapter->getProducts();
?>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="row row-header">
        <div class="col-12" id="count">
            <img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
            <h1>Прокат Y</h1>
        </div>
    </div>

    <div class="row row-form">
        <div class="col-12">
            <form action="App/calculate.php" method="POST" id="form">

                <?php
                if (is_array($products)) { ?>
                    <label class="form-label" for="product">Выберите продукт:</label>
                    <select class="form-select" name="product" id="product">
                        <?php foreach ($products as $product) {
                            $name = $product['NAME'];
                            $price = $product['PRICE'];
                            $tarif = $product['TARIFF'];
                            ?>
                            <option value="<?= $product['ID']; ?>"><?= $name; ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>

                <div class="container p-0">
                    <div class="row mx-0">
                        <div class="col-md-6 px-0 pe-2">
                            <label for="startDate" class="form-label">Дата начала аренды:</label>
                            <input type="text" name="start_date" class="form-control" id="startDate" required>
                        </div>
                        <div class="col-md-6 px-0 ps-2">
                            <label for="endDate" class="form-label">Дата окончания аренды:</label>
                            <input type="text" name="end_date" class="form-control" id="endDate" required>
                        </div>
                    </div>
                </div>


                <?php
                if (is_array($services)) {
                    ?>
                    <label for="customRange1" class="form-label">Дополнительно:</label>
                    <?php
                    $index = 0;
                    foreach ($services as $k => $s) {
                        ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="services[]" value="<?= $s; ?>" id="flexCheck<?= $index; ?>">
                            <label class="form-check-label" for="flexCheck<?= $index; ?>">
                                <?= $k ?>: <?= $s ?>
                            </label>
                        </div>
                        <?php $index++; } ?>
                <?php } ?>

                <button type="submit" class="btn btn-primary">Рассчитать</button>
            </form>

            <h5>Итоговая стоимость: <span id="total-price"></span>
                <i class="fas fa-info-circle" id="info-icon" data-placement="right"></i>
            </h5>

            <button type="button" class="btn btn-success" id="leave-request-btn" style="display: none;">Оставить заявку</button>

            <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="requestModalLabel">Оставить заявку</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            <form id="request-form">
                                <div class="mb-3">
                                    <label for="phone-number" class="form-label">Номер телефона:</label>
                                    <input type="text" class="form-control" id="phone-number" name="phone_number" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Оставить</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Скрипты -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script src="assets/js/index/index.js"></script>

</body>
</html>
