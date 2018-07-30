<div class="form-group row">
    <label for="{{ $field['db'] }}" class="col-sm-3 col-md-2 col-form-label">
        {{ $field['title'] }}
        @if(in_array('required', $field['rules']))
            <span class="text-danger">*</span>
        @endif
    </label>
    <div class="col-sm-9 col-md-10">
        <input type="text" name="{{ "${category}.{$field['db']}" }}" class="form-control" id="{{ $field['db'] }}" value="{{ old("${category}[{$field['db']}]") ?? $value }}">
        @if($field['helptext'])
            <small id="{{ $field['db'] }}Help" class="form-text text-muted">{{ $field['helptext'] }}</small>
        @endif
    </div>
</div>
