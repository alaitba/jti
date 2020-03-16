/**
 * Обработчик клика
 */
$(document.body).on('click', '.handle-click', function (e) {
    e.preventDefault();
    let type = $(this).data('type');
    switch (type) {
        case 'modal':
            var url = $(this).data('url');
            let modal = $(this).data('modal');
            var params = {'url': url, 'modal': modal};
            app.functions.loadContentInModal(params);
            break;
        case 'ajax-get':
            app.functions.ajaxGet({url: $(this).data('url')});
            break;

        case 'trigger-hidden-input':
            let input = $($(this).data('input-id'));
            if (input)
            {
                input.trigger('click');
            }
            break;

        case 'confirm':
            var url = $(this).data('url');
            let title = $(this).data('title');
            let message = $(this).data('message');
            let cancelText = $(this).data('cancel-text');
            let confirmText = $(this).data('confirm-text');
            let confirmBtnColor = $(this).data('confirm-btn-color');
            let cancelBtnColor = $(this).data('cancel-btn-color');

            var params = {
                url: url,
                title: title,
                message: message,
                cancelText: cancelText,
                confirmText: confirmText,
                confirmBtnColor: confirmBtnColor,
                cancelBtnColor: cancelBtnColor
            };
            app.functions.showConfirm(params)
            break;
    }
});

$(document.body).on('change', 'input[type=file]', function (e) {

    if ($(this).hasClass('form-input-image'))
    {
        let form = $(this).closest('form');

        if (form.hasClass('ajax'))
        {
            form.submit();
        }
    }


});


$(document.body).on('click', '.pagination .page-link', function (e) {
    e.preventDefault();
    let url = $(this).attr('href');
    app.functions.blockPage();
    app.functions.loadContentInTable({url: url});
    app.functions.unblockPage()
});


$(document).ready(function () {

    /**
     * Перехват сабмита формы
     */
    $('body').on('submit', 'form', function (e) {
        /**
         * Если у формы есть класс .ajax то берем сабмит на себя
         */
        if ($(this).hasClass('ajax')) {
            e.preventDefault();

            var blockElement = $(this).data('ui-block-element');

            if (!blockElement) {
                app.functions.blockPage();
            } else {
                app.functions.blockElement({selector: blockElement});
            }

            app.functions.ajaxPost($(this));
        }

    });


    /**
     * Загрузка контента в таблицы
     */

    $.each($('.ajax-content'), function () {

        let url = $(this).attr('data-url');
        app.functions.blockPage();
        app.functions.loadContentInTable({url: url});
        app.functions.unblockPage()

    });

    /**
     * Инициализация дейт редактора
     */
    app.functions.initEditor();

    /**
     * Инициализация дейт пикера
     */
    app.functions.initDatePicker();

    /**
     * Инициализация дейт time пикера
     */
    app.functions.initDateTimePicker();

    /**
     * Инициализация Select2
     */
    app.functions.initSelect2();

});

