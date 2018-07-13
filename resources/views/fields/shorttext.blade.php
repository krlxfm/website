<div class="form-group row">
    <label for="{{ $field['db'] }}" class="col-sm-3 col-md-2 col-form-label">{{ $field['title'] }}</label>
    <div class="col-sm-9 col-md-10">
        <input type="text" name="{{ "${category}[{$field['db']}]" }}" class="form-control" id="{{ $field['db'] }}" value="{{ old("${category}[{$field['db']}]") ?? $value }}">
    </div>
</div>
