<p>{{ $show->track->name }} has a fixed time slot, so you will only be applying to one episode. Please indicate which days you are unable to do the show (usually when you're off campus), and if there are any weeks you would prefer.</p>
<div class="row">
    @php
        $date = Carbon\Carbon::instance($show->term->on_air->modify('next '.$show->track->start_day));
        $date->setTimeFromTimeString($show->track->start_time.':00');
        if($date->dayOfWeek == $show->term->on_air->dayOfWeek and $show->term->on_air->diffInHours($date->copy()->subWeek(), false) >= 0) {
            $date->subWeek();
        }
        $end_date = $date->copy()->setTimeFromTimeString($show->track->end_time.':00');
        if($end_date < $date) {
            $end_date->addDay();
        }
        $options = [];
        while($end_date < $show->term->off_air) {
            $options[] = ['date' => $date->copy()->format('Y-m-d'), 'start' => $date->copy(), 'end' => $end_date->copy()];
            $date->addWeek();
            $end_date->addWeek();
        }
    @endphp
    <div class="col-sm mb-3">
        <h2>Conflicts</h2>
        @foreach($options as $dateset)
            <div class="custom-control custom-checkbox">
                <input type="checkbox" id="conflicts-{{ $dateset['date'] }}" name="conflicts" data-cast="array" class="custom-control-input" value="{{ $dateset['date'] }}" {{ in_array($dateset['date'], $show->conflicts) ? 'checked' : '' }}>
                <label class="custom-control-label" for="conflicts-{{ $dateset['date'] }}" dusk="conflicts-{{ $dateset['date'] }}-label">
                    {{ $dateset['start']->format('F j, Y g:i a') }} - {{ $dateset['end']->format('g:i a') }}
                </label>
            </div>
        @endforeach
    </div>
    <div class="col-sm mb-3">
        <h2>Preferences</h2>
        @foreach($options as $dateset)
            <div class="custom-control custom-checkbox">
                <input type="checkbox" id="preferences-{{ $dateset['date'] }}" name="preferences" data-cast="array" class="custom-control-input" value="{{ $dateset['date'] }}" {{ in_array($dateset['date'], $show->preferences) ? 'checked' : '' }}>
                <label class="custom-control-label" for="preferences-{{ $dateset['date'] }}" dusk="preferences-{{ $dateset['date'] }}-label">
                    {{ $dateset['start']->format('F j, Y g:i a') }} - {{ $dateset['end']->format('g:i a') }}
                </label>
            </div>
        @endforeach
    </div>
</div>
