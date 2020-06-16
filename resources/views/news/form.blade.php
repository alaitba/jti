<div class="row">
    <div @if(isset($item)) class="col-md-8" @else class="col-md-12" @endif>
        <form action="{{$formAction}}" method="post" class="ajax" id="newsForm" data-ui-block-element="#newsForm">
            @if(!isset($item))
                <fieldset>
                    <legend>Медиа новости</legend>
                    <div class="form-group">
                        <label for="image">Картинка новости</label>
                        <input type="file" class="form-control  " id="image" name="image" placeholder="">
                        <p class="help-block"></p>
                    </div>
                </fieldset>
            @endif

            <ul class="nav nav-tabs" role="tablist">
                @foreach(config('project.locales') as $count => $locale)
                    <li role="presentation" class="nav-item">
                        <a class="@if($count == 0) active @endif nav-link" href="#tab-{{ $count }}"
                           aria-controls="#tab-{{ $count }}" role="tab"
                           data-toggle="tab">{{ $locale }}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach(config('project.locales') as $count => $locale)
                    <div role="tabpanel" class="tab-pane @if($count == 0)  active  @endif " id="tab-{{ $count }}">
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset>
                                    <legend>Информация о новости</legend>
                                    <div class="form-group">
                                        <label for="title.{{ $locale }}">Заголовок *</label>
                                        <input type="text" class="form-control" id="title.{{ $locale }}" name="title[{{ $locale }}]" placeholder=""  @if(isset($item)) value="{{$item->getTranslation('title', $locale)}}" @endif>
                                        <p class="help-block"></p>
                                    </div>
                                    <div class="form-group">
                                        <label for="contents.{{ $locale }}">Содержимое *</label>
                                        <textarea id="contents.{{ $locale }}" name="contents[{{ $locale }}]"  class="form-control editor ">@if(isset($item)) {{$item->getTranslation('contents', $locale)}} @endif</textarea>
                                        <p class="help-block"></p>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <fieldset>
                <legend>Период проведения</legend>
                <div class="form-group">
                    <div id="drp">
                        <i class="la la-calendar"></i> <span id="period"> @if(isset($item->period)) {{ $item->period }}  @else {{$date->period}} @endif </span>
                        <input type="hidden" name="from_date" id="from_date" @if(isset($item->from_date)) data-formatted="{{ $item->from_date->format('d.m.Y') }}" value="{{ $item->from_date }}" @else data-formatted="{{ $date->from_date->format('d.m.Y') }}" value="{{ $date->from_date }}" @endif>
                        <input type="hidden" name="to_date" id="to_date" @if(isset($item->to_date)) data-formatted="{{ $item->to_date->format('d.m.Y') }}" value="{{ $item->to_date }}" @else data-formatted="{{ $date->to_date->format('d.m.Y') }}" value="{{ $date->to_date }}" @endif>
                    </div>
                    <p class="help-block"></p>
                </div>
            </fieldset>

            <button type="submit" class="btn btn-sm btn-success">{{ $buttonText }}</button>
        </form>
    </div>
    @if(isset($item))
    <div class="col-md-4" id="media_container">
        <fieldset>
            <legend>Изображения</legend>
            <form action="{{ route('admin.news.media', ['newsId' => $item->id]) }}"
                  method="post"
                  id="formImage" data-ui-block-element="#media_container" class="ajax">
                @csrf
                <input type="file" name="image[]" class="form-input-image-media" style="display: none"
                       accept="image/x-png,image/gif,image/jpeg,image/svg"
                       multiple>
                <button type="button" class="btn btn-success btn-sm add-photo">Добавить фото</button>
            </form>
            <div class="media-block">
                @foreach($medias as $row)
                    <div class="row">
                        @foreach($row as $media)
                            @include('news.media_item')
                        @endforeach
                    </div>
                @endforeach
            </div>
        </fieldset>
    </div>
    @endif
</div>
<script>
    $('.editor').each(function () {
        let height = $(this).attr('data-editor-height');
        editor = CKEDITOR.replace($(this).attr('id'), {

            height: (height) ? height : 150
        });
    });
</script>
@include('quiz.dp')

