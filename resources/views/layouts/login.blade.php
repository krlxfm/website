<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'Mission Control') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://use.fontawesome.com/releases/v5.1.0/js/all.js" integrity="sha384-3LK/3kTpDE/Pkp8gTNp2gR/2gOiwQ6QaO7Td0zV76UFJVhqLl4Vl3KL1We6q6wR9" crossorigin="anonymous" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="auth">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">KRLX</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Record Libe</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Playlist</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Schedule</a></li>
            </ul>
            <div class="nav-item">
                <a class="btn btn-outline-light mr-2" href="#">Listen Live</a>
            </div>
            <div class="nav-item">
                <a class="btn btn-outline-light" href="#">Mission Control</a>
            </div>
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
    <div class="container mt-4">
        <div class="col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
            <div class="card card-auth">
                <div class="card-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>
