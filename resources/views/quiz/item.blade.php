<tr class="row-{{ $item->id }}">
    <td class="text-center align-middle">{{ $item->id }}</td>

    <td class="align-middle">{{ $item->type_string }}</td>
    <td class="align-middle">{{ $item->title }}</td>
    <td class="align-middle">{{ $item->period }}</td>
    <td class="align-middle">{{ $item->amount }}</td>
    <td class="align-middle">{!! $item->target !!}</td>
    <td class="text-center align-middle">
        <a href="#" data-url="{{ route('admin.quizzes.edit', ['id' => $item->id ]) }}" class="handle-click" data-type="modal" data-modal="regularModal">
            <i class="la la-edit"></i>
        </a>
    </td>
</tr>
