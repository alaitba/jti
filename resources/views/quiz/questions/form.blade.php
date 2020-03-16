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
            <label for="photo">Фотография</label><br/>
            @if(isset($question) && $question->photo)
                <span>
                <img src="{{ $question->photo->url }}" alt="Фотография" style="max-width: 250px;"><br/>
                <a style="text-decoration:none" href="#" class="delete-media-data-single"
                   data-url="{{ route('admin.quizzes.media.delete', ['mediaId' => $question->photo->id]) }}" data-type="confirm"
                   data-title="Удаление" data-message="Вы уверены, что хотите удалить?">Удалить</a>&nbsp;|&nbsp;</span>
            @endif
            <p class="no-photo text-danger @if(isset($question) && $question->photo) d-none @endif">Фото не загружено</p>
            <a href="#" class="photo-upload">Загрузить</a>
            <p class="filename"></p>
            <input type="file" class="fileinput d-none" name="photo" id="photo">
            <p class="help-block"></p>
        </div>
    </div>
    <div class="row @if(isset($question) && $question->type === 'text') d-none @endif" id="answers">
        <div class="col-12">
            <label>Ответы</label> <a class="ml-3 add-answer" href="#">Добавить ответ</a>
            @if(isset($question) && count($question->answers))
                @foreach($question->answers as $answer)
                    <div class="answer answer-{{ $answer->id }}">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach(config('project.locales') as $locale)
                                <li role="presentation" class="nav-item">
                                    <a class="@if($loop->first) active @endif nav-link" href="#tab-answer-{{ $answer->id }}-{{ $loop->index }}"
                                       aria-controls="#tab-answer-{{ $answer->id }}-{{ $loop->index }}" role="tab"
                                       data-toggle="tab">{{ $locale }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach(config('project.locales') as $locale)
                                <div role="tabpanel" class="tab-pane{{ $loop->first ? ' active' : '' }}"
                                     id="tab-answer-{{ $answer->id }}-{{ $loop->index }}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="answer.{{ $answer->id }}.{{ $locale }}">Ответ *</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                           id="answer.{{ $answer->id }}.{{ $locale }}"
                                                           name="answer[{{ $answer->id }}][{{ $locale }}]"
                                                           value="{{ $answer->getTranslation('answer', $locale) }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn del-answer" data-id="{{ $answer->id }}" data-type="existing">
                                                            <i class="la la-trash-o"></i></button>
                                                    </div>
                                                </div>
                                                <p class="help-block"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="m-checkbox">
                                        <input name="correct[{{ $answer->id }}]" value="1" type="checkbox" {{ $answer->correct ? ' checked' : '' }} />
                                        Верный ответ
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="answer new-answer new-answer-0">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach(config('project.locales') as $locale)
                            <li role="presentation" class="nav-item">
                                <a class="@if($loop->first) active @endif nav-link" href="#tab-new-answer-0-{{ $loop->index }}"
                                   aria-controls="#tab-new-answer-0-{{ $loop->index }}" role="tab"
                                   data-toggle="tab">{{ $locale }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        @foreach(config('project.locales') as $locale)
                            <div role="tabpanel" class="tab-pane{{ $loop->first ? ' active' : '' }}" id="tab-answer-0-{{ $loop->index }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="new-answer.0.{{ $locale }}">Ответ *</label>
                                            <div class="input-group">
                                            <input type="text" class="form-control"
                                                   id="new-answer.0.{{ $locale }}"
                                                   name="new-answer[0][{{ $locale }}]"
                                                   value="">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn del-answer" data-id="0" data-type="new">
                                                        <i class="la la-trash-o"></i></button>
                                                </div>
                                            </div>
                                            <p class="help-block"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="m-checkbox">
                                    <input name="new-correct[0]" value="1" type="checkbox" />
                                    Верный ответ
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="row text-center">
        <div class="col-12">
            <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
        </div>
    </div>
</form>

<div class="answer d-none" id="new-answer-template">
    <ul class="nav nav-tabs" role="tablist">
        @foreach(config('project.locales') as $locale)
            <li role="presentation" class="nav-item">
                <a class="@if($loop->first) active @endif nav-link" href="#tab-new-answer-%num%-{{ $loop->index }}"
                   aria-controls="#tab-new-answer-%num%-{{ $loop->index }}" role="tab"
                   data-toggle="tab">{{ $locale }}</a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach(config('project.locales') as $locale)
            <div role="tabpanel" class="tab-pane{{ $loop->first ? ' active' : '' }}" id="tab-answer-%num%-{{ $loop->index }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="new-answer.%num%.{{ $locale }}">Ответ *</label>
                            <div class="input-group">
                                <input type="text" class="form-control"
                                       id="new-answer.%num%.{{ $locale }}"
                                       name="new-answer[%num%][{{ $locale }}]"
                                       value="">
                                <div class="input-group-append">
                                    <button type="button" class="btn del-answer" data-id="%num%" data-type="new">
                                        <i class="la la-trash-o"></i></button>
                                </div>
                            </div>
                            <p class="help-block"></p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="m-checkbox">
                    <input name="new-correct[%num%]" value="1" type="checkbox" />
                    Верный ответ
                    <span></span>
                </label>
            </div>
        </div>
    </div>
</div>

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
    $('.del-answer').on('click', e => {
        if ($('.answer').length === 1) {
            Swal.fire({
                title: 'Ошибка!',
                text: 'Необходим хотя бы один ответ!',
                type: 'error'
            });
            return;
        }
        const id = $(e.currentTarget).data('id');
        Swal.fire({
            title: 'Удаление',
            text: 'Вы уверены, что хотите удалить вопрос?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'rgb(48, 133, 214)',
            cancelButtonColor: '#aaa',
            cancelButtonText: 'Отмена',
            confirmButtonText: 'Подтвердить'
        }).then((result) => {
            if (result.value) {
                $(`.${$(e.currentTarget).data('type') === 'new' ? 'new-' : ''}answer-${id}`).remove();
            }
        })
    });
    $('#quizQuestionForm').on('submit', e => {
        e.preventDefault();
        if ($('input[name="type"]').val() === 'choice' && !($('input[name*="correct"]:checked').length)) {
            Swal.fire({
                title: 'Ошибка!',
                text: 'Необходимо выбрать хотя бы один правильный ответ!',
                type: 'error'
            });
            return false;
        }
    });
    $('.add-answer').on('click', e => {
        e.preventDefault();
        const num = $('.new-answer').length;
        let clonedAnswer = $('#new-answer-template').clone().removeAttr('id').removeClass('d-none').addClass('new-answer').addClass(`new-answer-${num}`);
        clonedAnswer.html(clonedAnswer.html().replace(/%num%/g, num));
        clonedAnswer.appendTo($('#answers .col-12'));
    });
</script>
