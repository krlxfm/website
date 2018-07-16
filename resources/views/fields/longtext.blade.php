<div class="form-group row">
    <label for="{{ $field['db'] }}" class="col-sm-3 col-md-2 col-form-label">{{ $field['title'] }}</label>
    <div class="col-sm-9 col-md-10">
        <textarea name="{{ "${category}.{$field['db']}" }}" class="form-control" id="{{ $field['db'] }}" rows="3">{{ old("${category}[{$field['db']}]") ?? $value }}</textarea>
        @if($field['helptext'])
            <small id="{{ $field['db'] }}Help" class="form-text text-muted">{{ $field['helptext'] }}</small>
        @endif
    </div>
</div>
