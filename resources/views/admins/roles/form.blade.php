<form action="{{ $formAction }}" method="post" class="ajax"
      data-ui-block-type="element" data-ui-block-element="#largeModal .modal-body" id="ajaxForm">
    <fieldset>
        <legend>Информация о роли</legend>

        <div class="form-group ">
            <label for="name">Название *</label>
            <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="name" name="name"
                   @if(isset($roleItem)) value="{{ $roleItem->name }}" @endif>
            <p class="help-block"></p>
        </div>

        @if(isset($roleItem))
        <div class="form-group ">
            <label for=guard_name">Имя защитника</label>
            <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="guard_name" name="guard_name"
                   value="{{ $roleItem->guard_name }}" disabled>
            <p class="help-block"></p>
        </div>
        @endif

        <div class="form-group ">
            <fieldset>
                <label for="permissions">Права</label>
                <select class="permissions_select2" name="permissions[]" multiple="multiple" style="width: 100%">
                    @if(isset($roleItem))
                        @foreach($permissions as $permission)
                            <option value="{{ $permission }}" selected="selected">{{ $permission }}</option>
                        @endforeach
                    @endif
                </select>
                <p class="help-block"></p>
            </fieldset>
        </div>

        <button type="submit" class="btn btn-brand btn-sm">{{ $buttonText }}</button>
        <button type="button" class="btn btn-accent btn-sm float-right" data-dismiss="modal">Отмена</button>
    </fieldset>
</form>

<script type="text/javascript">
    $('.permissions_select2').select2({
        ajax: {
            url: '/admins/permissions/api/all',
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: $.map(data.data, function (d) {
                        d.id = d.id;
                        d.text = d.name;

                        return d;
                    }),
                    pagination: {
                        more: data.next_page_url
                    }
                };
            },
            placeholder: 'Поиск прав...',
            minimumInputLength: 1,
        }
    });
</script>
