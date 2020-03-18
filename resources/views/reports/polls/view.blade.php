@foreach($items as $item)
<b>{{ $item['question'] }}</b><br />
<pre>{{ $item['answer'] }}</pre>
@endforeach
