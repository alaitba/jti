<form action="{{ $formAction }}" method="post" class="ajax"
      data-ui-block-type="element" data-ui-block-element="#regularModal .modal-body" id="ajaxForm">
    <ul class="nav nav-tabs" role="tablist">
        @foreach(config('project.locales') as $count => $locale)
            <li role="presentation" class="nav-item">
                <a class="@if($count == 0) active @endif nav-link" href="#tab-{{ $count }}"
                   aria-controls="#tab-{{ $count }}" role="tab"
                   data-toggle="tab">{{ $locale }}</a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach(config('project.locales') as $count => $locale)
            <div role="tabpanel" class="tab-pane @if($count == 0)  active  @endif " id="tab-{{ $count }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group ">
                            <label for="title.{{ $locale }}">Заголовок *</label>
                            <input type="text" autocomplete="off" class="form-control m-input m-input--square" id="title.{{ $locale }}" name="title[{{ $locale }}]"
                                   @if(isset($item)) value="{{ $item->getTranslation('title', $locale) }}" @endif>
                            <p class="help-block"></p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>



        <button type="submit" class="btn btn-brand btn-sm">{{ $buttonText }}</button>
        <button type="button" class="btn btn-accent btn-sm float-right" data-dismiss="modal">Отмена</button>
</form>
