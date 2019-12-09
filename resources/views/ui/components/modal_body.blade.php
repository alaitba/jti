

@foreach($modal->getComponents() as $components)

    @if(count($components) > 1)
        <div class="row">
            @foreach($components as $component)
                <div class="col-md-{{12/count($components)}}">{!! $component->getContent() !!}</div>
            @endforeach
        </div>


    @else
        {!! $components[0]->getContent() !!}
    @endif
@endforeach