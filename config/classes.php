<?php

return [
    'groups' => [
        ['name' => '"A" classes (M/W/F)', 'classes' => ['1a', '2a', '3a', '4a', '5a', '6a']],
        ['name' => '"C" classes (Tu/Th)', 'classes' => ['1-2c', '2-3c', '4-5c', '5-6c']],
        ['name' => '"L" daily language classes', 'classes' => ['1a-L', '2a-L', '3a-L', '4a-L', '5a-L', '6a-L']]
    ],
    'times' => [
        '1a' => [['days' => ['Monday', 'Wednesday'], 'start' => '08:30', 'end' => '10:00'],
                 ['days' => ['Friday'], 'start' => '08:30', 'end' => '09:30']],
        '2a' => [['days' => ['Monday', 'Wednesday', 'Friday'], 'start' => '09:30', 'end' => '11:00']],
        '3a' => [['days' => ['Monday', 'Wednesday'], 'start' => '11:00', 'end' => '12:30'],
                 ['days' => ['Friday'], 'start' => '12:00', 'end' => '13:00']],
        '4a' => [['days' => ['Monday', 'Wednesday'], 'start' => '12:30', 'end' => '14:00'],
                 ['days' => ['Friday'], 'start' => '13:00', 'end' => '14:30']],
        '5a' => [['days' => ['Monday', 'Wednesday'], 'start' => '13:30', 'end' => '15:00'],
                 ['days' => ['Friday'], 'start' => '14:00', 'end' => '15:30']],
        '6a' => [['days' => ['Monday', 'Wednesday'], 'start' => '15:00', 'end' => '16:30'],
                 ['days' => ['Friday'], 'start' => '15:30', 'end' => '16:30']],
        '1-2c' => [['days' => ['Tuesday', 'Thursday'], 'start' => '08:00', 'end' => '10:00']],
        '2-3c' => [['days' => ['Tuesday', 'Thursday'], 'start' => '10:00', 'end' => '12:00']],
        '4-5c' => [['days' => ['Tuesday', 'Thursday'], 'start' => '13:00', 'end' => '15:00']],
        '5-6c' => [['days' => ['Tuesday', 'Thursday'], 'start' => '15:00', 'end' => '17:00']],
        '1a-L' => [['days' => ['Monday', 'Wednesday'], 'start' => '08:30', 'end' => '10:00'],
                   ['days' => ['Friday'], 'start' => '08:30', 'end' => '09:30'],
                   ['days' => ['Tuesday', 'Thursday'], 'start' => '08:00', 'end' => '09:30']],
        '2a-L' => [['days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], 'start' => '09:30', 'end' => '11:00']],
        '3a-L' => [['days' => ['Monday', 'Wednesday'], 'start' => '11:00', 'end' => '12:30'],
                   ['days' => ['Friday'], 'start' => '12:00', 'end' => '13:00'],
                   ['days' => ['Tuesday', 'Thursday'], 'start' => '10:30', 'end' => '12:00']],
        '4a-L' => [['days' => ['Monday', 'Wednesday'], 'start' => '12:30', 'end' => '14:00'],
                   ['days' => ['Tuesday', 'Thursday', 'Friday'], 'start' => '13:00', 'end' => '14:30']],
        '5a-L' => [['days' => ['Monday', 'Wednesday'], 'start' => '13:30', 'end' => '15:00'],
                   ['days' => ['Friday'], 'start' => '14:00', 'end' => '15:30'],
                   ['days' => ['Tuesday', 'Thursday'], 'start' => '13:00', 'end' => '14:30']],
        '6a-L' => [['days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday'], 'start' => '15:00', 'end' => '16:30'],
                   ['days' => ['Friday'], 'start' => '15:30', 'end' => '16:30']]
    ]
];
