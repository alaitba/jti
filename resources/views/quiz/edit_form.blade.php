<div class="row">
    <div class="col-md-12">
        <form action="{{ $formAction }}" method="post" class="ajax" id="quizForm">
            <div class="form-group">
                <label for="type">Тип</label>
                <select class="form-control selectpicker" name="type" id="type" disabled>
                    <option value="quiz"{{ $item->type == 'quiz' ? ' selected' : '' }}>Викторина</option>
                    <option value="poll"{{ $item->type == 'poll' ? ' selected' : '' }}>Опрос</option>
                </select>
            </div>
            <div class="form-group">
                <label for="public">Целевая аудитория</label>
                <select class="form-control selectpicker" name="public" id="public">
                    <option value="1"{{ $item->public ? ' selected' : '' }}>Все пользователи</option>
                    <option value="0"{{ $item->public ? '' : ' selected' }}>Пользователи из списка</option>
                </select>
            </div>
            <div class="form-group" id="userListDiv">
                <label for="user_list">Список пользователей</label>
                <input type="file" class="form-control" name="user_list" id="user_list">
                <p class="help-block"></p>
            </div>
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
                                <div class="form-group">
                                    <label for="title.{{ $locale }}">Заголовок *</label>
                                    <input type="text" class="form-control" id="title.{{ $locale }}"
                                           name="title[{{ $locale }}]" value="{{ $item->getTranslation('title', $locale) }}">
                                    <p class="help-block"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="form-group">
                <label>Период проведения</label>
                <div id="drp">
                    <i class="la la-calendar"></i> <span id="period">{{ $item->period }}</span>
                    <input type="hidden" name="from_date" id="from_date" data-formatted="{{ $item->from_date->format('d.m.Y') }}" value="{{ $item->from_date }}">
                    <input type="hidden" name="to_date" id="to_date" data-formatted="{{ $item->to_date->format('d.m.Y') }}" value="{{ $item->to_date }}">
                </div>
                <p class="help-block"></p>
            </div>
            <div class="form-group">
                <label for="amount">Цена, тг</label>
                <input type="text" class="form-control" name="amount" id="amount" value="{{ $item->amount ?? 0 }}">
                <p class="help-block"></p>
            </div>
            <div class="form-group">
                <label for="photo">Фотография</label><br />
                @if($item->photo)
                    <span>
                    <img src="{{ $item->photo->url }}" alt="Фотография" style="max-width: 250px;"><br />
                    <a style="text-decoration:none" href="#" class="delete-media-data-single"
                       data-url="{{ route('admin.quizzes.media.delete', ['mediaId' => $item->photo->id]) }}" data-type="confirm"
                       data-title="Удаление" data-message="Вы уверены, что хотите удалить?">Удалить</a>&nbsp;|&nbsp;</span>
                @endif
                <p class="no-photo text-danger @if($item->photo) d-none @endif">Фото не загружено</p>
                <a href="#" class="photo-upload">Загрузить</a>
                <p class="filename"></p>
                <input type="file" class="fileinput d-none" name="photo" id="photo">
                <p class="help-block"></p>
            </div>
            <div class="form-group">
                <label class="m-checkbox">
                    <input name="active" id="active" value="1" type="checkbox" {{ $item->active ? ' checked' : ''}}{{ isset($item->disabled) ? 'disabled' : '' }} />
                    {{ $item->type == 'quiz' ? 'Активна' : 'Активен'}}
                    <span></span>
                </label>
                <span class="ml-3 text-warning">{{ $item->disabled ?? '' }}</span>
            </div>
            <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
        </form>
    </div>
</div>
@include('quiz.dp')
<script>
    $('.photo-upload').on('click', function (e) {
        e.preventDefault();
        $(this).closest('div').find('input[type="file"]').trigger('click');
    });
    $('.fileinput').on('change', function (e) {
        $(this).closest('div').find('p.filename').text($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
</script>
