<tr class="row-{{ $item->id }}">
    <td>{{ $item->id }}</td>
    <td>{{ $item->admin->name ?? '-' }}</td>
    <td>{{ $item->type == 'all' ? 'Всем' : 'По списку' }}</td>
    <td>{{ $item->title }}</td>
    <td>{{ $item->message }}</td>
    <td>{!! $item->user_list_file
            ? '<a href="' . route('admin.notifications.custom-file', ['id' => $item->id]) . '">Скачать</a>'
            : ''  !!}</td>
    <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
</tr>
