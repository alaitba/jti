<div class="row">
    <div class="col-md-12 media-div">
        <fieldset>
            <legend>Изображения</legend>
            <form action="{{ route('admin.brands.media', ['brandId' => $item->id]) }}"
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
                            @include('brands.media.media_item')
                        @endforeach
                    </div>
                @endforeach
            </div>
        </fieldset>
    </div>
</div>
