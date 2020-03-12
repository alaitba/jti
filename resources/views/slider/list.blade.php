@foreach($items as $item)
    @include('slider.item')
@endforeach

@if(!$items->count())
    <tr>
        <td colspan="4" class="text-center">Слайдер пуст</td>
    </tr>
@endif
