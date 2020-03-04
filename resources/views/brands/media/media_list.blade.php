<div class="row">
    @foreach($photos as $photo)
        @include('brands.media.media_item', ['photo' => $photo])
    @endforeach
</div>
