
<div class="text-right">
    <button class="btn btn-sm btn-success ">Обрезать</button>

</div>

<div class="text-center">
    <img  style="max-width: 100%;" id="cropImage" src="{{asset('storage/uploads/' . $image->getOriginal('original_file_name'))}}">
</div>

<script>
    var $image = $('#cropImage');

    $image.cropper({
        aspectRatio: 16/11,
        mouseWheelZoom: false,
        zoomable: false,

        crop: function(event) {
            $("#width").val(event.detail.width);
            $("#height").val(event.detail.height);
            $("#offset_x").val(event.detail.x);
            $("#offset_y").val(event.detail.y);
        },

    });


</script>

