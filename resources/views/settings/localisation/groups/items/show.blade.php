<ul class="list-group">
@foreach($locales as $locale)
<li class="list-group-item">{{strtoupper($locale)}}: {{$item->getTranslation('values', $locale)}}</li>
@endforeach
</ul>