<fieldset>
    <legend>{{$legend}}</legend>
    <form action="{{$formAction}}" method="post" id="wysiwygFolderForm" class="ajax" data-ui-block-type="element" data-ui-block-element="#manageFolderModal .modal-body">
        <div class="form-group">
            <label for="name">Название папки</label>
            <input type="text" name="name" id="name" class="form-control" autocomplete="off" @if(isset($folder)) value="{{$folder->getTranslation('name', 'ru')}}" @endif>
            <p class="help-block"></p>
        </div>

        <button type="submit" class="btn btn-sm btn-success">{{$submitBtnText}}</button>
    </form>
</fieldset>