@foreach($items as $item)
    @include('settings.localisation.groups.items.item')
@endforeach

@if(!$items->count())
    <tr>
        <td colspan="4" class="text-center">Данных не найдено</td>
    </tr>
@endif