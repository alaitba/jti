<form action="{{ $formAction }}" method="post" class="ajax" data-ui-block-type="element"
      data-ui-block-element="#largeModal .modal-body" id="ajaxForm">
    <div class="m-portlet__body">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="name">Название класса</label>
                    <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="name" name="name">
                    <p class="help-block"></p>
                </div>
                <div class="form-group">
                    <label for="display_name">Название шаблона письма</label>
                    <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="display_name" name="display_name">
                    <p class="help-block"></p>
                </div>
                <div class="form-group">
                    <label for="params">Параметры</label>
                    <textarea type="text" class="form-control m-input m-input--square" id="params" name="params"></textarea>
                    <p class="help-block"></p>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-brand btn-sm">{{ $buttonText }} </button>
        <button type="button" class="btn btn-outline-accent btn-sm float-right" data-dismiss="modal">Отмена</button>
    </div>
</form>



