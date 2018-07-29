<tr data-field="{{ $field }}">
    <td class="align-middle">{{ $title }}</td>
    @if(is_array($value))
        <td class="align-middle">{!! implode('<br>', $value) !!}</td>
    @else
        <td class="align-middle">{{ $value }}</td>
    @endif
    <td class="align-middle"><a href="{{ route("shows.$path", $show) }}">Edit</a></td>
</tr>
