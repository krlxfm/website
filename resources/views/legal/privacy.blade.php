@extends('layouts.login', ['title' => 'Terms of service'])

@section('content')
<h1 class="head-sans-serif"><strong>Privacy statement</strong></h1>
@php
$parsedown = new \Parsedown();
$contract = $parsedown->text(file_get_contents(resource_path('assets/markdown/privacy.md')));
@endphp
{!! $contract !!}
<a href="{{ route('login') }}">Back to login</a>
@endsection
