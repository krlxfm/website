<?php

return [
    'directory' => 'https://apps.carleton.edu/stock/ldapimage.php?id=',
    'title' => 'KRLX Community',
    'salt' => env('OAUTH_SALT', 'krlx'),
    'priority' => [
        'default' => 'A1',
        'terms' => array_merge(range('J', 'B'), ['A3', 'A2', 'A1'])
    ],
    'show_id_length' => 6,
    'status_codes' => [
        'Non-Carleton',
        'Faculty',
        'Staff',
        'St. Olaf Faculty',
        'St. Olaf Staff'
    ],
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
            'description' => 'KRLX is occasionally played in the dining halls on Mondays. Choose a time during the Monday lunch hour to hear your show played for all of campus to hear!',
            'days' => ['Monday'],
            'start' => '11:30',
            'end' => '14:00',
        ],
    ],
];
