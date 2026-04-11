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
        'duration' => 2592000,
        'timeout' => 2592000,
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
        ],

        // Logout
        [
            'pattern' => 'cuenta/salir',
            'action'  => function () {
                if ($user = kirby()->user()) {
                    $user->logout();
                }
                return go('/');
            }
        ],

        // Toggle favorite recipe (AJAX)
        [
            'pattern' => 'api/favorite/(:any)',
            'method'  => 'POST',
            'action'  => function ($recipeId) {
                $kirby = kirby();

                try {
                    $user = $kirby->user();
                    if (!$user) {
                        return \Kirby\Http\Response::json(['error' => 'Not signed in'], 401);
                    }

                    $recipe = $kirby->page('recetas/' . $recipeId);
                    if (!$recipe) {
                        return \Kirby\Http\Response::json(['error' => 'Recipe not found: ' . $recipeId], 404);
                    }

                    $favorites = $user->favorites()->toPages();
                    $isFavorite = $favorites->has($recipe);

                    $newIds = [];
                    foreach ($favorites as $fav) {
                        if ($fav->id() !== $recipe->id()) {
                            $newIds[] = $fav->id();
                        }
                    }
                    if (!$isFavorite) {
                        $newIds[] = $recipe->id();
                    }

                    $kirby->impersonate('kirby');
                    $user->update([
                        'favorites' => $newIds
                    ]);
                    $kirby->impersonate(null);

                    return \Kirby\Http\Response::json([
                        'favorited' => !$isFavorite,
                        'count'     => count($newIds)
                    ]);
                } catch (\Throwable $e) {
                    return \Kirby\Http\Response::json([
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
        ]
    ]
];
