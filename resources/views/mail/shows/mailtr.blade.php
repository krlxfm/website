@php
$strengths = ['None', 'Preferred', 'Strongly Preferred', 'First Choice'];
$final_list = [];

foreach($list as $item) {
    $start = Carbon\Carbon::createFromTimeString($item['start']);
    $end = Carbon\Carbon::createFromTimeString($item['end']);
    $item_string = implode(', ', $item['days']).' '.$start->format('g:i a').' - '.$end->format('g:i a');
    if($end->lte($start)) {
        $item_string .= ' <small><em>NEXT DAY</em></small>';
    }
    if(isset($item['strength'])) {
        $item_string .= ' ('.$strengths[$item['strength']].')';
    }
    $final_list[] = $item_string;
}
@endphp
| {{ $title }} | {!! implode('<br>', $final_list) !!} |
