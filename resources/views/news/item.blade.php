<tr class="row-{{ $item->id }}">
    <td class="text-center align-middle">{{ $item->id }}</td>

    <td class="align-middle">{{ $item->title }}</td>
    <td class="align-middle">{!! $item->target !!}</td>
    <td class="align-middle">{{ $item->period }}</td>
    <td class="text-center align-middle">
        <a href="#" data-url="{{ route('admin.news.edit', ['id' => $item->id ]) }}" class="handle-click" data-type="modal" data-modal="superLargeModal">
            <i class="la la-edit"></i>
        </a>

        <a href="#" class="handle-click" data-type="confirm"
           title="Удалить новость"
           data-title="Удаление"
           data-message="Вы уверены, что хотите удалить новость?"
           data-cancel-text="Нет"
           data-confirm-text="Да, удалить" data-url="{{ route('admin.news.delete', ['id' => $item->id ]) }}">
            <i class="la la-trash"></i>
        </a>
    </td>
</tr>
