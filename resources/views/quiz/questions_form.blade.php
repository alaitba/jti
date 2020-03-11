<form action="{{ $formAction }}" method="post" class="ajax" id="quizQuestionsForm">
    @if(!count($item->questions))
        <div class="alert alert-warning m-alert--outline">
            Вопросов пока нет.
        </div>
    @endif
    <div id="questions">
        @foreach($item->questions as $question)
            <div class="question-{{ $loop->index }}">
                <ul class="nav nav-tabs" role="tablist">
                    @foreach(config('project.locales') as $count => $locale)
                        <li role="presentation" class="nav-item">
                            <a class="@if($count == 0) active @endif nav-link" href="#tab-{{ $count }}"
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
                                        <label for="question.{{ $locale }}.{{ $loop->index }}">Вопрос *</label>
                                        <input type="text" class="form-control"
                                               id="question.{{ $locale }}.{{ $loop->index }}"
                                               name="question[{{ $locale }}][{{ $loop->index }}]"
                                               value="{{ $question->getTranslation('question', $locale) }}">
                                        <p class="help-block"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="answers">

            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-12 text-center">
            <button type="button" class="btn btn-sm btn-outline-brand" id="add-quesion">Добавить вопрос</button>
            <button type="button" class="btn btn-sm btn-outline-success" id="save-questions">Сохранить изменения</button>
        </div>
    </div>
    <div id="question-template">
        <div class="row">
            @if($item->type == 'poll')
                <div class="col-12 form-group">
                    <label for="type.%num%">Тип</label>
                    <select class="form-control selectpicker" name="type[%num%]" id="type.%num%">
                        <option value="choice">Выбор</option>
                        <option value="text">Текст</option>
                    </select>
                </div>
            @endif
            <div class="col-12 form-group">
                <label>Вопрос</label>
                <input type="text" class="form-control form-control-sm" name="question[%num%]">
            </div>
            <div class="col-12 form-group">
                <label for="photo">Фотография</label>
                <p class="text-danger">Фото не загружено</p>
                <a href="#" class="photo-upload">Загрузить</a>
                <p class="filename"></p>
                <input type="file" class="fileinput d-none" name="photo[]" id="photo">
                <p class="help-block"></p>
            </div>
        </div>
    </div>
</form>
<script>
    if (typeof numQuestions === 'undefined') {
        let numQuestions;
    }
    numQuestions = {{ count($item->questions) }}
    $('.photo-upload').on('click', function (e) {
        e.preventDefault();
        $(this).closest('div').find('input[type="file"]').trigger('click');
    });
    $('.fileinput').on('change', function (e) {
        $(this).closest('div').find('p.filename').text($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
</script>
