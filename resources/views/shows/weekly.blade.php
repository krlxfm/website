<div class="alert alert-info">
    Please carefully enter your schedule details below. <strong>You are responsible for declaring your full schedule!</strong>
</div>
<fieldset class="form-group">
    <div class="row">
        <legend class="col-form-label col-sm-2 pt-0">Preferred length</legend>
        <div class="col-sm-10">
            <div class="custom-control custom-radio">
                <input type="radio" id="length-30" name="length" class="custom-control-input" value="30" {{ $show->preferred_length == 30 ? 'checked' : '' }}>
                <label class="custom-control-label" for="length-30">30 minutes</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="length-60" name="length" class="custom-control-input" value="60" {{ $show->preferred_length == 60 ? 'checked' : '' }}>
                <label class="custom-control-label" for="length-60">60 minutes (1 hour)</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="length-90" name="length" class="custom-control-input" value="90" {{ $show->preferred_length == 90 ? 'checked' : '' }}>
                <label class="custom-control-label" for="length-90">90 minutes (1&frac12; hours)</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="length-120" name="length" class="custom-control-input" value="120" {{ $show->preferred_length == 120 ? 'checked' : '' }}>
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
                    <select class="custom-select">
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
<p>You can add lab, art, PE, and other class sections in the "Other conflicts" section. We'll automatically round class times to the nearest half hour.</p>
<div class="row mb-3">
    @foreach(config('classes.groups') as $group)
        <div class="col-sm">
            <p class="mb-1">{{ $group['name'] }}</p>
            @foreach($group['classes'] as $block)
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" id="classes-{{ $block }}" name="classes" class="custom-control-input" value="{{ $block }}" {{ in_array($block, $show->classes) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="classes-{{ $block }}">
                        {{ $block }}
                    </label>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
<div class="d-flex my-3 align-items-center flex-wrap">
    <h2>Other conflicts</h2>
    <button type="button" class="btn btn-primary ml-auto">
        <i class="fas fa-plus"></i> Add conflict
    </button>
</div>
<div class="d-flex my-3 align-items-center flex-wrap">
    <h2>Preferences</h2>
    <button type="button" class="btn btn-primary ml-auto">
        <i class="fas fa-plus"></i> Add preference
    </button>
</div>

<script>
var classes = {!! json_encode($show->classes) !!};
var conflicts = {!! json_encode($show->conflicts) !!};
var preferences = {!! json_encode($show->preferences) !!};
var classTimes = {!! json_encode(config('classes.times')) !!};
</script>
