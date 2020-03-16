<form action="{{ $formAction }}" method="post" class="ajax" id="quizQuestionForm">
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                @foreach(config('project.locales') as $locale)
                    <li role="presentation" class="nav-item">
                        <a class="@if($loop->first) active @endif nav-link" href="#tab-{{ $loop->index }}"
                           aria-controls="#tab-{{ $loop->index }}" role="tab"
                           data-toggle="tab">{{ $locale }}</a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach(config('project.locales') as $locale)
                    <div role="tabpanel" class="tab-pane{{ $loop->first ? ' active' : '' }}" id="tab-{{ $loop->index }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="question.{{ $locale }}">Вопрос *</label>
                                    <input type="text" class="form-control"
                                           id="question.{{ $locale }}"
                                           name="question[{{ $locale }}]"
                                           @if (isset($question))
                                           value="{{ $question->getTranslation('question', $locale) }}"
                                           @endif
                                    >
                                    <p class="help-block"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @if($quizType == 'poll')
    <div class="row">
        <div class="col-12 form-group">
            <label for="type">Тип вопроса</label>
            <select class="form-control selectpicker" name="type" id="type">
                <option value="choice">Выбор</option>
                <option value="text" @if(isset($question) && $question->type == 'text') selected @endif>Текст</option>
            </select>
        </div>
    </div>
    @else
        <input type="hidden" name="type" value="choice">
    @endif
    <div class="row">
        <div class="col-12 form-group">
            <label for="photo">Фотография</label>
            @if(isset($question) && $question->photo)
                <img src="{{ $question->photo->url }}" alt="Фотография" style="max-width: 100%;"><br />
                <a style="text-decoration:none" href="#" class="delete-media-data"
                   data-url="{{ route('admin.quizzes.media.delete', ['mediaId' => $question->photo->id]) }}" data-type="confirm"
                   data-title="Удаление" data-message="Вы уверены, что хотите удалить?">Удалить</a>&nbsp;|&nbsp;
            @else
                <p class="text-danger">Фото не загружено</p>
            @endif
            <a href="#" class="photo-upload">Загрузить</a>
            <p class="filename"></p>
            <input type="file" class="fileinput d-none" name="photo" id="photo">
            <p class="help-block"></p>
        </div>
    </div>
    <div class="row" id="answers">
        <div class="col-12">dfdsf</div>
    </div>
    <div class="row text-center">
        <div class="col-12">
            <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
        </div>
    </div>
</form>
<script>
    $('.selectpicker').selectpicker({container: 'body'});
    $('.photo-upload').on('click', function (e) {
        e.preventDefault();
        $(this).closest('div').find('input[type="file"]').trigger('click');
    });
    $('.fileinput').on('change', function (e) {
        $(this).closest('div').find('p.filename').text($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
    $('#type').on('change', e => {
        $('#answers').toggleClass('d-none');
    });
</script>
