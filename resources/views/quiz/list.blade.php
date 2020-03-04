@foreach($items as $item)
    @include('quiz.item')
@endforeach

@if(!$items->count())
    <tr>
        <td colspan="7" class="text-center">Викторин и опросов нет</td>
    </tr>
@endif
