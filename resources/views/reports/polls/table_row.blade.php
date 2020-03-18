<tr class="row-{{ $item->id }}">
    <td class="text-center">{{ $item->id }}</td>
    <td>{{ $item->partner->current_contact->name }}</td>
    <td>{{ $item->quiz->title ?? '' }}</td>
    <td>{{ $item->created_at->format('d.m.Y H:i')}}</td>
    <td class="text-center">
        <a href="#"
           title="Посмотреть ответы"
           data-url="{{ route('admin.reports.polls.view', ['id' => $item->id ]) }}"
           class="handle-click"
           data-type="modal"
           data-modal="regularModal"><i class="la la-eye"></i></a>
    </td>
</tr>
