<div class="m-portlet">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
                {!! $portlet->getIcon() !!}
                <h3 class="m-portlet__head-text">{!! $portlet->getTitle() !!}</h3>
            </div>
        </div>
        <div class="m-portlet__head-tools">
            <ul class="m-portlet__nav">
                @foreach($portlet->getControls() as $control)
                    {!! $control->render() !!}
                @endforeach
            </ul>

        </div>
    </div>
    <div class="m-portlet__body">


        @foreach($portlet->getComponents() as $components)

            @if(count($components) > 1)
                    <div class="row">
                    @foreach($components as $component)
                            <div class="col-md-{{12/count($components)}}">{!! $component->getContent() !!}</div>
                    @endforeach
                </div>


            @else
                {{--{{dd($components[0])}}--}}
             {!! $components[0]->getContent() !!}
            @endif
        @endforeach


    </div>
</div>