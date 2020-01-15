<div class="row">
    <div class="col-md-8">
        <form action="{{ $formAction }}" method="post" class="ajax" id="rewardForm">
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
                                <fieldset>
                                    <legend>Информация о призе</legend>
                                    <div class="form-group">
                                        <label for="name.{{ $locale }}">Название *</label>
                                        <input type="text" class="form-control" id="name.{{ $locale }}" name="name[{{ $locale }}]" value="{{$item->getTranslation('name', $locale)}}" @if($locale == 'ru') readonly @endif>
                                        <p class="help-block"></p>
                                    </div>
                                    <div class="form-group">
                                        <label for="description.{{ $locale }}">Описание</label>
                                        <textarea id="description.{{ $locale }}" name="description[{{ $locale }}]"  class="form-control editor">{{$item->getTranslation('description', $locale)}}</textarea>
                                        <p class="help-block"></p>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-sm btn-success">{{ $buttonText }}</button>
        </form>
    </div>
    <div class="col-md-4 media-div">
        <fieldset>
            <legend>Изображения</legend>
            <form action="{{ route('admin.rewards.media', ['rewardId' => $item->id]) }}"
                  method="post"
                  id="formImage">
                @csrf
                <input type="file" name="image[]" class="form-input-image-media" style="display: none"
                       accept="image/x-png,image/gif,image/jpeg,image/svg"
                       multiple>
                <button type="button" class="btn btn-success btn-sm add-photo">Добавить фото</button>
            </form>
            <div class="media-block">
                @foreach($photos as $row)
                    <div class="row">
                        @foreach($row as $photo)
                            @include('rewards.media.media_item')
                        @endforeach
                    </div>
                @endforeach
            </div>
        </fieldset>
    </div>
</div>
<script>
    $('.editor').each(function () {
        const height = $(this).attr('data-editor-height');
        CKEDITOR.replace($(this).attr('id'), {
            height: (height) ? height : 150,
            toolbar: [
                { name: 'document', items: [ 'Source', '-' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            ]
        });
    });
</script>
