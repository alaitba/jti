@foreach($quizQuestions as $question)
<b>{{ $question->question }}</b><br />
<pre>{{
    $question->type == 'text'
        ? $resultQuestions[$question->id]['answer'] ?? ''
        : \App\Models\QuizAnswer::query()->find($resultQuestions[$question->id]['answer'])->answer ?? ''
    }}</pre>
@endforeach
