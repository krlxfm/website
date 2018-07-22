<?php

return [
    'groups' => [
        ['name' => '"A" classes (M/W/F)', 'classes' => ['1a', '2a', '3a', '4a', '5a', '6a']],
        ['name' => '"C" classes (Tu/Th)', 'classes' => ['1-2c', '2-3c', '4-5c', '5-6c']],
        ['name' => '"L" daily language classes', 'classes' => ['1a-L', '2a-L', '3a-L', '4a-L', '5a-L', '6a-L']],
        ['name' => 'Art classes', 'classes' => ['mw-am-art', 'mw-pm-art', 'tuth-am-art', 'tuth-pm-art']],
        ['name' => 'Morning labs', 'classes' => ['m-am-lab', 'tu-am-lab', 'w-am-lab', 'th-am-lab']],
        ['name' => 'Afternoon labs', 'classes' => ['m-pm-lab', 'tu-pm-lab', 'w-pm-lab', 'th-pm-lab']]
    ],
    'times' => [
        '1a' => [
            'name' => '1a',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '08:30', 'end' => '09:40'],
                ['days' => ['Friday'], 'start' => '08:30', 'end' => '09:30'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday', 'Friday'], 'start' => '08:00', 'end' => '10:00']
            ],
        ],
        '2a' => [
            'name' => '2a',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '09:50', 'end' => '11:00'],
                ['days' => ['Friday'], 'start' => '09:40', 'end' => '10:40'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '09:30', 'end' => '11:30'],
                ['days' => ['Friday'], 'start' => '09:30', 'end' => '11:00'],
            ],
        ],
        '3a' => [
            'name' => '3a',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '11:10', 'end' => '12:20'],
                ['days' => ['Friday'], 'start' => '12:00', 'end' => '13:00'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '11:00', 'end' => '12:30'],
                ['days' => ['Friday'], 'start' => '11:30', 'end' => '13:30'],
            ],
        ],
        '4a' => [
            'name' => '4a',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '12:30', 'end' => '13:40'],
                ['days' => ['Friday'], 'start' => '13:10', 'end' => '14:10'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '12:00', 'end' => '14:00'],
                ['days' => ['Friday'], 'start' => '13:00', 'end' => '14:30'],
            ],
        ],
        '5a' => [
            'name' => '5a',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '13:50', 'end' => '15:00'],
                ['days' => ['Friday'], 'start' => '14:20', 'end' => '15:20'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '13:30', 'end' => '15:30'],
                ['days' => ['Friday'], 'start' => '14:00', 'end' => '15:30'],
            ],
        ],
        '6a' => [
            'name' => '6a',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '15:10', 'end' => '16:20'],
                ['days' => ['Friday'], 'start' => '15:30', 'end' => '16:30'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '15:00', 'end' => '16:30'],
                ['days' => ['Friday'], 'start' => '15:00', 'end' => '17:00'],
            ],
        ],
        '1-2c' => [
            'name' => '1-2c',
            'displayTimes' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '08:15', 'end' => '10:00'],
            ],
            'times' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '08:00', 'end' => '10:30']
            ],
        ],
        '2-3c' => [
            'name' => '2-3c',
            'displayTimes' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '10:10', 'end' => '11:55'],
            ],
            'times' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '10:00', 'end' => '12:00']
            ],
        ],
        '4-5c' => [
            'name' => '4-5c',
            'displayTimes' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '13:15', 'end' => '15:00'],
            ],
            'times' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '13:00', 'end' => '15:30']
            ],
        ],
        '5-6c' => [
            'name' => '5-6c',
            'displayTimes' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '15:10', 'end' => '16:55'],
            ],
            'times' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '15:00', 'end' => '17:00']
            ],
        ],
        '1a-L' => [
            'name' => '1a Language',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '08:30', 'end' => '09:40'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '08:15', 'end' => '09:20'],
                ['days' => ['Friday'], 'start' => '08:30', 'end' => '09:30'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday', 'Friday'], 'start' => '08:00', 'end' => '10:00'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '08:00', 'end' => '09:30']
            ],
        ],
        '2a-L' => [
            'name' => '2a Language',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '09:50', 'end' => '11:00'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '09:30', 'end' => '10:35'],
                ['days' => ['Friday'], 'start' => '09:40', 'end' => '10:40'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '09:30', 'end' => '11:30'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '09:00', 'end' => '11:00'],
                ['days' => ['Friday'], 'start' => '09:30', 'end' => '11:00']
            ],
        ],
        '3a-L' => [
            'name' => '3a Language',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '11:10', 'end' => '12:20'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '10:45', 'end' => '11:50'],
                ['days' => ['Friday'], 'start' => '12:00', 'end' => '13:00'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '11:00', 'end' => '12:30'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '10:30', 'end' => '12:00'],
                ['days' => ['Friday'], 'start' => '11:30', 'end' => '13:30'],
            ],
        ],
        '4a-L' => [
            'name' => '4a Language',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '12:30', 'end' => '13:40'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '13:15', 'end' => '14:20'],
                ['days' => ['Friday'], 'start' => '13:10', 'end' => '14:10'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '12:00', 'end' => '14:00'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '13:00', 'end' => '14:30'],
                ['days' => ['Friday'], 'start' => '13:00', 'end' => '14:30'],
            ],
        ],
        '5a-L' => [
            'name' => '5a Language',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '13:50', 'end' => '15:00'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '15:10', 'end' => '16:15'],
                ['days' => ['Friday'], 'start' => '14:20', 'end' => '13:20'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '13:30', 'end' => '16:30'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '15:00', 'end' => '16:30'],
                ['days' => ['Friday'], 'start' => '14:00', 'end' => '15:30'],
            ],
        ],
        '6a-L' => [
            'name' => '6a Language',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '15:10', 'end' => '16:20'],
                ['days' => ['Tuesday', 'Thursday'], 'start' => '15:10', 'end' => '16:15'],
                ['days' => ['Friday'], 'start' => '15:30', 'end' => '16:30'],
            ],
            'times' => [
                ['days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday'], 'start' => '15:00', 'end' => '16:30'],
                ['days' => ['Friday'], 'start' => '15:00', 'end' => '17:00'],
            ],
        ],
        'mw-am-art' => [
            'name' => 'Monday/Wednesday Morning',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '08:30', 'end' => '11:00'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '08:00', 'end' => '11:30'],
            ],
        ],
        'mw-pm-art' => [
            'name' => 'Monday/Wednesday Afternoon',
            'displayTimes' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '12:30', 'end' => '15:00'],
            ],
            'times' => [
                ['days' => ['Monday', 'Wednesday'], 'start' => '12:00', 'end' => '15:30'],
            ],
        ],
        'tuth-am-art' => [
            'name' => 'Tuesday/Thursday Morning',
            'displayTimes' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '09:00', 'end' => '11:30'],
            ],
            'times' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '08:30', 'end' => '12:00'],
            ],
        ],
        'tuth-pm-art' => [
            'name' => 'Tuesday/Thursday Afternoon',
            'displayTimes' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '13:15', 'end' => '15:45'],
            ],
            'times' => [
                ['days' => ['Tuesday', 'Thursday'], 'start' => '13:00', 'end' => '16:00'],
            ],
        ],
        'm-am-lab' => [
            'name' => 'Monday Morning',
            'displayTimes' => [
                ['days' => ['Monday'], 'start' => '08:00', 'end' => '12:00'],
            ],
            'times' => [
                ['days' => ['Monday'], 'start' => '07:30', 'end' => '12:30'],
            ],
        ],
        'tu-am-lab' => [
            'name' => 'Tuesday Morning',
            'displayTimes' => [
                ['days' => ['Tuesday'], 'start' => '08:00', 'end' => '12:00'],
            ],
            'times' => [
                ['days' => ['Tuesday'], 'start' => '07:30', 'end' => '12:30'],
            ],
        ],
        'w-am-lab' => [
            'name' => 'Wednesday Morning',
            'displayTimes' => [
                ['days' => ['Wednesday'], 'start' => '08:00', 'end' => '12:00'],
            ],
            'times' => [
                ['days' => ['Wednesday'], 'start' => '07:30', 'end' => '12:30'],
            ],
        ],
        'th-am-lab' => [
            'name' => 'Thursday Morning',
            'displayTimes' => [
                ['days' => ['Thursday'], 'start' => '08:00', 'end' => '12:00'],
            ],
            'times' => [
                ['days' => ['Thursday'], 'start' => '07:30', 'end' => '12:30'],
            ],
        ],
        'm-pm-lab' => [
            'name' => 'Monday Afternoon',
            'displayTimes' => [
                ['days' => ['Monday'], 'start' => '14:00', 'end' => '18:00'],
            ],
            'times' => [
                ['days' => ['Monday'], 'start' => '13:30', 'end' => '18:30'],
            ],
        ],
        'w-pm-lab' => [
            'name' => 'Wednesday Afternoon',
            'displayTimes' => [
                ['days' => ['Wednesday'], 'start' => '14:00', 'end' => '18:00'],
            ],
            'times' => [
                ['days' => ['Wednesday'], 'start' => '13:30', 'end' => '18:30'],
            ],
        ],
        'tu-pm-lab' => [
            'name' => 'Tuesday Afternoon',
            'displayTimes' => [
                ['days' => ['Tuesday'], 'start' => '13:00', 'end' => '17:00'],
            ],
            'times' => [
                ['days' => ['Tuesday'], 'start' => '12:30', 'end' => '17:30'],
            ],
        ],
        'th-pm-lab' => [
            'name' => 'Thursday Afternoon',
            'displayTimes' => [
                ['days' => ['Thursday'], 'start' => '13:00', 'end' => '17:00'],
            ],
            'times' => [
                ['days' => ['Thursday'], 'start' => '12:30', 'end' => '17:30'],
            ],
        ],
    ],
];
