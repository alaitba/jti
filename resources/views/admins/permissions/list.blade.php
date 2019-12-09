<fieldset>
    <legend>Список прав</legend>
    <ul class="list-group m-scrollable" id="permissionsListPlaceholder"  data-scrollable="true" data-max-height="360">
        @foreach($permissions as $permission)
            <li class="list-group-item role-item-{{$permission->id}}">
                <a class="role-item handle-click"
                   data-type="ajax-get"
                   href="#" data-url="{{ route('admin.admins.permissions.edit', ['permissionId' => $permission->id]) }}">{{$permission->name}}</a>
            </li>
        @endforeach
    </ul>
</fieldset>