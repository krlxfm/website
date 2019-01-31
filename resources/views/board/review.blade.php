<h2>Review and submit</h2>

@if($app->submitted)
    <p>This application has been successfully submitted for review. No further action is required, though if you need to withdraw your name from consideration or need to update your interview availability, you may do so by emailing the Station Manager at <code>{{ 'manager@'.env('MAIL_DOMAIN', 'example.org') }}</code>.</p>
@elseif($can_submit)
    <p>This is your final checklist for submitting your board application. Please carefully review each item listed and check the checkbox to confirm it. Once all checkboxes are checked, the submit button will appear. (If the button doesn't appear, please email <code>{{ 'it@'.env('MAIL_DOMAIN', 'example.org') }}</code> to let us know!)</p>
    <hr>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" value="no-edits" id="reviewNoEdits" data-action="review-checkbox">
        <label class="form-check-label" for="reviewNoEdits">
            I understand that once I submit this application, I will no longer be able to make any changes.
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

    <form id="submit-form-live" name="submit" action="{{ route('board.submit', $app->year )}}" method="post">
        @csrf
        @method('patch')
        <button type="submit" id="theBigSubmitButton" class="btn btn-success btn-lg btn-block">Submit Board Application</button>
    </form>
@elseif ($app->positions->count() == 0)
    <p><strong>You're not quite ready to submit yet, because you need to add at least one position to your application.</strong></p>
    <p>The KRLX Board does not have "at-large" or "general" seats. Candidates apply for seats directly. You'll want to <a href="{{ route('board.positions') }}">review the list of positions</a> and choose one or more to add (there's no hard limit on how many seats you can apply for). If you're having trouble nailing down which seat(s) are best to apply for, you can <a href="{{ 'mailto:manager@'.env('MAIL_DOMAIN', 'example.org') }}"> email the Station Manager</a> asking which seats best align with what you're excited about and what you want to do on the board.</p>
@else
    <div class="alert alert-warning">
        One or more sections in this application need to be completed. Please answer all questions in sections labeled with an incomplete warning (<i class="fas fa-exclamation-circle"></i>) and then come back here.
    </div>
@endif
