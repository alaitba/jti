<tr class="row-{{ $item->id }}">
    <td>{{ $item->id }}</td>
    <td>{{ $item->brand}}</td>
    <td>{!! $item->photos_count ? $item->photos_count : '<span class="text-danger">0</span>' !!}</td>
    <td>
        <a
            style="text-decoration: none"
            href="#"
            class="handle-click"
            data-url="{{ route('admin.brands.edit', ['id' => $item->id]) }}"
            data-type="modal"
            data-modal="largeModal"
        ><i class="la la-edit"></i></a>
    </td>
</tr>
