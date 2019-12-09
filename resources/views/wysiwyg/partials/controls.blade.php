<div class="pull-right">

    <a href="#"  class="btn btn-sm btn-default handle-click" data-type="trigger-hidden-input" data-input-id="#fileInput" ><i class="fa fa-upload"></i> Загрузить</a>
    <a href="#"  class="btn btn-sm btn-default handle-click" data-url="{{$folderCreateUrl}}" data-type="modal" data-modal="manageFolderModal"><i class="fa fa-folder-open-o"></i> Создать папку</a>

  <form action="{{$uploadUrl}}" method="post" class="ajax" id="wysiwygUploadForm" data-progress-bar="#editorModal .progress">
    <input type="file" name="uploads[]" style="display: none" class="form-input-image" id="fileInput" multiple="multiple">
  </form>
</div>


