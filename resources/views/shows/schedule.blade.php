@extends('layouts.showapp', ['title' => 'Schedule', 'back' => 'content'])

@section('head')
    @parent
    <div class="row">
        <div class="col">
            <form method="post" action="/shows/{{ $show->id }}" id="content-form">
                @method('patch')
                @csrf
                @if($show->track->weekly)
                    @include('shows.weekly')
                @endif
                <h2>Additional details</h2>
                <div id="scheduling-extras">
                    @foreach($show->track->scheduling as $field)
                        @include('fields.'.$field['type'], ['category' => 'scheduling', 'value' => $show->scheduling[$field['db']]])
                    @endforeach
                    <div class="form-group row">
                        <label for="notes" class="col-sm-3 col-md-2 col-form-label">Schedule notes</label>
                        <div class="col-sm-9 col-md-10">
                            <textarea name="notes" class="form-control" id="notes" rows="3">{{ $show->notes }}</textarea>
                            <small id="notes-help" class="form-text text-muted">If you have anyting to say to us regarding your schedule, say it here. You do <strong>not</strong> need to justify every single conflict you have listed, but please do let us know if there's anything unusual about your schedule.</small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
