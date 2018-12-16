<h2>Common questions</h2>
<p>The {{ count($app->common) }} common questions serve as a baseline to your board application and give us a general overview of your goals with KRLX. You'll need to answer all {{ count($app->common) }} questions, regardless of which position(s) you are actually applying for.</p>

@unless($app->submitted)
    <p><a href="{{ route('board.common', $app->year) }}" class="btn btn-lg btn-secondary">Answer or revise the common questions <i class="fas fa-chevron-right"></i></a></p>
@endunless

@foreach($app->common as $question => $response)
    <h5 class="head-sans-serif mb-1 mt-3">
        @include('board.panelicon', ['complete' => !empty($response)])
        <strong>{{ $question }}</strong>
    </h5>
    @empty ($response)
        <em>No response yet.</em>
    @else
        {!! str_replace('<script>', '&lt;script&rt;', $response) !!}
    @endempty
@endforeach
