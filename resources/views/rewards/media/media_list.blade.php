<div class="row">
    @foreach($photos as $photo)
        @include('rewards.media.media_item', ['photo' => $photo])
    @endforeach
</div>
