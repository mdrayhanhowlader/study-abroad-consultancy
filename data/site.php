<?php
return [
    'brand' => [
        'name'           => 'Manami',
        'tagline'        => 'EDUCATION',
        'full_name'      => 'Manami Education',
        'slogan'         => 'Manami Leads, You Succeed...',
        'logo_image_id'  => null,
        'domain'         => 'https://manamieducation.com.bd',
        'og_image'       => 'https://manamieducation.com.bd/assets/img/og-default.jpg',
    ],
    'contact' => [
        'phone'        => '+880 1817-890995',
        'phone_tel'    => '+8801817890995',
        'email'        => 'manamieducationbd@gmail.com',
        'address'      => '20/A, B, Block No-2, Gate No-3, Monipuripara, Farmgate, Dhaka, Bangladesh, 1215',
        'address_link' => 'https://maps.google.com/?q=20%2FA+B+Block+No-2+Monipuripara+Farmgate+Dhaka+1215',
    ],
    'event' => [
        'title'    => 'Manami Study Japan Expo 2026',
        'date_iso' => date('Y-m-d', strtotime('+21 days')),
    ],
    'social' => [
        ['label' => 'Facebook',  'href' => 'https://facebook.com/',  'icon' => 'facebook'],
        ['label' => 'Instagram', 'href' => 'https://instagram.com/', 'icon' => 'instagram'],
        ['label' => 'YouTube',   'href' => 'https://youtube.com/',   'icon' => 'youtube'],
        ['label' => 'TikTok',    'href' => 'https://tiktok.com/',    'icon' => 'tiktok'],
        ['label' => 'X',         'href' => 'https://x.com/',         'icon' => 'x'],
    ],
    'nav' => [
        ['key' => 'home',         'href' => 'index.php'],
        ['key' => 'quick_order',  'href' => 'quick-order.php'],
        [
            'key' => 'study_abroad', 'children' => [
                ['key' => 'services', 'href' => 'services.php', 'class' => 'section'],
                ['key' => 'student_visa', 'href' => 'services.php#student-visa'],
                ['key' => 'ssw',          'href' => 'services.php#ssw'],
                ['key' => 'titp',         'href' => 'services.php#titp'],
                ['key' => 'japanese_lang','href' => 'services.php#japanese'],
                ['key' => 'ielts',        'href' => 'services.php#ielts'],
                ['key' => 'destinations', 'href' => 'destinations.php', 'class' => 'section'],
                ['key' => 'japan',          'href' => 'country/japan'],
                ['key' => 'australia',      'href' => 'country/australia'],
                ['key' => 'malaysia',       'href' => 'country/malaysia'],
                ['key' => 'south_korea',    'href' => 'country/south-korea'],
            ],
        ],
        ['key' => 'destinations', 'href' => 'destinations.php'],
        ['key' => 'courses',      'href' => 'courses.php'],
        ['key' => 'books_courses','href' => 'books.php'],
        ['key' => 'events',       'href' => 'events.php'],
    ],
    'footer_links' => [
        'privacy' => 'privacy.php',
        'terms'   => 'terms.php',
        'track'   => 'login.php',
    ],
];
