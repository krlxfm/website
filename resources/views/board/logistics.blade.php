<h2>Logistics</h2>
<p>Also known as: let's figure out how we'll be working together to conduct your interview, and if you're planning on going abroad in the next three terms.</p>
<div class="alert alert-danger">
    In order to retain your board seat, you must be on campus for two out of the next three academic terms. If you will be off campus for two terms next year, <strong>STOP HERE</strong> and <a href="{{ 'mailto:manager@'.env('MAIL_DOMAIN', 'example.org') }}" class="alert-link">contact the Station Manager</a> immediately to discuss your options.
</div>

@if ($logistics_needed)
    <a href="{{ route('board.logistics', $app->year) }}" class="btn btn-lg btn-secondary">Answer the logistics questions <i class="fas fa-chevron-right"></i></a>
@endif
