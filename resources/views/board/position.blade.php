<h2>{{ $position->position->title }}</h2>

<p>The questions here pertain only to the {{ $position->position->title }} position.</p>
<p><a href="{{ route('positions.show', $position->id) }}" class="btn btn-lg btn-secondary">Answer or revise the {{ $position->position->title }} questions <i class="fas fa-chevron-right"></i></a></p>

@foreach($position->position->app_questions ?? [] as $question)
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
