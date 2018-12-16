<h2>Review and submit</h2>

@if($can_submit)
    <p>This is your final checklist for submitting your board application. Please carefully review each item listed and check the checkbox to confirm it. Once all checkboxes are checked, the submit button will appear.</p>
    <hr>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" value="no-edits" id="reviewNoEdits" data-action="review-checkbox">
        <label class="form-check-label" for="reviewNoEdits">
            I understand that once I submit this application, I will no longer be able to make changes.
        </label>
    </div>
    @if($app->positions->count() == 1)
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="no-edits" id="reviewPrefOrder" data-action="review-checkbox">
            <label class="form-check-label" for="reviewPrefOrder">
                I am aware that the only position I am applying for is {{ implode(', ', $app->positions->map(function($position) { return $position->position->title; })->all())}}.
            </label>
        </div>
        <div class="alert alert-info">
            There is no limit to the number of positions you can apply for. Your chances of receiving an offer greatly improve if you apply for more than one position.
        </div>
    @else
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="no-edits" id="reviewPrefOrder" data-action="review-checkbox">
            <label class="form-check-label" for="reviewPrefOrder">
                My preference order has been stored correctly as follows: {{ implode(', ', $app->positions->map(function($position) { return $position->position->title; })->all())}} (my first choice position is {{$app->positions->first()->position->title }}).
            </label>
        </div>
    @endif
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" value="no-edits" id="reviewInterviewAvailability" data-action="review-checkbox">
        <label class="form-check-label" for="reviewInterviewAvailability">
            My interview availability, OCS plans, and video conference connection information (if applicable) are up to date.
        </label>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" value="no-edits" id="reviewInterviewChange" data-action="review-checkbox">
        <label class="form-check-label" for="reviewInterviewChange">
            If my schedule changes from what I have listed, I will contact the Station Manager via email at <code>{{ 'manager@'.env('MAIL_DOMAIN', 'example.org') }}</code> as soon as possible.
        </label>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" value="no-edits" id="reviewExcited" data-action="review-checkbox">
        <label class="form-check-label" for="reviewExcited">
            I am excited about KRLX and am looking forward to the interview process!
        </label>
    </div>

    <form id="submit-form-live" name="submit" action="{{ route('board.app', $app->year )}}" method="post">
        @csrf
        <button type="submit" id="theBigSubmitButton" class="btn btn-success btn-lg btn-block">Submit Board Application</button>
    </form>
@else
    <div class="alert alert-warning">
        One or more sections in this application need to be completed. Please answer all questions in sections labeled with an incomplete warning (<i class="fas fa-exclamation-circle"></i>) and then come back here.
    </div>
@endif
