<?php

return [
    'directory' => 'https://apps.carleton.edu/stock/ldapimage.php?id=',
    'title' => 'KRLX Community',
    'salt' => env('OAUTH_SALT', 'krlx'),
    'show_id_length' => 6,
    'special_times' => [
        'safe-harbor' => [
            'name' => 'Safe Harbor Hours',
            'description' => 'Federal law prohibits broadcasting indecent or profane content during most of the day, however this content is permitted in the "safe harbor" overnight hours between 10:00 pm and 6:00 am so long as it retains artistic merit and is not obscene. Explicit music can ONLY be played in the safe harbor.',
            'days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            'start' => '22:00',
            'end' => '06:00',
        ],
        'music-mondays' => [
            'name' => 'Music Mondays',
            'description' => 'KRLX is occasionally played in the dining halls on Mondays. Choose a time during Monday the lunch hour to hear your show played for all of campus to hear!',
            'days' => ['Monday'],
            'start' => '11:30',
            'end' => '14:00',
        ],
    ],
];
