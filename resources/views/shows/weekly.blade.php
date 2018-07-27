<div class="alert alert-info">
    Please carefully enter your schedule details below. <strong>You are responsible for declaring your full schedule!</strong>
</div>
<fieldset class="form-group">
    <div class="row">
        <legend class="col-form-label col-sm-2 pt-0">Preferred length</legend>
        <div class="col-sm-10">
            <div class="custom-control custom-radio">
                <input type="radio" id="length-30" name="preferred_length" class="custom-control-input" value="30" {{ $show->preferred_length == 30 ? 'checked' : '' }}>
                <label class="custom-control-label" for="length-30">30 minutes (&frac12; hour)</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="length-60" name="preferred_length" class="custom-control-input" value="60" {{ $show->preferred_length == 60 ? 'checked' : '' }}>
                <label class="custom-control-label" for="length-60">60 minutes (1 hour)</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="length-90" name="preferred_length" class="custom-control-input" value="90" {{ $show->preferred_length == 90 ? 'checked' : '' }}>
                <label class="custom-control-label" for="length-90">90 minutes (1&frac12; hours)</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="length-120" name="preferred_length" class="custom-control-input" value="120" {{ $show->preferred_length == 120 ? 'checked' : '' }}>
                <label class="custom-control-label" for="length-120">120 minutes (2 hours)</label>
            </div>
        </div>
    </div>
</fieldset>
<h2>Special time designations</h2>
<p>Some time slots throughout the schedule are given special designations. Please indicate your level of interest in being scheduled in each of these designated times.</p>
<div class="list-group mb-3">
    @foreach(config('defaults.special_times') as $id => $zone)
        <div class="list-group-item row">
            <div class="row">
                <div class="col-sm-9">
                    <h4>{{ $zone['name'] }}</h4>
                    {{ $zone['description'] }}
                </div>
                <div class="col-sm-3">
                    <select class="custom-select" name="special_times.{{ $id }}">
                        <option value="y" {{ $show->special_times[$id] == 'y' ? 'selected' : '' }}>Yes, please schedule me here</option>
                        <option value="n" {{ $show->special_times[$id] == 'n' ? 'selected' : '' }}>No, please avoid scheduling me here</option>
                        <option value="m" {{ $show->special_times[$id] == 'm' ? 'selected' : '' }}>Meh, doesn't matter</option>
                    </select>
                </div>
            </div>
        </div>
    @endforeach
</div>
<h2>Classes</h2>
<p>Most common class times are listed here. Hover over a class time for details. If you have other classes that don't fit neatly in the standard schedule, add them as conflicts below.</p>
<div class="row mb-3">
    @foreach(config('classes.groups') as $group)
        <div class="col-sm-6 col-md-4 mb-3">
            <p class="mb-1"><strong>{{ $group['name'] }}</strong></p>
            @foreach($group['classes'] as $block)
                @php
                    $dispTimes = array_map(function($time) {
                        $start = Carbon\Carbon::parse($time['start']);
                        $end = Carbon\Carbon::parse($time['end']);
                        $days = array_map(function($day) { return substr($day, 0, $day[0] == 'T' ? 2 : 1); }, $time['days']);
                        return implode(', ', $days).' '.$start->format('g:i a').' - '.$end->format('g:i a');
                    }, config("classes.times.$block.displayTimes"));
                @endphp
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" id="classes-{{ $block }}" name="classes" data-cast="array" class="custom-control-input" value="{{ $block }}" {{ in_array($block, $show->classes) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="classes-{{ $block }}" dusk="classes-{{ $block }}-label" data-toggle="tooltip" data-placement="bottom" data-html="true" title="{{ implode('<br>', $dispTimes) }}">
                        {{ config("classes.times.$block.name") }}
                    </label>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
<div class="d-flex mb-2 mt-4 align-items-center flex-wrap">
    <h2>Other conflicts</h2>
    <button type="button" class="btn btn-primary ml-auto" id="add-conflict-button" dusk="add-conflict-button">
        <i class="fas fa-plus"></i> Add conflict
    </button>
