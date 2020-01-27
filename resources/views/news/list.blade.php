@foreach($items as $item)
    @include('news.item')
@endforeach

@if(!$items->count())
    <tr>
        <td colspan="4" class="text-center">Новостей нет</td>
    </tr>
@endif
