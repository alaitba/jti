<tr class="row-{{ $item->id }}">
    <td>{{ $item->crm_id }}</td>
    <td>{!! $item->names !!}</td>
    <td>{{ $item->price }}</td>
    <td>{{ $item->qty }}</td>
    <td>{!! $item->has_desc !!}</td>
    <td>{!! $item->photos_count ? $item->photos_count : '<span class="text-danger">0</span>' !!}</td>
    <td>
        <a
            style="text-decoration: none"
            href="#"
            class="handle-click"
            data-url="{{ route('admin.rewards.edit', ['id' => $item->id]) }}"
            data-type="modal"
            data-modal="largeModal"
        ><i class="la la-edit"></i></a>
    </td>
</tr>
