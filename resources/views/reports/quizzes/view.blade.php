@foreach($items as $item)
    <div class="card mb-3">
        <div class="card-header">{{ $item['question'] }}</div>
        <div class="card-body">
            <div class="pull-right"><i class="la {{ $item['correct'] ? 'la-check text-success' : 'la-ban text-danger' }}"></i></div>
            {{ $item['answer'] }}
        </div>
    </div>
@endforeach
