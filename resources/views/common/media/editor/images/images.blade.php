@foreach($images as $row)
    <div class="row">
        @foreach($row as $image)
            <div class="col-md-3">
                <a href="{{$image->original_file_name}}" class="insert-image">
                    <img class="img-thumbnail" width="100%" src="{{$image->thumb_256}}">
                </a>

            </div>
        @endforeach
    </div>

@endforeach