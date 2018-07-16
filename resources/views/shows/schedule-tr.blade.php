<tr>
    <td>{{ $title }}</td>
    <td>
        @foreach($list as $item)
            @php
            $start = Carbon\Carbon::createFromTimeString($item['start']);
            $end = Carbon\Carbon::createFromTimeString($item['end']);
            $strengths = ['None', 'Preferred', 'Strongly Preferred', 'First Choice'];
            @endphp
            @if(!$loop->first) <br> @endif
            {{ implode(', ', $item['days']) }}
            {{ $start->format('g:i a') }} -
            {{ $end->format('g:i a') }}
            @if($end->lte($start))
                <small class="text-info"><em>NEXT DAY</em></small>
            @endif
            @isset($item['strength'])
                ({{ $strengths[$item['strength']] }})
            @endisset
        @endforeach
    </td>
    <td><a href="{{ route("shows.$path", $show) }}">Edit</a></td>
</tr>