let app = {

    lastBlockedElement: {},

    modals: {
        'regularModal': "#regularModal",
        'largeModal': '#largeModal',
        'superLargeModal': '#superLargeModal',
        'editorModal' : '#editorModal',
        'manageFolderModal' : "#manageFolderModal",
        'editImageModal' : '#editImageModal'
    },

    vars : {
        editor: null
    },

    functions: {

        initCrop: function(params){
            myCropper();
        },

        editorInject: function(params){
            app.vars.editor.insertHtml(params.content, 'unfiltered_html')
        },

        loadContentInTable: function (params) {
            app.functions.ajaxGet({url: params.url});
        },

        prependTableRow: function (params) {
            $(params.selector).find('tbody').prepend(params.content);
        },

        updateTableRow: function (params) {
            $(params.selector).find(params.row).fadeOut(function () {
                $(this).replaceWith(params.content);
                $(this).fadeIn();
            });
        },

        deleteTableRow: function(params){
            $(params.selector).find(params.row).fadeOut(function () {
                $(this).remove();
            });
        },

        updateTableContent: function (params) {
            $(params.selector).find('tbody').html(params.content);
            if (params.pagination) {
                $('.pagination_placeholder').html(params.pagination)
            }
        },

        updateElementContent: function (params) {
            if (params.selector) {
                $.each(params.selector, function (selector, content) {
                    $(selector).html(content);
                });

            }
        },

        replaceElementContent: function (params) {
            if (params.selector) {
                $(params.selector).replaceWith(params.content);
            }
        },

        // Menu
        updateMenuList: function (params) {
            $("#menusList").html(params.content);
        },

        showNotify: function (params) {
            $.notify({
                // options
                message: params.message
            }, {
                // settings
                type: params.type,
                newest_on_top: true,
                placement: {
                    from: "top",
                    align: "right"
                },

                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                },

            });
        },

        showAlert: function (params) {

            Swal.fire({
                type: params.alert_type,
                title: params.alert_header,
                text: params.alert_message,
            })
        },

        showConfirm: function(params) {

            let cancelText = (params.cancelText) ? params.cancelText : 'Отмена';
            let confirmText = (params.confirmText) ? params.confirmText : 'Подтвердить';
            let confirmBtnColor = params.confirmBtnColor;
            let cancelBtnColor = params.cancelBtnColor;

            Swal.fire({
                title: params.title,
                text: params.message,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: cancelBtnColor,
                cancelButtonText: cancelText,
                confirmButtonText: confirmText
            }).then((result) => {
                if (result.value) {
                    app.functions.ajaxGet({url: params.url})
                }
            })

        },

        redirect: function (params) {
            location.replace(params.url);
        },

        blockInterface: function () {
            $(".preloader").show();
            $(".preloader .sk-cube-grid").show();
        },


        unBlockInterface: function () {
            $(".preloader").fadeOut(600);
            $(".preloader .sk-cube-grid").fadeOut(600);
        },



        validationFail: function (params) {
            for (input in params.response.errors) {


                let formGroup = $("#" + params.formId + " *[id='" + input + "']").closest('.form-group');

                formGroup.addClass('has-error');

                for (message in params.response.errors[input]) {
                    formGroup.find('.help-block').append(' ' + params.response.errors[input][message]);
                }
            }
        },

        validationReset: function () {
            $('.form-group').find('.help-block').html('');
            $('.form-group').removeClass('has-error');
        },

        loadContentInModal: function (params) {
            app.functions.openModal({modal: params.modal});
            app.functions.blockElement({selector: app.modals[params.modal] + ' .modal-body'});
            app.functions.ajaxGet({url: params.url});
        },

        openModal: function (params) {
            if (!app.modals.hasOwnProperty(params.modal)) {
                alert('Модального окна: ' + params.modal + ' не существует');
                return;
            }

            $(app.modals[params.modal]).find('.modal-body').html('');
            $(app.modals[params.modal]).find('.modal-title').html('Подождите');
            $(app.modals[params.modal]).modal('show');
        },

        closeModal: function (params) {

            if (!app.modals.hasOwnProperty(params.modal)) {
                alert('Модального окна: ' + params.modal + ' не существует');
                return;
            }

            $(app.modals[params.modal]).modal('hide');
        },

        updateModal: function (params) {
            $(app.modals[params.modal]).find('.modal-body').html(params.content);
            $(app.modals[params.modal]).find('.modal-title').html(params.title);
        },

        blockElement: function (params) {

            app.lastBlockedElement = params;

            mApp.block(params.selector, {
                overlayColor: "#000000",
                type: "loader",
                state: "success",
                message: "Подождите..."
            });
        }
        ,

        unblockElement: function (params) {
            mApp.unblock(params.selector)
        }
        ,

        blockPage: function () {
            mApp.blockPage({
                overlayColor: "#000000",
                type: "loader",
                state: "success",
                message: "Подождите..."
            })
        }
        ,

        unblockPage: function () {
            mApp.unblockPage();
        }
        ,

        ajaxGet: function (params) {
            $.ajax({
                method: 'get',
                url: params.url,
                dataType: 'json',

                beforeSend: function () {
                    app.functions.blockPage();
                },

                success: function (response) {

                    if (response.functions) {
                        $.each(response.functions, function (funcName, data) {
                            app.functions[funcName](data.params)
                        });
                    }

                    app.functions.unblockPage();
                },

                error: function () {
                    app.functions.unblockPage();
                    app.functions.unblockElement(app.lastBlockedElement)
                },
                complete: function () {
                    app.functions.unblockPage();
                }
            });
        }
        ,

        ajaxPost: function (form) {
            let url = form.attr('action');
            let formId = form.attr('id');
            let formData = new FormData(form[0]);

            $.ajax({
                method: 'post',
                url: url,
                data: formData,
                dataType: 'json',
                async: true,
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },


                beforeSend: function () {
                    app.functions.validationReset();
                },

                success: function (response) {

                    if (response.functions) {
                        $.each(response.functions, function (funcName, data) {
                            app.functions[funcName](data.params)
                        });
                    }

                    app.functions.unblockPage();

                    app.functions.unblockElement(app.lastBlockedElement)

                },

                error: function (response) {

                    app.functions.unblockPage();
                    app.functions.unblockElement(app.lastBlockedElement);

                    if (response.status == 422) {

                        app.functions.validationFail({response: response.responseJSON, formId: formId});
                    }

                    if (response.responseJSON.functions) {
                        $.each(response.responseJSON.functions, function (funcName, data) {
                            app.functions[funcName](data.params)
                        });
                    }



                },
                complete: function () {
                    app.functions.unblockPage();

                    app.functions.unblockElement(app.lastBlockedElement)

                }
            });
        },

        initEditor: function () {
            $('.editor').each(function () {
                let height = $(this).attr('data-editor-height');
                editor = CKEDITOR.replace($(this).attr('id'), {

                    height: (height) ? height : 500
                });

                editor.ui.addButton('FileManager', {
                    label: "Менеджер файлов",
                    command: 'showFileManager',
                    toolbar: 'insert',
                    icon: '/core/js/vendors/ckeditor/image_file.png'
                });

                editor.addCommand("showFileManager", {
                    exec: function (edt) {
                        app.functions.editorShowObjects(edt);
                    }
                });
            });
        },

        initDatePicker: function()
        {
            if ($('.dtepkr').length)
            {
                $('.dtepkr').datepicker({
                    autoclose: true,
                    format: 'dd.mm.yyyy',
                })
            }

        },

        initDateTimePicker: function()
        {
            if ($('.dpt').length)
            {
                $('.dpt').datetimepicker({
                    todayHighlight: true,
                    autoclose: true,
                    format: 'dd.mm.yyyy hh:ii',
                })
            }

        },

        initSelect2: function()
        {
            if ($('.select2').length)
            {
                $('.select2').select2();
            }
        },


        editorShowObjects: function(edt)
        {
            app.vars.editor = edt;
            let url = $('meta[name="editor-objects-url"]').attr('content');
            app.functions.ajaxGet({url: url});
        }
    }

}