</div>
<p>Add any times that you absolutely cannot miss, and we won't schedule you during these times. Good things to declare include non-standard class times, employment (on or off campus), sports, or club meetings where you have a significant obligation. If you're an RA, be sure to declare standard duty hours.</p>
<p><strong class="text-danger">If you are declaring an overnight conflict, please state the reasons for it in the notes box below</strong> to ensure it gets honored.</p>
<conflict-list></conflict-list>
<div class="d-flex mb-2 mt-4 align-items-center flex-wrap">
    <h2>Preferences</h2>
    <button type="button" class="btn btn-primary ml-auto" id="add-preference-button" dusk="add-preference-button">
        <i class="fas fa-plus"></i> Add preference
    </button>
</div>
<preference-list></preference-list>

@push('modals')
    @component('components.modal')
        @slot('id', 'conflict-manager')
        @slot('title', 'Add Conflict')
        @slot('footer')
            <button type="button" class="btn btn-primary" dusk="save-conflict" id="save-conflict" onclick="saveConflict()">
                Save conflict
            </button>
        @endslot
        <p>Add any times that you absolutely cannot miss, and we won't schedule you during these times. Good things to declare include non-standard class times, employment (on or off campus), sports, or club meetings where you have a significant obligation.</p>
        <p><strong class="text-danger">If you are declaring an overnight conflict, please state the reasons for it in the notes box on the main page</strong> to ensure it gets honored.</p>
        <input type="hidden" id="conflict-index" value="-1">
        <div class="row">
            <div class="col-sm">
                <p>Days</p>
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="conflict-days-{{ $day }}" value="{{ $day }}" name="conflict-days">
                        <label class="custom-control-label" for="conflict-days-{{ $day }}">{{ $day }}</label>
                    </div>
                @endforeach
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label class="form-control-label">Start time</label>
                    <select class="custom-select" name="conflict-start" id="conflict-start">
                        @for($i = 0; $i < 48; $i++)
                            @php
                            $date = Carbon\Carbon::today()->addMinutes($i * 30);
                            @endphp
                            <option value="{{ $date->format('H:i') }}">{{ $date->format('g:i a') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-control-label">End time</label>
                    <select class="custom-select" name="conflict-end" id="conflict-end"></select>
                </div>
            </div>
        </div>
    @endcomponent

    @component('components.modal')
        @slot('id', 'preference-manager')
        @slot('title', 'Add Preference')
        @slot('footer')
            <button type="button" class="btn btn-primary" dusk="save-preference" id="save-preference" onclick="savePreference()">
                Save preference
            </button>
        @endslot
        <p>Enter the times you'd like this show to occur. For best results, be flexible and give a few options. You can also list the relative strength of your preferences.</p>
        <input type="hidden" id="preference-index" value="-1">
        <div class="row">
            <div class="col-sm">
                <p>Days</p>
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="preference-days-{{ $day }}" value="{{ $day }}" name="preference-days">
                        <label class="custom-control-label" for="preference-days-{{ $day }}">{{ $day }}</label>
                    </div>
                @endforeach
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label class="form-control-label">Start time</label>
                    <select class="custom-select" name="preference-start" id="preference-start">
                        @for($i = 0; $i < 48; $i++)
                            @php
                            $date = Carbon\Carbon::today()->addMinutes($i * 30);
                            @endphp
                            <option value="{{ $date->format('H:i') }}">{{ $date->format('g:i a') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-control-label">End time</label>
                    <select class="custom-select" name="preference-end" id="preference-end"></select>
                </div>
                <div class="form-group">
                    <label class="form-control-label">Preference strength</label>
                    <select class="custom-select" name="preference-strength" id="preference-strength">
                        <option value="1">Preferred</option>
                        <option value="2">Strongly Preferred</option>
                        <option value="3">First Choice</option>
                    </select>
                </div>
            </div>
        </div>
    @endcomponent
@endpush

@push('js')
<script>
var classes = {!! json_encode($show->classes) !!};
var conflicts = {!! json_encode($show->conflicts) !!};
var preferences = {!! json_encode($show->preferences) !!};
var classTimes = {!! json_encode(config('classes.times')) !!};
</script>
<script src="/js/pages/shows/weekly.js" defer></script>
@endpush
