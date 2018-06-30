<?php

return [
    'rules' => [
        'name' => 'string|min:3|max:190|unique:tracks',
        'description' => 'string|min:20',
        'active' => 'boolean',
        'boostable' => 'boolean',
        'clonable' => 'boolean',
        'allows_images' => 'boolean',
        'can_fall_back' => 'boolean',
        'taggable' => 'boolean',
        'awards_xp' => 'boolean',
        'prefix' => 'nullable|string',
        'zone' => 'nullable|string|alpha|size:1',
        'group' => 'nullable|integer|min:0|max:100',
        'order' => 'integer|min:0|max:65500',
        'allows_direct_add' => 'boolean',
        'joinable' => 'boolean',
        'max_participants' => 'nullable|integer|min:0|max:200',
        'title_label' => 'nullable|string|max:190',
        'description_label' => 'nullable|string|max:190',
        'description_min_length' => 'nullable|integer|min:0|max:65500',
        'weekly' => 'boolean',
        'start_day' => [
            'nullable',
            'required_with_all:start_time,end_time',
            'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'
        ],
        'start_time' => [
            'nullable',
            'required_with_all:start_day,end_time',
            'regex:/^(([01][0-9])|(2[0-3])):[0-9]{2}$/'
        ],
        'end_time' => [
            'nullable',
            'required_with_all:start_day,start_time',
            'regex:/^(([01][0-9])|(2[0-3])):[0-9]{2}$/'
        ]
    ]
];
