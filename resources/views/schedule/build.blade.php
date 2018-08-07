@extends('layouts.missioncontrol', ['title' => 'Schedule Builder - '.$term->name])

@push('css')
<link href="/css/fullcalendar.min.css" rel="stylesheet">
@endpush

@section('mid')
<schedule-builder></schedule-builder>
@endsection

@push('topjs')
<script src="/js/schedule.js" defer></script>
@endpush

@push('js')
<script>
var shows = {!! json_encode($shows) !!};
</script>
@endpush
