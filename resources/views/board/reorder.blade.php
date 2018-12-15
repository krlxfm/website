<h2>Reorder positions</h2>
<p>Candidates can be offered a maximum of one position, so if you're applying for more than one you will need to rank them in the order you prefer. If you're relatively indifferent between two or more positions, please <a href="mailto:manager@{{ env('MAIL_DOMAIN', 'example.org') }}">let us know</a>.</p>
<p><strong>Here's the order we have on file right now</strong> (with #1 being your first choice):</p>
<ol>
    @foreach($app->positions as $position)
        <li>{{ $position->position->title }}</li>
    @endforeach
</ol>
<p><a href="{{ route('board.common', $app->year) }}" class="btn btn-lg btn-secondary">Revise this order <i class="fas fa-chevron-right"></i></a></p>
<p>If this order looks good, you don't need to do anything. If you add any new positions, they will be added to the bottom of the list.</p>
<p>No longer interested in a position? Choose its entry in the sidebar, then click "Remove this position".</p>
