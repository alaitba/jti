@switch(get_class($component))

    @case('StarterKit\Core\Ui\Components\Form\Fieldset')
        @if($component->isTabeable())
            <fieldset>
                <legend>{!! $component->getTitle() !!}</legend>
                @foreach($component->getComponents() as $componentItem)
                    @switch(get_class($componentItem))
                        @case('StarterKit\Core\Ui\Components\Form\Input')
                        @include('ui.components.input', ['input' => $componentItem])
                        @break;

                        @case('StarterKit\Core\Ui\Components\Form\Textarea')
                        @include('ui.components.textarea', ['textarea' => $componentItem])
                        @break;
                    @endswitch
                @endforeach
            </fieldset>

            @break;
        @endif
@endswitch