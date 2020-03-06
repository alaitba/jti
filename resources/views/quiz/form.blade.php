<div class="row">
    <div class="col-md-12">
        <form action="{{ $formAction }}" method="post" class="ajax" id="quizForm">
            <div class="form-group">
                <label for="type">Тип</label>
                <select class="form-control selectpicker" name="type" id="type">
                    <option value="quiz">Викторина</option>
                    <option value="poll">Опрос</option>
                </select>
            </div>
            <div class="form-group">
                <label for="public">Целевая аудитория</label>
                <select class="form-control selectpicker" name="public" id="public">
                    <option value="1">Все пользователи</option>
                    <option value="0">Пользователи из списка</option>
                </select>
            </div>
            <div class="form-group d-none" id="userListDiv">
                <label for="user_list">Список пользователей</label>
                <input type="file" class="form-control" name="user_list" id="user_list" disabled>
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
                                           name="title[{{ $locale }}]" value="">
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

            <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
        </form>
    </div>
</div>
@include('quiz.dp')
