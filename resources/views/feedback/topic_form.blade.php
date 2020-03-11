<form action="{{ $formAction }}" method="post" class="ajax"
      data-ui-block-type="element" data-ui-block-element="#regularModal .modal-body" id="ajaxForm">
        <div class="form-group ">
            <label for="title">Заголовок *</label>
            <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="title" name="title" value="{{ $item->title ?? '' }}">
            <p class="help-block"></p>
        </div>

        <button type="submit" class="btn btn-brand btn-sm">{{ $buttonText }}</button>
        <button type="button" class="btn btn-accent btn-sm float-right" data-dismiss="modal">Отмена</button>
</form>
