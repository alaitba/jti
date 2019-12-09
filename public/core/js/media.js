$(document.body).on('click', '.add-photo', function (e) {
    e.preventDefault();
    $(document.body).find('.form-input-image-media').trigger('click');
});

$(document.body).on('change', '.form-input-image-media', function (e) {
    e.preventDefault();
    $("#formImage").submit();
});

$(document.body).on('submit', '#formImage', function (e) {
    e.preventDefault();
    var url = $(this).attr('action');
    var formData = new FormData($(this)[0]);
    var method = $(this).attr('method');
    let blockSelector = ".www";

    $.ajax({
        method: method,
        url: url,
        data: formData,
        dataType: 'json',
        async: true,
        cache: false,
        contentType: false,
        processData: false,

        beforeSend: function () {
            // mask(blockSelector)
        },
        success: function (data) {

            // console.log(data);
            $(".media-block").empty();

            $(".media-block").prepend(data.media);
            // updateTableRow(data.table_row)
            //initCrop();
        },
        error: function (response) {
            if (response.status == 419) {
                location.reload();
            }

            if (response.status == 422) {
                alert('Один или несколько файлов невалидны');
            }
        },
        complete: function () {
            // unmask(blockSelector)
            $("#addLookForm").find('.look-file-input').val('');
        }

    });
});


$(document.body).on('click', '.delete-media-data', function (e) {
    e.preventDefault();
    var url = $(this).attr('data-url');
    var title = $(this).data('title');
    var self = $(this);
    Swal.fire({
        title: 'Удаление',
        text: 'Вы уверены, что хотите удалить картинку?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'rgb(48, 133, 214)',
        cancelButtonColor: '#aaa',
        cancelButtonText: 'Отмена',
        confirmButtonText: 'Подтвердить'
    }).then((result) => {
        if (result.value) {
            app.functions.ajaxGet({url})
            self.parent().parent().fadeOut();
        }
    })
});


$(document.body).on('click', '.make-main-image', function (e) {

    e.preventDefault();
    var url = $(this).attr('href');
    var title = $(this).data('title');
    var self = $(this);
    Swal.fire({
        title: 'Сделать главной?',
        text: '',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'rgb(48, 133, 214)',
        cancelButtonColor: '#aaa',
        cancelButtonText: 'Отмена',
        confirmButtonText: 'Подтвердить'
    }).then((result) => {
        if (result.value) {
            app.functions.ajaxGet({url})
            $(".media-block").find('.make-main-image').show();
            $(self).fadeOut();
        }
    })
});