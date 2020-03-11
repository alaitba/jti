<tr class="row-{{ $item->id }}{{ $item->answer ? '' : ' bg-warning' }}">
    <td>{{ $item->id }}</td>
    <td>{{ $item->partner->mobile_phone ?? '-'}}</td>
    <td>{{ $item->topic->title ?? '-'}}</td>
    <td>{{ \Illuminate\Support\Str::limit($item->question) }}</td>
    <td>{{ \Illuminate\Support\Str::limit($item->answer) }}</td>
    <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
    <td>
        <a
            style="text-decoration: none"
            href="#"
            class="handle-click"
            data-url="{{ route('admin.feedback.edit', ['id' => $item->id]) }}"
            data-type="modal"
            data-modal="largeModal"
        ><i class="la la-edit"></i></a>
    </td>
</tr>
