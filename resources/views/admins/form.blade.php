<form action="{{ $formAction }}" method="post" class="ajax"
      data-ui-block-type="element" data-ui-block-element="#largeModal .modal-body" id="ajaxForm">
    <fieldset>
        <legend>Информация об администраторе</legend>

        <div class="form-group ">
            <label for="name">Имя *</label>
            <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="name" name="name"
                   @if(isset($item)) value="{{ $item->name }}" @endif>
            <p class="help-block"></p>
        </div>

        <div class="form-group">
            <label for="email">E-mail *</label>
            <input type="text" class="form-control  " id="email" name="email" placeholder="" @if(isset($item)) value="{{ $item->email }}" @endif>
            <p class="help-block"></p>
        </div>

        <div class="form-group">
            <label for="password">Пароль *</label>
            <input type="text" class="form-control  " id="password" name="password" @if(isset($item)) placeholder="Новый пароль, если хотите изменить" @endif autocomplete="off">
            <p class="help-block"></p>
        </div>

        <div class="form-group ">
            <fieldset>
                <label for="roles">Роли</label>
                <select class="roles_select2" name="roles[]" multiple="multiple" style="width: 100%">
                @if(isset($item))
                        @foreach($roles as $role)
                            <option value="{{ $role }}" selected="selected">{{ $role }}</option>
                        @endforeach
                    @endif
                </select>
                <p class="help-block"></p>
            </fieldset>
        </div>

        <fieldset>
            <legend>Настройки</legend>
            <div class="m-checkbox-list">
                <label class="m-checkbox">
                    <input type="checkbox" name="active" @if(isset($item) && $item->getOriginal('active')) checked @endif> Активен
                    <span></span>
                </label>
            </div>
            <div class="m-checkbox-list">
                <label class="m-checkbox">
                    <input type="checkbox" name="super_user" @if(isset($item) && $item->getOriginal('super_user')) checked @endif> Супер юзер
                    <span></span>
                </label>
            </div>
        </fieldset>

        <button type="submit" class="btn btn-brand btn-sm">{{ $buttonText }}</button>
        <button type="button" class="btn btn-accent btn-sm float-right" data-dismiss="modal">Отмена</button>
    </fieldset>
</form>

<script type="text/javascript">
    $('.roles_select2').select2({
        ajax: {
            url: '/rbkcp/admins/roles/api/all',
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
            placeholder: 'Поиск ролей...',
            minimumInputLength: 1,
        }
    });
</script>