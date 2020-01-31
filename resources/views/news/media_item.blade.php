<div class="col-md-6 image-{{$media->id}}" style="margin-top: 5px;margin-bottom: 5px">
    <div class="text-center">
        <img style="max-width: 100%;" class="img-thumbnail media-item" src="{{$media->url}}" alt="нет медиа">
    </div>
    <div class="controls" style="margin-top: 10px;">
        <a style="text-decoration:none" href="#" class="delete-media-data btn btn-sm btn-danger pull-right" data-url="{{ route('admin.news.media.delete',['newsId'=>$media->id]) }}" data-type="confirm" data-title="Удаление" data-message="Вы уверены, что хотите удалить?"><i class="la la-trash-o"></i></a>
    </div>
</div>
