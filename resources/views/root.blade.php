<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>KRLX Mission Control</title>

        <link href="{{ mix("css/app.css") }}" rel="stylesheet">
        <script src="{{ mix("js/manifest.js") }}" defer></script>
        <script src="{{ mix("js/vendor.js") }}" defer></script>
        <script src="{{ mix("js/app.js") }}" defer></script>
        <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js" integrity="sha384-3LK/3kTpDE/Pkp8gTNp2gR/2gOiwQ6QaO7Td0zV76UFJVhqLl4Vl3KL1We6q6wR9" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container mt-5">
            <div class="px-5 pb-5">
                <img src="/img/logo.png" class="img-fluid">
            </div>

            <p class="text-center">Yeah, it's not the prettiest landing page... we're working on it. But if you are looking to get a radio show on KRLX-FM, you're in the right place.</p>
            <p><a href="/login" class="btn btn-lg btn-block btn-primary" style="font-size: xx-large"><strong>Sign in</strong></a></p>
        </div>
    </body>
</html>
