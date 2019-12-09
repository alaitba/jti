@extends('layouts.master')

@section('content')

    @foreach($rows as $columns)
        @if(count($columns) > 1)

           <div class="row">
               @foreach($columns as $component)
                   <div class="col-md-{{12 / count($columns)}}">
                       {!! $component->getContent() !!}
                   </div>
               @endforeach
           </div>
        @else

            {!! $columns[0]->getContent() !!}
        @endif
    @endforeach
@stop