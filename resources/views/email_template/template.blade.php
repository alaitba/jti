<div class="row">
    <div class=" col-md-8">
        <fieldset>
            <legend>Настройка шаблона</legend>
        <form action="{{ $formAction }}" method="post" class="ajax" data-ui-block-type="element"
              data-ui-block-element="#ajaxForm" id="ajaxForm">
            <input name="_token" type="hidden" value="{{ csrf_token() }}">

            <div class="tab-content">
                 <div role="tabpanel" class="tab-pane active" id="tab">
                        @foreach($fields as $fieldName => $field)
                            <div class="form-group">
                            @switch($field['type'])
                                @case ('textarea')
                                <label for="{{$fieldName}}">{{$field['label']}}</label>
                                <textarea class="form-control" rows="15" id="{{$fieldName}}" name="data[{{$fieldName}}]">
                                    @if(is_array($data) && isset($data[$fieldName])){{$data[$fieldName]}}@endif</textarea>
                                @break

                                @case ('text')
                                <label for="{{$fieldName}}">{{$field['label']}}</label>
                                <input type="text" class="form-control" id="{{$fieldName}}" name="data[{{$fieldName}}]"
                                       @if(is_array($data) && isset($data[$fieldName])) value="{{$data[$fieldName]}}" @endif>
                                @break

                            @endswitch
                                <p class="help-block"></p>
                            </div>
                        @endforeach
                 </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">{{  $buttonText }} </button>
            </div>
        </form>
        </fieldset>
    </div>

    <div class=" col-md-4">
        <fieldset>
            <legend>Доступные переменные</legend>
            <div class="list-group">
                @foreach($variables as $variableName => $variable)
                    <a href="#" class="list-group-item variable" data-variable="%{{$variableName}}%">
                       {{$variable['title']}}
                    </a>
                @endforeach
            </div>
        </fieldset>
    </div>
</div>

