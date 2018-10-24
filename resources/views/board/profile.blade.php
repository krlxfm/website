<h2>Profile</h2>
<p>Your profile is where you'll enter your bio, major (if you have one), hometown, and other information about yourself. This information is published on the <a href="{{ route('board.meet') }}">Meet the Board</a> page, as well as public locations, for all board members. We'd love for your information to be populated right away if you get elected, so please take the time to make sure your profile is up to date.</p>
<p>Note that while board members do have access to this information, <em>it has absolutely no impact on the decision-making process.</em></p>
<p>Board candidates need to have the following fields set:</p>
<ul>
    <li>
        Bio
        @include('board.panelicon', ['complete' => !empty(Auth::user()->bio)])
    </li>
    <li>
        Hometown
        @include('board.panelicon', ['complete' => !empty(Auth::user()->hometown)])
    </li>
    <li>
        Pronouns
        @include('board.panelicon', ['complete' => !empty(Auth::user()->pronouns)])
    </li>
    <li>
        Major (if you don't have one, enter "undecided" or "undeclared")
        @include('board.panelicon', ['complete' => !empty(Auth::user()->major)])
    </li>
</ul>
<p>You are highly encouraged (though not required) to fill in the other profile fields.</p>
<a href="{{ route('profile') }}" class="btn btn-lg btn-secondary">Edit profile</a>
