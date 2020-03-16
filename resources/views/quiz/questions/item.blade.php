<tr class="row-{{ $question->id }}">

    <td class="text-left">
        {{ $question->question }}
    </td>

    <td class="text-center">
        {{ $question->type_string }}
    </td>

    <td class="text-center">
        <a
            style="text-decoration:none"
            href="#"
            class="handle-click"
            data-url="{{ route('admin.quizzes.questions.edit', ['quizId' => $quizId, 'id' => $question->id]) }}"
            data-type="modal"
            data-modal="largeModal"
        ><i class="la la-edit"></i></a>
    </td>

    <td class="text-center">
        <a
            style="text-decoration:none"
            href="#" class="handle-click"
            data-url="{{ route('admin.quizzes.questions.delete', ['quizId' => $quizId, 'id' => $question->id]) }}"
            data-type="confirm"
            data-title="Удаление"
            data-message="Вы уверены, что хотите удалить"
        ><i class="la la-trash-o"></i></a>
    </td>

</tr>
