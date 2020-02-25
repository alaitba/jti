<tr class="row-{{ $item->id }}">
    <td>{{ $item->id }}</td>
    <td>{{ $item->admin->name ?? '-' }}</td>
    <td>{{ $item->type == 'all' ? 'Всем' : 'По списку' }}</td>
    <td>{{ $item->title }}</td>
    <td>{{ $item->message }}</td>
    <td>{!! $item->user_list_file
            ? '<a href="' . \Illuminate\Support\Facades\Storage::disk('local')->url($item->user_list_file) . '">Скачать</a>'
            : ''  !!}</td>
    <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
</tr>
