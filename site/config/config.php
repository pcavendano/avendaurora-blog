<?php

return [
    'debug' => true,

    'languages' => true,
    'languages.detect' => true,

    'panel' => [
        'install' => true,
        'slug' => 'panel',
        'language' => 'es',
    ],

    'auth' => [
        'methods' => ['password'],
        'challenge' => [
            'timeout' => 10
        ],
        'trials' => 5
    ],

    'session' => [
        'timeout' => 120,
        'cookieName' => 'avendaurora_session',
    ],

    'cache' => [
        'pages' => [
            'active' => false // Enable in production
        ]
    ],

    'thumbs' => [
        'srcsets' => [
            'default' => [300, 600, 900, 1200, 1800],
            'cover' => [800, 1024, 1440, 2048]
        ]
    ],

    'routes' => [
        // Redirect /recipes to /recetas for Spanish default
        [
            'pattern' => 'recipes',
            'action'  => function () {
                return go('recetas');
            }
        ],
        [
            'pattern' => 'ingredients',
            'action'  => function () {
                return go('ingredientes');
            }
        ]
    ]
];
