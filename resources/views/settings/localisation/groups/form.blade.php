<form action="{{$formAction}}" method="post" class="ajax" id="LocalisationGroupForm" >
    <div class="form-group">
        <label for="name">Название группы</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Например: Интерфейс" @if(isset($item)) value="{{$item->name}}" @endif>
        <p class="help-block"></p>
    </div>

    <button type="submit" class="btn btn-brand btn-sm">{{  $buttonText }} </button>
</form>