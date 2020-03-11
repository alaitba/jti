<tr class="row-{{ $item->id }}">
    <td>{{ $item->id }}</td>
    <td>{{ $item->title }}</td>
    <td class="text-center">
        <a
            style="text-decoration: none"
            href="#"
            class="handle-click"
            data-url="{{ route('admin.feedback.topics.edit', ['topicId' => $item->id]) }}"
            data-type="modal"
            data-modal="regularModal"
        ><i class="la la-edit"></i></a>
    </td>
    <td class="text-center">
        <a href="#" class="handle-click" data-type="confirm"
           title="Удалить тему"
           data-title="Удаление"
           data-message="Вы уверены, что хотите удалить тему?"
           data-cancel-text="Нет"
           data-confirm-text="Да, удалить" data-url="{{ route('admin.feedback.topics.delete', ['topicId' => $item->id ]) }}">
            <i class="la la-trash"></i>
        </a>
    </td>
</tr>
