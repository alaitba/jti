<fieldset>
    <legend>Список ролей</legend>
    <ul class="list-group m-scrollable" id="rolesListPlaceholder"  data-scrollable="true" data-max-height="360">
        @foreach($roles as $role)
            <li class="list-group-item role-item-{{$role->id}}">
                <a class="role-item handle-click"
                   data-type="ajax-get"
                   href="#" data-url="{{ route('admin.admins.roles.edit', ['roleId' => $role->id]) }}">{{$role->name}}</a>
            </li>
        @endforeach
    </ul>
</fieldset>