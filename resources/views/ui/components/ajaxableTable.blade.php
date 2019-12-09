<table class="table table-bordered ajax-content" data-url="{!! $ajaxTable->getUrl() !!}" id="{!! $ajaxTable->getId() !!}">
    <thead>
    <tr>
        @foreach($ajaxTable->getColumns() as $column)
            <th class="{!! $column['align'] !!}" @if($column['width']) width="{!! $column['width'] !!}" @endif>{!! $column['title'] !!}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<div class="pagination_placeholder"></div>