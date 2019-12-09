<div>
    <a href="#" class="btn btn-info btn-sm openFileBrowser" data-input-id="#hiddenInputFile" >Добавить изображения</a>
    <form action="{{$formAction}}" method="post" id="mediaForm" class="ajax-submit" >
        <input type="file" name="images[]" multiple="multiple" accept="image/*" id="hiddenInputFile"  style="display: none">
    </form>
    <div class="progress m-progress--sm" id="progressBar" style="margin-top: 5px; display: none">
        <div class="progress-bar m--bg-primary" role="progressbar" style="width: 0%; " aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
<div class="media-list" style="margin-top: 20px">
    @include('backend.common.media.images')
</div>