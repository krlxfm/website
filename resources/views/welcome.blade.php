<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>KRLX</title>

        <link href="{{ mix("css/app.css") }}" rel="stylesheet">
        <script src="{{ mix("js/manifest.js") }}" defer></script>
        <script src="{{ mix("js/vendor.js") }}" defer></script>
        <script src="{{ mix("js/app.js") }}" defer></script>
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
        <div class="landing-card">
            <img src="/img/default.jpg">
            @if (session('status'))
                <div class="alert m-3 py-2 px-3 d-flex align-items-center alert-success">
                    <i class="fas fa-check fa-2x mr-3"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            <div class="landing-headline px-3">
                <h1 class="cover">Welcome to KRLX.</h1>
                <h2>{{ $messages->random() }}</h2>
                <p class="my-3 hide-mobile">
                    <a href="#" class="btn btn-light btn-lg"><strong>Listen Live</strong></a>
                    <a href="#" class="btn btn-outline-light">Request Song</a>
                    <a href="#" class="btn btn-outline-light">Get Involved</a>
                </p>
            </div>
        </div>
        @if($show)
            <div class="bg-black d-flex flex-wrap px-3 py-2 align-items-center">
                <span class="onair-now">
                    <strong>On air now:</strong> {{ $show->title }}
                    <span class="d-none d-md-inline">
                        <span class="text-smallcaps">with</span>
                        @switch($show->hosts->count())
                            @case(1)
                                {{ $show->hosts->first()->public_name }}
                                @break
                            @case(2)
                                {{ implode(' and ', $show->hosts->pluck('public_name')->all()) }}
                                @break
                            @default
                                {{ $show->hosts->first()->public_name }} and {{ $show->hosts->count() - 1 }} others
                                @break
                        @endswitch
                        </span>
                    </span>
                <span class="ml-auto">
                    <a href="#" class="btn btn-outline-light btn-sm mr-2">Listen live</a>
                    <a href="#" class="btn btn-outline-light btn-sm">About this show</a>
                </span>
            </div>
            <div class="bg-dark text-light d-flex flex-wrap align-items-center px-3 py-1">
                <span><strong>Up next, at {{ $transition }}:</strong> {{ $show->next->title }}</span>
                <span class="ml-auto">
                    <a href="#" class="text-light">Full schedule<i class="fas fa-chevron-right ml-1"></i></a>
                </span>
            </div>
        @else
            <div class="bg-black px-3 py-2 text-center">
                <span class="onair-now">
                    KRLX is currently off air due to a break in Carleton's academic schedule.
                </span>
            </div>
        @endif
        <div class="container-fluid my-4">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="text-center mb-3">Playlist</h2>
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
                    <h2 class="text-center mb-3">From the Blog</h2>
                    <div>
                        <h4 class="mb-0">Late Night Trivia 2019: What You Need to Know</h4>
                        <p class="my-1">
                            <small class="text-muted mr-2">Tate Bosler, IT Engineer</small>
                            <small class="text-muted">March 1, 2019</small>
                        </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. <a href="#">Read more<i class="fas fa-chevron-right ml-1"></i></a></p>
                    </div>
                    <hr>
                    <div>
                        <h4 class="mb-0">So Weezer Covered Africa By Toto</h4>
                        <p class="my-1">
                            <small class="text-muted mr-2">Tristan Pitt, Music Director</small>
                            <small class="text-muted">February 20, 2019</small>
                        </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. <a href="#">Read more<i class="fas fa-chevron-right ml-1"></i></a></p>
                    </div>
                    <a href="#" class="btn btn-lg btn-dark">More articles on the blog <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="col-md-3">
                    <h2 class="text-center mb-3">Events</h2>
                    <div class="list-group">
                        <div class="list-group-item">
                            <h5 class="my-1">Board of Directors Meeting</h5>
                            <p class="mb-1">Record Libe<br>Sunday, March 3, 5:00 - 6:00 pm</p>
                        </div>
                        <div class="list-group-item">
                            <h5 class="my-1">Bandemonium: Prince</h5>
                            <p class="mb-1">KRLX One<br>Hosted by Tate Bosler<br>Sunday, March 3, 5:00 - 7:00 pm</p>
                        </div>
                        <div class="list-group-item">
                            <h5 class="my-1">Mansplainless Music Monday</h5>
                            <p class="mb-1">Record Libe<br>Monday, March 4, 7:00 - 8:30 pm</p>
                        </div>
                        <div class="list-group-item">
                            <h5 class="my-1">KRLX Off Air for Spring Break</h5>
                            <p class="mb-1">KRLX One<br>Wednesday, March 13, 9:00 pm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-light py-4 px-3 text-center page-footer">
            <p class="mb-2">
                <a class="fa-stack fa-2x" href="#">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fas fa-rss fa-stack-1x"></i>
                </a>
                <a class="fa-stack fa-2x" href="https://facebook.com/krlxradio">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fab fa-facebook-f fa-stack-1x"></i>
                </a>
                <a class="fa-stack fa-2x" href="https://twitter.com/krlxfm">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fab fa-twitter fa-stack-1x"></i>
                </a>
                <a class="fa-stack fa-2x" href="https://instagram.com/krlxfm">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fab fa-instagram fa-stack-1x"></i>
                </a>
                <a class="fa-stack fa-2x" href="https://www.youtube.com/channel/UCCNkWqOvLNmDEpd7PD2BLJg">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fab fa-youtube fa-stack-1x"></i>
                </a>
                <a class="fa-stack fa-2x" href="https://www.soundcloud.com/krlxfm">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fab fa-soundcloud fa-stack-1x"></i>
                </a>
            </p>
            <p>
                Website content copyright &copy; KRLX-FM 2018. All rights reserved.<br>
                KRLX-FM is a registered student organization of <a href="https://www.carleton.edu">Carleton College</a>, Northfield, Minn., and is licensed by the FCC to broadcast.
            </p>
            Home - About - Contact - FCC Public File - Terms - Privacy
        </div>
    </body>
</html>
