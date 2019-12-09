<tr class="row-{{ $item->id }}"  @if(isset($loop))data-index="{{$loop->iteration}}"@endif>
    <td class="text-center align-middle">{{ $item->id }}</td>
    <td class="align-middle">{{ $item->name }}</td>
    <td class="text-left">{{ $item->email }}</td>
    <td class="text-center">
        <i class="la la-power-off" style="color:@if($item->active) green; @else red;@endif"></i>
    </td>
    <td class="text-center">
        <i class="la la-magic" style="color:@if($item->super_user) green; @else red;@endif"></i>
    </td>
    <td class="text-center">
        <i class="la la-code" style="color:@if($item->develop) green; @else red;@endif"></i>
    </td>
    <td class="text-center">
        <a href="#" data-url="{{ route('admin.admins.edit', ['id' => $item->id ]) }}" class="handle-click" data-type="modal" data-modal="largeModal">
            <i class="la la-edit"></i>
        </a>
    </td>
    <td class="text-center">
        <a href="#" class="handle-click" data-type="confirm"
           title="Удалить аккаунт"
           data-title="Удаление"
           data-message="Вы уверены, что хотите удалить аккаунт?"
           data-cancel-text="Нет"
           data-confirm-text="Да, удалить" data-url="{{ route('admin.admins.delete', ['id' => $item->id ]) }}">
            <i class="la la-trash"></i>
        </a>
    </td>
</tr>