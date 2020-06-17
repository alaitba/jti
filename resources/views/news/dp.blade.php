<script>
    var drp = $('#drp');
    drp.daterangepicker({
        "locale": {
            "format": "DD.MM.YYYY",
            "separator": " - ",
            "applyLabel": "Применить",
            "cancelLabel": "Отмена",
            "fromLabel": "С",
            "toLabel": "по",
            "daysOfWeek": ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            "monthNames": ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
            "firstDay": 1
        },
        "opens": "right",
        "drops": "up",
        "autoApply": true,
    }).on('apply.daterangepicker', (e, picker) => {
        const start = picker.startDate;
        const end = picker.endDate;
        $('#from_date').val(start.format('YYYY-MM-DD'));
        $('#to_date').val(end.format('YYYY-MM-DD'));
        $('#period').text(`${start.format('DD.MM.YYYY')} - ${end.format('DD.MM.YYYY')}`);
    });
    var pickerData = drp.data('daterangepicker');
    pickerData.setStartDate($('#from_date').data('formatted'));
    pickerData.setEndDate($('#to_date').data('formatted'));
    $('.selectpicker').selectpicker({container: 'body'});
    $('#public').on('change', e => {
        $('#userListDiv').toggleClass('d-none');
        $('#user_list').prop('disabled', $(e.currentTarget).val() === '1');
    });
</script>
<style>
    .daterangepicker_input {
        display: none;
    }
</style>
