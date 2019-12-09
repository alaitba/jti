<form action="{!! $form->getAction() !!}" method="{!! $form->getMethod() !!}" id="{!! $form->getId() !!}"
      @if($form->isAjaxable()) class="ajax" @endif data-ui-block-type="element" data-ui-block-element=".modal-body">

    @foreach($form->getComponents() as $components)

        @if(count($components) > 1)
            <div class="row">
                @foreach($components as $component)
                    @if(!$component->isTabeable())
                        <div class="col-md-{{12/count($components)}}">
                            @include('ui.components.form_component', ['form' => $form, 'component' => $component])
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            @include('ui.components.form_component', ['form' => $form, 'component' => $components[0]])
        @endif
    @endforeach

    @if ($form->isLocalable())
        <ul class="nav nav-tabs" role="tablist">
            @foreach($form->getLocales() as $count => $locale)
                <li role="presentation" class="nav-item">
                    <a class="@if($count == 0) active @endif nav-link" href="#tab-{{ $count }}"
                       aria-controls="#tab-{{ $count }}" role="tab"
                       data-toggle="tab">{{ $locale }}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content">
            @foreach($form->getLocales() as $count => $locale)
                <div role="tabpanel" class="tab-pane @if($count == 0)  active  @endif " id="tab-{{ $count }}">

                    @foreach($form->getComponents() as $components)

                        @if(count($components) > 1)
                            <div class="row">
                                @foreach($components as $component)
                                    @if($component->isTabeable())
                                        <div class="col-md-{{12/count($components)}}">
                                            @include('ui.components.form_component_tabeable', ['form' => $form, 'locale' => $locale, 'component' => $component])
                                        </div>
                                    @endif
                                @endforeach
                            </div>


                        @else
                            @include('ui.components.form_component_tabeable', ['form' => $form, 'locale' => $locale, 'component' => $components[0]])

                        @endif
                    @endforeach


                </div>
            @endforeach
        </div>

    @else

        @foreach($form->getComponents() as $components)

            @if(count($components) > 1)
                <div class="row">
                    @foreach($components as $component)
                        <div class="col-md-{{12/count($components)}}">
                            @include('ui.components.form_component_tabeable', ['form' => $form, 'component' => $component])
                        </div>
                    @endforeach
                </div>


            @else
                @include('ui.components.form_component_tabeable', ['form' => $form, 'component' => $components[0]])
            @endif
        @endforeach

    @endif



    @if($form->hasOptions())
            <fieldset>
                <legend>{!! $form->getOptionsTitle() !!}</legend>
                @foreach($form->getOptionsComponents() as $component)
                    @switch(get_class($component))
                        @case('StarterKit\Core\Ui\Components\Form\Checkbox')
                        @include('ui.components.checkbox', ['checkbox' => $component])
                        @break
                        @case('StarterKit\Core\Ui\Components\Form\Input')
                        @include('ui.components.input', ['input' => $component])
                        @break;
                    @endswitch
                @endforeach
            </fieldset>

    @endif

    <button type="submit" class="btn btn-sm btn-success">{!! $form->getSubmitButtonText() !!}</button>

</form>
