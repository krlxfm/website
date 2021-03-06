<h2>{{ $position->position->title }}</h2>

<p>The questions here pertain only to the {{ $position->position->title }} position.</p>
@unless($app->submitted)
<p>
    <a href="{{ route('positions.show', $position->id) }}" class="btn btn-lg btn-secondary">Answer or revise the {{ $position->position->title }} questions <i class="fas fa-chevron-right"></i></a>
</p>
@endunless

@foreach($position->position->app_questions ?? [] as $index => $question)
    @php
        $response = array_key_exists($question, $position->responses) ? $position->responses[$question] : null;
    @endphp
    <h5 class="head-sans-serif mb-1 mt-3">
        @include('board.panelicon', ['complete' => (array_key_exists($question, $position->responses) and !empty($position->responses[$question]))])
        <strong>{{ $question }}</strong>
    </h5>
    @if (array_key_exists($question, $position->responses) and !empty($position->responses[$question]))
        {!! str_replace('<script>', '&lt;script&rt;', $response) !!}
    @else
        <em>No response yet.</em>
    @endif
@endforeach

@unless($app->submitted)
<form action="{{ route('positions.destroy', $position->id) }}" class="mt-3" data-position-id="{{ $position->position->id }}" method="post">
    @csrf
    @method('delete')
    <button type="button" data-action="delete-position" class="btn btn-outline-danger" data-position="{{ $position->position->title }}" data-posid="{{ $position->position->id }}">Remove this position</button>
</form>
@endunless
