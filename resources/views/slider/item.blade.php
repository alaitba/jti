@php
$last = \App\Models\Slider::query()->max('position');
@endphp
<tr class="row-{{ $item->id }}">
    <td class="align-middle"><img alt="" style="max-height:150px" src="{{ $item->image->url ?? '' }}"></td>
    <td class="align-middle"><a href="{{ $item->link }}">{{ $item->link }}</a></td>
    <td class="align-middle">
        <a href="#" data-url="{{ route('admin.slider.switchactive', ['id' => $item->id ]) }}" class="handle-click" data-type="ajax-get"
           style="text-decoration: none">
            <i class="la la-dot-circle-o {{ $item->active ? 'text-success' : 'text-danger' }}"></i>
        </a>
    </td>
    <td class="text-center align-middle">
        @if($item->position > 1)
            <a href="#" data-url="{{ route('admin.slider.move', ['id' => $item->id, 'direction' => 1 ]) }}" class="handle-click" data-type="ajax-get"
               style="text-decoration: none">
                <i class="la la-arrow-up"></i>
            </a>
        @endif
        @if($item->position < $last)
            <a href="#" data-url="{{ route('admin.slider.move', ['id' => $item->id, 'direction' => 0 ]) }}" class="handle-click" data-type="ajax-get"
               style="text-decoration: none">
                <i class="la la-arrow-down"></i>
            </a>
        @endif
        <a href="#" data-url="{{ route('admin.slider.edit', ['id' => $item->id ]) }}" class="handle-click" data-type="modal"
           data-modal="regularModal">
            <i class="la la-edit"></i>
        </a>

        <a href="#" class="handle-click" data-type="confirm"
           title="Удалить элемент слайдера"
           data-title="Удаление"
           data-message="Вы уверены, что хотите удалить элемент слайдера?"
           data-cancel-text="Нет"
           data-confirm-text="Да, удалить" data-url="{{ route('admin.slider.delete', ['id' => $item->id ]) }}">
            <i class="la la-trash"></i>
        </a>
    </td>
</tr>
