<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>KRLX</title>

        <link href="/css/app.css" rel="stylesheet">
        <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js" integrity="sha384-3LK/3kTpDE/Pkp8gTNp2gR/2gOiwQ6QaO7Td0zV76UFJVhqLl4Vl3KL1We6q6wR9" crossorigin="anonymous"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-krlx-red">
            <a class="navbar-brand" href="#">KRLX</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
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
        <div class="landing-card">
            <img src="/storage/cover.jpg">
            <div class="landing-headline px-3">
                <h1 class="cover">Welcome to KRLX.</h1>
                <h2>Don't worry, sometimes we mix up the letters too.</h2>
                <p class="my-3 hide-mobile">
                    <a href="#" class="btn btn-light btn-lg"><strong>Listen Live</strong></a>
                    <a href="#" class="btn btn-outline-light">Request Song</a>
                    <a href="#" class="btn btn-outline-light">Get Involved</a>
                </p>
            </div>
        </div>
        <div class="bg-black d-flex flex-wrap px-3 py-2 align-items-center">
            <span class="onair-now"><strong>On air now:</strong> No Apologies <span class="text-smallcaps">with</span> Tate B.</span>
            <span class="ml-auto">
                <a href="#" class="btn btn-outline-light btn-sm mr-2">Listen live</a>
                <a href="#" class="btn btn-outline-light btn-sm">About this show</a>
            </span>
        </div>
        <div class="bg-dark text-light d-flex flex-wrap align-items-center px-3 py-1">
            <span><strong>Up next, at 9:00 pm:</strong> Savor Kindness</span>
            <span class="ml-auto">
                <a href="#" class="text-light">Full schedule<i class="fas fa-chevron-right ml-1"></i></a>
            </span>
        </div>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="text-center">Playlist</h2>
                    <div class="list-group">
                        <div class="list-group-item">
                            <small class="text-muted">Just now</small>
                            <h5 class="my-1">Africa</h5>
                            <p class="mb-1">Weezer</p>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted">4 minutes ago</small>
                            <h5 class="my-1">no tears left to cry</h5>
                            <p class="mb-1">Ariana Grande</p>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted">8 minutes ago</small>
                            <h5 class="my-1">There's Nothing Holdin' Me Back</h5>
                            <p class="mb-1">Shawn Mendes</p>
                        </div>
                        <div class="list-group-item">
                            <small class="text-muted">12 minutes ago</small>
                            <h5 class="my-1">This Is What You Came For</h5>
                            <p class="mb-1">Calvin Harris (feat. Rihanna)</p>
                        </div>
                        <a class="list-group-item list-group-item-action text-center" href="#">
                            Full chart and playlist <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2 class="text-center">From the Blog</h2>
                </div>
                <div class="col-md-3">
                    <h2 class="text-center">Events</h2>
                </div>
            </div>
        </div>
    </body>
</html>
