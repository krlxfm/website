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
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/etc/iphonex.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
</head>
<body class="bg-gray {{ $body_class ?? '' }}">
    <nav class="navbar navbar-expand-lg navbar-dark bg-krlx-gradient">
        <a class="navbar-brand" href="/home">Mission Control</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active"><a class="nav-link" href="/home">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Radio Shows
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">My radio shows</a>
                        <a class="dropdown-item" href="#">Start new application</a>
                        <a class="dropdown-item" href="#">Join show</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Schedule</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">All radio shows</a>
                        <a class="dropdown-item" href="#">DJ roster</a>
                        <a class="dropdown-item" href="#">Schedule builder</a>
                        <a class="dropdown-item" href="#">Special show manager</a>
                    </div>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">Record Libe</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Administration</a></li>
                @auth
                <li class="nav-item"><a class="nav-link" href="#">{{ Auth::user()->name }}</a></li>
            </ul>
                <div class="nav-item">
                    <a class="btn btn-outline-light" href="#">Sign out</a>
                </div>
                @else
            </ul>
            @endauth
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
    <div id="app">
        <div class="container mt-4">
            @yield('head')
        </div>
        <div class="container-fluid">
            @yield('mid')
        </div>
        <div class="container">
            @yield('foot')
        </div>
    </div>
    @yield('bottom')
    @stack('js')
</body>
