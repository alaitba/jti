
<tr class="row-{{ $item->id }}">
    <td class="text-center">{{ $item->id }}</td>
    <td>{{ $item->partner->current_contact->name }}</td>
    <td>{{ $item->partner->mobile_phone }}</td>
    <td>{{ $item->quiz->title ?? '' }}</td>
    <td>{{ $item->created_at->format('d.m.Y H:i')}}</td>
    <td>{{ $item->amount }}</td>
    <td class="text-center"><i class="la {{ $item->success ? 'la-check text-success' : 'la-ban text-danger' }}"></i></td>
    <td class="text-center">
        <a href="#"
           title="Посмотреть ответы"
           data-url="{{ route('admin.reports.quizzes.view', ['id' => $item->id ]) }}"
           class="handle-click"
           data-type="modal"
           data-modal="regularModal"><i class="la la-eye"></i></a>
    </td>
</tr>
