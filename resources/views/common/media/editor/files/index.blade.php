<div style="height: 2px;">
    <div class="progress" id="progressBar" style="margin-right: 5px; margin-left: 5px;">
        <div class="progress-bar m--bg-info" role="progressbar" style="width: 0; height: 1px;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
<div class="controls shadow" >

    <form action="{{route('admin.media.editor.files.upload')}}" method="post" id="editorForm">
        <input type="file" name="files[]" class="form-control input-file" multiple="multiple">
    </form>
</div>

<div class="galley-content" style="margin-top: 20px; max-height: 400px; overflow: scroll;">
    @include('backend.common.media.editor.files.files')
</div>