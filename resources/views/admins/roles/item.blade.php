<tr class="row-{{ $item->id }}"  @if(isset($loop))data-index="{{$loop->iteration}}"@endif>
    <td class="text-center align-middle">{{ $item->id }}</td>
    <td class="align-middle">{{ $item->name }}</td>
    <td class="text-center">{{ $item->created_at }}</td>
    <td class="text-center">
        <a href="#" data-url="{{ route('admin.admins.roles.edit', ['roleId' => $item->id ]) }}" class="handle-click" data-type="modal" data-modal="largeModal">
            <i class="la la-edit"></i>
        </a>
    </td>
    <td class="text-center">
        <a href="#" class="handle-click" data-type="confirm"
           title="Удалить роль"
           data-title="Удаление"
           data-message="Вы уверены, что хотите удалить роль?"
           data-cancel-text="Нет"
           data-confirm-text="Да, удалить" data-url="{{ route('admin.admins.roles.delete', ['roleId' => $item->id ]) }}">
            <i class="la la-trash"></i>
        </a>
    </td>
</tr>