@if(isset($locale))
<div class="form-group">
    @if($input->isLocaleable())
        <label for="{!! $input->getName() !!}.{{ $locale }}"  >{!! $input->getLabel() !!}</label>
        <input type="{!! $input->getType() !!}" class="form-control @if($input->isDatePicker()) dtepkr @endif @if($input->isDateTimePicker()) dpt @endif" id="{!! $input->getName() !!}.{{ $locale }}" name="{!! $input->getName() !!}[{{ $locale }}]" placeholder="{{$input->getPlaceholder()}}"  @if($form->hasData() && $input->hasValue()) value="{{$form->getDataTextFieldWithLocale($input->getName(), $locale)}}" @endif @if($input->isDisableable()) disabled @endif>
    @else
        <label for="{!! $input->getName() !!}" >{!! $input->getLabel() !!}</label>
        <input type="{!! $input->getType() !!}" class="form-control @if($input->isDatePicker()) dtepkr @endif @if($input->isDateTimePicker()) dpt @endif"   id="{!! $input->getName() !!}" name="{!! $input->getName() !!}" placeholder="{{$input->getPlaceholder()}}" @if($form->hasData() && $input->hasValue()) value="{!! $form->getDataTextField($input->getName()) !!}" @endif @if($input->isDisableable()) disabled @endif>
    @endif
    <p class="help-block"></p>
</div>
@else
    <div class="form-group">
        <label for="{!! $input->getName() !!}" >{!! $input->getLabel() !!}</label>
        <input type="{!! $input->getType() !!}" class="form-control @if($input->isDatePicker()) dtepkr @endif @if($input->isDateTimePicker()) dpt @endif"   id="{!! $input->getName() !!}" name="{!! $input->getName() !!}" placeholder="{{$input->getPlaceholder()}}" @if($form->hasData() && $input->hasValue()) value="{!! $form->getDataTextField($input->getName()) !!}" @endif @if($input->isDisableable()) disabled @endif>
        <p class="help-block"></p>
    </div>
@endif