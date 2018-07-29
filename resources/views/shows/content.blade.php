@extends('layouts.showapp', ['title' => 'Content', 'next' => 'schedule', 'back' => 'hosts'])

@section('head')
    @parent
    <p>Please fill in the following information about your show. Required fields are marked with a red asterisk (<span class="text-danger">*</span>) and must be filled in to continue. Some of this information may be published in programming catalogs and other locations.</p>
    <div class="row">
        <div class="col">
            <form method="post" action="/shows/{{ $show->id }}" id="content-form">
                @method('patch')
                @csrf
                <div class="form-group row">
                    <label for="title" class="col-sm-3 col-md-2 col-form-label">{{ $show->track->title_label ?? 'Title' }} <span class="text-danger">*</span></label>
                    <div class="col-sm-9 col-md-10">
                        <input type="text" name="title" class="form-control" id="title" required value="{{ old('title') ?? $show->title }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="description" class="col-sm-3 col-md-2 col-form-label">
                        {{ $show->track->description_label ?? 'Description' }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-9 col-md-10">
                        <textarea id="description" class="form-control" rows="3" name="description" placeholder="Show description, to be used in the program catalog and other locations.">{{ old('description') ?? $show->description }}</textarea>
                    </div>
                </div>
                @foreach($show->track->content as $field)
                    @include('fields.'.$field['type'], ['category' => 'content', 'value' => $show->content[$field['db']]])
                @endforeach
                @if($show->track->allows_images)
                    <div class="form-group row">
                        <label for="photo" class="col-sm-3 col-md-2 col-form-label">Promotional image</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" readonly class="form-control-plaintext" id="photoComingSoon" value="Coming soon, by popular demand!">
                        </div>
                    </div>
                @endif
                @if($show->track->taggable)
                    <div class="form-group row">
                        <label for="tags" class="col-sm-3 col-md-2 col-form-label">Tags</label>
                        <div class="col-sm-9 col-md-10">
                            <input type="text" readonly class="form-control-plaintext" id="tagsComingSoon" value="Coming soon, by popular demand!">
                        </div>
                    </div>
                @endif
                @if($show->track->can_fall_back)
                    <fieldset class="form-group">
                        <div class="row">
                            <legend class="col-form-label col-sm-3 col-md-2 pt-0">Fallback</legend>
                            <div class="col-sm-9 col-md-10">
                                <p>While we hope &ndash; as much as you do! &ndash; to schedule this show on the {{ $show->track->name }} track, there may be circumstances where this does not happen. If we are unable to schedule you on the {{ $show->track->name }} track, what would you like us to do?</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fallback" id="fallback-yes" value="1" {{ $show->fallback ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fallback-yes">
                                        <strong>Fall back</strong> to the Standard track, and schedule with standard priority <em>(recommended)</em>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="fallback" id="fallback-no" value="0" {{ $show->fallback ? '' : 'checked' }}>
                                    <label class="form-check-label" for="fallback-no">
                                        <strong>Cancel</strong> the application in its entirety
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                @endif
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
var showID = "{{ $show->id }}";
</script>
<script src="/js/pages/shows/submitform.js" defer></script>
<script src="/js/pages/shows/content.js" defer></script>
@endpush
