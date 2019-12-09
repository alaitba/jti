@if(isset($locale))
    <div class="form-group">
        <label for="{!! $textarea->getName() !!}.{{ $locale }}">{!! $textarea->getLabel() !!}</label>
        <textarea  id="{!! $textarea->getName() !!}.{{ $locale }}" name="{!! $textarea->getName() !!}[{{ $locale }}]"  class="form-control @if($textarea->isEditorable()) editor @endif">@if($form->hasData()) {!! $form->getDataTextFieldWithLocale($textarea->getName(), $locale) !!} @endif</textarea>
        <p class="help-block"></p>
    </div>
@else
    <div class="form-group">
        <label for="{!! $textarea->getName() !!}">{!! $textarea->getLabel() !!}</label>
        <textarea  id="{!! $textarea->getName() !!}" name="{!! $textarea->getName() !!}" rows="{!! $textarea->getTextareRowsNumber() !!}"  class="form-control @if($textarea->isEditorable()) editor @endif">@if($form->hasData()) {!! $form->getDataTextField($textarea->getName()) !!} @endif</textarea>
        <p class="help-block"></p>
    </div>
@endif