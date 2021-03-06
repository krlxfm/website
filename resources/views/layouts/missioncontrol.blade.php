<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'Mission Control') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('/js/manifest.js') }}" defer></script>
    <script src="{{ mix('/js/vendor.js') }}" defer></script>
    @stack('topjs')
    <script src="{{ mix('/js/app.js') }}" defer></script>
    @stack('postjs')

    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    @stack('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
</head>
<body class="bg-gray {{ $body_class ?? '' }}">
    <nav class="navbar navbar-expand-lg navbar-dark bg-krlx-gradient">
        <a class="navbar-brand" href="/home">Mission Control</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            {!! Menu::main() !!}
        </div>
    </nav>
    @if($errors->any())
        <div class="container my-4">
            <div class="alert alert-danger">
                The following {{ str_plural('error', $errors->count()) }} occurred:
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if (session('status'))
        <div class="container my-4">
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        </div>
    @endif
    @if (session('success'))
        <div class="container my-4">
            <div class="alert alert-success">
                <i class="fas fa-check"></i> {{ session('success') }}
            </div>
        </div>
    @endif
    <div id="app">
        <div class="container mt-4">
            @yield('head')
        </div>
        <div class="container-fluid" id="fluid-sector">
            @yield('mid')
        </div>
        <div class="container">
            @yield('foot')
        </div>
    </div>
    @yield('bottom')
    @stack('modals')
    @stack('js')
</body>
