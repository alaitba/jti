<form action="{{$formAction}}" method="post" class="ajax" id="localisationForm">
    <div class="form-group">
        <label for="name">Название перевода</label>
        <input type="text" name="name" id="name" class="form-control" @if(isset($item)) value="{{$item->name}}" disabled @endif>
        <p class="help-block"></p>
    </div>

    <fieldset>
        <legend>Значения</legend>
        @foreach($locales as $count => $locale)
            <div class="form-group">
                <label for="values.{{ $locale }}">{{strtoupper($locale)}}</label>
                <input type="text" class="form-control" id="values.{{ $locale }}" name="values[{{ $locale }}]"
                       @if(isset($item)) value="{{ $item->getTranslation('values', $locale) }}" @endif>
                <p class="help-block"></p>
            </div>
        @endforeach
    </fieldset>


    <button type="submit" class="btn btn-brand btn-sm">{{  $buttonText }} </button>
</form>