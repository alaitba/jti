<tr class="row-{{ $item->id }}">
    <td class="text-center align-middle">{{ $item->id }}</td>
    <td class="align-middle">{{ $item->type_string }}</td>
    <td class="align-middle">{{ $item->title }}</td>
    <td class="align-middle">{{ $item->period }}</td>
    <td class="align-middle">{{ $item->amount }}</td>
    <td class="align-middle">{!! $item->target !!}</td>
    <td class="text-center align-middle">
        <a href="{{ route('admin.quizzes.questions.index', ['quizId' => $item->id ]) }}" title="Вопросы"><i class="la la-question-circle"></i></a>
        <a href="#" title="Редактировать" data-url="{{ route('admin.quizzes.edit', ['id' => $item->id ]) }}" class="handle-click" data-type="modal" data-modal="regularModal"><i class="la la-edit"></i></a>
        <a href="#" class="handle-click" data-type="confirm"
           title="Удалить {{ $item->type == 'quiz' ? 'викторину' : 'опрос' }}"
           data-title="Удаление"
           data-message="Вы уверены, что хотите удалить {{ $item->type == 'quiz' ? 'викторину' : 'опрос' }}?"
           data-cancel-text="Нет"
           data-confirm-text="Да, удалить" data-url="{{ route('admin.quizzes.delete', ['id' => $item->id ]) }}"><i class="la la-trash"></i></a>
        <a href="#" class="handle-click" data-type="confirm"
           title="Скопировать {{ $item->type == 'quiz' ? 'викторину' : 'опрос' }}"
           data-title="Копирование"
           data-message="Вы уверены, что хотите скопировать {{ $item->type == 'quiz' ? 'викторину' : 'опрос' }}?"
           data-cancel-text="Нет"
           data-confirm-text="Да, скопировать" data-url="{{ route('admin.quizzes.copy', ['id' => $item->id ]) }}"><i class="la la-plus-circle"></i></a>
    </td>
</tr>
