


<div class="container-fluid">
    <form action="{{ $formAction }}" method="post" class="ajax" id="cropForm">
        <input name="dataX" type="hidden" class="form-control" id="dataX" placeholder="x">
        <input name="dataY" type="hidden" class="form-control" id="dataY" placeholder="y">
        <input name="dataHeight" type="hidden" class="form-control" id="dataHeight" placeholder="height">
        <input name="dataWidth" type="hidden" class="form-control" id="dataWidth" placeholder="width">
        <input name="dataScaleX" type="hidden" class="form-control" id="dataScaleX" placeholder="scaleX">
        <input name="dataScaleY" type="hidden" class="form-control" id="dataScaleY" placeholder="scaleY">


    </form>
    <div class="row">
        <div class="col-md-12">

            <br>
            <div id="actions">

                <div class="docs-buttons">
                    <!-- <h3>Toolbar:</h3> -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" data-method="zoom" data-option="0.1"
                                title="Zoom In">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
              <span class="la la-search-plus"></span>
            </span>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" data-method="zoom" data-option="-0.1"
                                title="Zoom Out">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
              <span class="la la-search-minus"></span>
            </span>
                        </button>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" data-method="move" data-option="-10"
                                data-second-option="0" title="Move Left">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(-10, 0)">
              <span class="la la-arrow-left"></span>
            </span>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" data-method="move" data-option="10"
                                data-second-option="0"
                                title="Move Right">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(10, 0)">
              <span class="la la-arrow-right"></span>
            </span>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" data-method="move" data-option="0"
                                data-second-option="-10" title="Move Up">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(0, -10)">
              <span class="la la-arrow-up"></span>
            </span>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" data-method="move" data-option="0"
                                data-second-option="10"
                                title="Move Down">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.move(0, 10)">
              <span class="la la-arrow-down"></span>
            </span>
                        </button>
                    </div>



                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" data-method="scaleX" data-option="-1"
                                title="Flip Horizontal">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleX(-1)">
              <span class="la la-arrows-h"></span>
            </span>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" data-method="scaleY" data-option="-1"
                                title="Flip Vertical">
            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleY(-1)">
              <span class="la la-arrows-v"></span>
            </span>
                        </button>
                    </div>

                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" data-method="reset" title="Reset">
                            <span class="docs-tooltip" data-toggle="tooltip" title="cropper.reset()">
                                <span class="la la-refresh"></span>
                            </span>
                        </button>
                    </div>

                    <button type="button" class="btn btn-sm btn-success" data-method="getCroppedCanvas"
                            data-option="{ &quot;maxWidth&quot;: 4096, &quot;maxHeight&quot;: 4096 }">
                                <span class="docs-tooltip" data-toggle="tooltip">
                                  Сохранить
                                </span>
                    </button>

                    <!-- Show the cropped image in modal -->
                    <div class="modal fade docs-cropped" id="getCroppedCanvasModal" role="dialog" aria-hidden="true"
                         aria-labelledby="getCroppedCanvasTitle" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="getCroppedCanvasTitle">Cropped</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body"></div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <a class="btn btn-primary" id="download" href="javascript:void(0);"
                                       download="cropped.jpg">Download</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <!-- <h3>Demo:</h3> -->
            <div class="img-container">
                <img style="max-width: 100%;" src="{{$imgPath}}" alt="Picture">
            </div>
        </div>

    </div>
</div>

