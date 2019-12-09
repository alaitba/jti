@foreach($media as $row)
    <div class="row">
        @foreach($row as $image)
            <div class="col-md-3 text-center media-item" style="margin-bottom: 10px">

                @if(file_exists(storage_path('app/public/media/256_' . $image->getOriginal('original_file_name'))))
                    <img class="img-thumbnail" src="{{asset('storage/media/256_' . $image->getOriginal('original_file_name'))}}">
                @else
                    <img src="{{asset('storage/media/' . $image->getOriginal('original_file_name'))}}">
                @endif
                <div class="text-center" style="margin-top: 5px">

                        <a href="{{route('admin.media.model.main', ['mediaId' => $image->id])}}" @if($image->main_image) style="display: none" @endif class="btn btn-info btn-sm media-set-main"><i class="fa fa-star-o"></i></a>

                    <a href="{{route('admin.media.model.delete', ['mediaId' => $image->id])}}" class="btn btn-danger btn-sm delete-media" data-confirm-title="Удаление" data-confirm-message="Вы действительно хотите удалить изображение?"><i class="fa fa-trash-o"></i></a>
                </div>
            </div>
        @endforeach
    </div>
@endforeach