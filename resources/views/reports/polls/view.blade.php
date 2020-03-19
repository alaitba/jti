@foreach($items as $item)
    <div class="card mb-3">
        <div class="card-header">{{ $item['question'] }}</div>
        <div class="card-body">
            @if(is_countable($item['answer']))
                @if(count($item['answer']) == 1)
                    {{ $item['answer'][0]->getTranslation('answer', 'ru') }}
                @else
                    <ul>
                    @foreach($item['answer'] as $answer)
                        <li>{{ $answer->getTranslation('answer', 'ru') }}</li>
                    @endforeach
                    </ul>
                @endif
            @else
                {{ $item['answer'] }}
            @endif
        </div>
    </div>
@endforeach
