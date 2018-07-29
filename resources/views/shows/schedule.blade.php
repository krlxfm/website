@extends('layouts.showapp', ['title' => 'Schedule', 'back' => 'content', 'next' => 'review'])

@section('head')
    @parent
    <div class="row">
        <div class="col">
            <form method="post" action="/shows/{{ $show->id }}" id="scheduling-form">
                @method('patch')
                @csrf
                @if($show->track->weekly)
                    @include('shows.weekly')
                @else
                    @include('shows.single')
                @endif
                <h2 dusk="schedule-standard-return">Additional details</h2>
                <div id="scheduling-extras">
                    @foreach($show->track->scheduling as $field)
                        @include('fields.'.$field['type'], ['category' => 'scheduling', 'value' => $show->scheduling[$field['db']]])
                    @endforeach
                    <div class="form-group row">
                        <label for="notes" class="col-sm-3 col-md-2 col-form-label">Schedule notes</label>
                        <div class="col-sm-9 col-md-10">
                            <textarea name="notes" class="form-control" id="notes" rows="3">{{ $show->notes }}</textarea>
                            <small id="notes-help" class="form-text text-muted">If you have anything to say to us regarding your schedule, say it here. You do <strong>not</strong> need to justify every single conflict you have listed, but please do let us know if there's anything unusual about your schedule.</small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
var showID = "{{ $show->id }}";
</script>
<script src="/js/pages/shows/submitform.js" defer></script>
<script src="/js/pages/shows/schedule.js" defer></script>
@endpush
