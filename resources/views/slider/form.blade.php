<div class="row">
    <div class="col-md-12">
        <form action="{{ $formAction }}" method="post" class="ajax" id="sliderForm">
            <div class="form-group">
                <label for="image">Изображение</label>
                <input type="file" class="form-control" name="image" id="image">
                <p class="help-block"></p>
            </div>
            <div class="form-group">
                <label for="link">Ссылка</label>
                <input type="url" class="form-control" id="link" name="link" value="{{ $item->link ?? '' }}">
                <p class="help-block"></p>
            </div>
            <button type="submit" class="btn btn-sm btn-success">{{ $buttonText }}</button>
        </form>
    </div>
</div>
