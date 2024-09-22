$(document).ready(function() {
    $(function() {
        $("#startDate, #endDate").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: new Date(),
            onSelect: function(selectedDate) {
                if (this.id === 'startDate') {
                    var minDate = $(this).datepicker('getDate');
                    minDate.setDate(minDate.getDate() + 1);
                    $("#endDate").datepicker("option", "minDate", minDate);
                }
            }
        });
    });

    $("#form").submit(function(event) {
        event.preventDefault();

        $('#info-icon').css('visibility', 'hidden');

        var startDate = $("#startDate").datepicker("getDate");
        var endDate = $("#endDate").datepicker("getDate");

        if (startDate >= endDate) {
            $("#total-price").text("начальная дата должна быть меньше конечной");
            return;
        }

        $.ajax({
            url: 'App/calculate.php',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(response) {
                if (response.error) {
                    $("#total-price").text(response.error);
                } else {
                    $("#total-price").text(response.totalPrice + " р.");
                    $("#info-icon").css('visibility', 'visible');

                    $('#info-icon').tooltip('dispose');

                    $('#info-icon').attr("title", response.details);

                    $('#info-icon').tooltip().tooltip('show');

                    $('#leave-request-btn').show();

                    window.orderData = response.orderData;
                }
            },
            error: function() {
                $("#total-price").text('Ошибка при расчете');
            }
        });
    });

    $('#phone-number').mask('+7 (999) 999-99-99');

    $('#leave-request-btn').click(function() {
        $('#requestModal').modal('show');
    });

    $('#request-form').submit(function(event) {
        event.preventDefault();

        var phoneNumber = $('#phone-number').val();

        var requestData = {
            phone_number: phoneNumber,
            order_data: window.orderData
        };

        $.ajax({
            url: 'App/send_order.php',
            type: 'POST',
            dataType: 'json',
            data: requestData,
            success: function(response) {
                if (response.success) {
                    alert('Заявка успешно отправлена!');
                    $('#requestModal').modal('hide');
                } else {
                    alert('Ошибка при отправке заявки: ' + response.error);
                }
            },
            error: function() {
                alert('Ошибка при отправке заявки.');
                console.log("Ошибка AJAX-запроса:", textStatus, errorThrown);
                console.log("Ответ сервера:", jqXHR.responseText);
            }
        });
    });
});