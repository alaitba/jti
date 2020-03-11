<form action="{{ $formAction }}" method="post" class="ajax" id="feedbackForm">
    <div class="form-group">
        <label for="question">Вопрос</label>
        <textarea id="question" class="form-control" readonly>{{ $item->question }}</textarea>
    </div>
    <div class="form-group">
        <label for="answer">Ответ</label>
        <textarea id="answer" name="answer" class="form-control" >{{ $item->answer }}</textarea>
    </div>
    <button type="submit" class="btn btn-sm btn-success">{{ $buttonText }}</button>
</form>
