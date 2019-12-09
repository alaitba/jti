<form action="{{ $formAction }}" method="post" class="ajax"
      data-ui-block-type="element" data-ui-block-element="#largeModal .modal-body" id="ajaxForm">
    <fieldset>
        <legend>Информация о правах</legend>

        <div class="form-group ">
            <label for="name">Название *</label>
            <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="name" name="name"
                   @if(isset($permissionItem)) value="{{ $permissionItem->name }}" @endif>
            <p class="help-block"></p>
        </div>

        @if(isset($permissionItem))
        <div class="form-group ">
            <label for=guard_name">Имя защитника</label>
            <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="guard_name" name="guard_name"
                   value="{{ $permissionItem->guard_name }}" disabled>
            <p class="help-block"></p>
        </div>
        @endif
        <button type="submit" class="btn btn-brand btn-sm">{{ $buttonText }}</button>
        <button type="button" class="btn btn-accent btn-sm float-right" data-dismiss="modal">Отмена</button>
    </fieldset>
</form>