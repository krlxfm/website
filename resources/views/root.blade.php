<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>KRLX BETA TEST</title>

        <link href="{{ mix("css/app.css") }}" rel="stylesheet">
        <script src="{{ mix("js/manifest.js") }}" defer></script>
        <script src="{{ mix("js/vendor.js") }}" defer></script>
        <script src="{{ mix("js/app.js") }}" defer></script>
        <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js" integrity="sha384-3LK/3kTpDE/Pkp8gTNp2gR/2gOiwQ6QaO7Td0zV76UFJVhqLl4Vl3KL1We6q6wR9" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container mt-4">
            <h1>Hello, beta testers!</h1>
            <p>First of all, <strong>THANK YOU</strong> again for helping test out the site. I'm really excited to hear your feedback, and I'm grateful for your assistance in verifying that the site works as expected. <strong>Welcome to Beta Two!</strong></p>

            <h3>Your mission, should you choose to accept it:</h3>
            <p>Sign in with your Carleton account, then:</p>
            <ul>
                <li>Create a group show on any track, and invite another beta tester. (Let me know if you need the list.)</li>
                <li>Accept an invitation to join a show from someone else, then add a conflict or class to the schedule.</li>
                <li>Join a show by Show ID.</li>
                <li>Create and submit a Bandemonium show.</li>
            </ul>

            <h3>While testing, keep in mind:</h3>
            <ul>
                <li><strong>THIS IS A BETA.</strong> It should work reasonably well, but don't expect a flawless experience (especially with regards to cosmetics).</li>
                <li>The point of this beta is to verify that individual shows can be created and saved correctly and without too much of a hassle. While you are welcome to enter your full schedule into your show applications to test that it all works, don't worry about getting it perfect. <strong>Applications created here will not automatically transfer over to the official set.</strong></li>
                <li>There is currently no way to delete show applications. If you need to delete one or withdraw it from "submitted" status, let me know. This should be fixed in a future update.</li>
                <li>You win if you break something.</li>
            </ul>

            <h3>When something breaks, let me know!</h3>
            <p>You can send me feedback and bug reports via email. Or, if you have a GitHub account, feel free to <a href="https://github.com/krlxfm/website/issues">open an issue on the GitHub project.</a></p>
            <p>Anyone who reports a bug will get a shout-out in <a href="https://github.com/krlxfm/website/releases">the release that fixes it.</a></p>

            <h3>Entry into A1 priority drawing and Fall 2018 early access</h3>
            <p>You'll need to do <strong>ALL</strong> of the following in order to get early access to the Fall 2018 applications, and to get entered into the drawing for A1 priority:</p>
            <ul>
                <li>Be on at least one group show.</li>
                <li>Create and submit at least one Bandemonium show.</li>
                <li>Let me know how it went by <a href="http://go.tatebosler.com/krlx-beta2">filling out the feedback survey.</a> (It's a different link than last time.)</li>
            </ul>
            <p>Of course, if you have any questions or run into problems, please get in touch!</p>

            <p><a href="/login" class="btn btn-lg btn-block btn-primary" style="font-size: xx-large"><strong>Get Started!</strong></a></p>
        </div>
    </body>
</html>
