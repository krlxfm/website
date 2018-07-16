@php
$currentValue = old("${category}[{$field['db']}]") ?? $value;
@endphp
<fieldset class="form-group">
    <div class="row">
        <legend class="col-form-label col-sm-3 col-md-2 pt-0">{{ $field['title'] }}</legend>
        <div class="col-sm-9 col-md-10">
            @if($field['helptext'])
                <p>{{ $field['helptext'] }}</p>
            @endif
            @foreach($field['options'] as $option)
                <div class="form-check">
                    <input
                        class="form-check-input"
                        id="{{ $field['db'].'-'.$option['value'] }}"
                        type="radio"
                        name="{{ "${category}.{$field['db']}" }}"
                        value="{{ $option['value'] }}"
                        {{ ($currentValue == $option['value'] or ($currentValue == null and $option['default'])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $field['db'].'-'.$option['value'] }}">
                        {{ $option['title'] }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</fieldset>
