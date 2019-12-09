<tr class="row-{{$item->id}}">
    @foreach($columns as $column)

        <td class="{!! $column->getAlign() !!}" @if($column->getWidth()) width="{!! $column->getWidth() !!}" @endif>
            {!! $column->getValue($item) !!}
        </td>

    @endforeach
</tr>