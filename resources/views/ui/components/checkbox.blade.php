<div class="m-checkbox-list">
    <label class="m-checkbox">
        <input type="checkbox"  name="{!! $checkbox->getName() !!}" @if($form->hasData() && $form->isDataFieldTrue($checkbox->getName())) checked @endif> {!! $checkbox->getLabel() !!}
        <span></span>
    </label>
</div>