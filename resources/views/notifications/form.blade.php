<div class="row">
    <div class="col-md-12">
        <form action="{{ $formAction }}" method="post" class="ajax" id="notificationForm">
            <div class="form-group">
                <label for="type">Тип уведомления</label>
                <select class="form-control" name="type" id="type">
                    <option value="all">Всем активным пользователям</option>
                    <option value="list">Активным пользователям из списка</option>
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
                                <div class="form-group">
                                    <label for="message.{{ $locale }}">Текст *</label>
                                    <textarea id="message.{{ $locale }}" name="message[{{ $locale }}]"
                                              class="form-control editor"></textarea>
                                    <p class="help-block"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-sm btn-success">{{ $buttonText }}</button>
        </form>
    </div>
</div>
<script>
    $('#type').on('change', e => {
        $('#userListDiv').toggleClass('d-none');
        $('#user_list').prop('disabled', $(e.currentTarget).val() === 'all');
    });
</script>
