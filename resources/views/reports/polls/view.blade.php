@foreach($items as $item)
    <div class="card mb-3">
        <div class="card-header">{{ $item['question'] }}</div>
        <div class="card-body">
            @if(is_string($item['answer']) || count($item['answer']) == 1)
                {{ $item['answer'] }}
            @else
                <ul>
                @foreach($item['answer'] as $answer)
                    <li>{{ $answer->getTranslation('answer', 'ru') }}</li>
                @endforeach
                </ul>
            @endif
        </div>
    </div>
@endforeach
