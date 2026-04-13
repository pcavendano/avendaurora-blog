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

        // List existing ingredient pages (for autocomplete)
        [
            'pattern' => 'api/ingredients',
            'method'  => 'GET',
            'action'  => function () {
                $kirby = kirby();
                $user = $kirby->user();
                if (!$user || $user->role()->name() !== 'admin') {
                    return \Kirby\Http\Response::json(['error' => 'Admin only'], 403);
                }
                $parent = $kirby->page('ingredientes');
                if (!$parent) {
                    return \Kirby\Http\Response::json(['ingredients' => []]);
                }
                $items = [];
                foreach ($parent->children() as $ingredient) {
                    $items[] = [
                        'id'    => $ingredient->id(),
                        'title' => (string) $ingredient->title(),
                        'slug'  => $ingredient->slug(),
                    ];
                }
                usort($items, fn($a, $b) => strcasecmp($a['title'], $b['title']));
                return \Kirby\Http\Response::json(['ingredients' => $items]);
            }
        ],

        // Recipe importer: extract recipe from image via OpenAI
        [
            'pattern' => 'api/import/extract',
            'method'  => 'POST',
            'action'  => function () {
                $kirby = kirby();
                $user = $kirby->user();
                if (!$user || $user->role()->name() !== 'admin') {
                    return \Kirby\Http\Response::json(['error' => 'Admin only'], 403);
                }

                try {
                    $upload = $_FILES['image'] ?? null;
                    if (!$upload || $upload['error'] !== UPLOAD_ERR_OK) {
                        return \Kirby\Http\Response::json(['error' => 'No file uploaded'], 400);
                    }
                    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!in_array($upload['type'], $allowed, true)) {
                        return \Kirby\Http\Response::json(['error' => 'Unsupported file type: ' . $upload['type']], 400);
                    }
                    if ($upload['size'] > 10 * 1024 * 1024) {
                        return \Kirby\Http\Response::json(['error' => 'File too large (max 10 MB)'], 400);
                    }

                    $recipe = RecipeImporter::extractFromImage($upload['tmp_name'], $upload['type']);

                    // Cache the upload to a temp file keyed by session so we can reuse as cover
                    $cacheDir = $kirby->root('cache') . '/imports';
                    if (!is_dir($cacheDir)) {
                        mkdir($cacheDir, 0775, true);
                    }
                    $token = bin2hex(random_bytes(16));
                    $ext = match ($upload['type']) {
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                        default => 'jpg',
                    };
                    $cachedPath = $cacheDir . '/' . $token . '.' . $ext;
                    move_uploaded_file($upload['tmp_name'], $cachedPath);

                    return \Kirby\Http\Response::json([
                        'recipe' => $recipe,
                        'image_token' => $token,
                        'image_filename' => $upload['name'],
                    ]);
                } catch (\Throwable $e) {
                    return \Kirby\Http\Response::json(['error' => $e->getMessage()], 500);
                }
            }
        ],

        // Save extracted recipe as draft
        [
            'pattern' => 'api/import/save',
            'method'  => 'POST',
            'action'  => function () {
                $kirby = kirby();
                $user = $kirby->user();
                if (!$user || $user->role()->name() !== 'admin') {
                    return \Kirby\Http\Response::json(['error' => 'Admin only'], 403);
                }

                try {
                    $raw = file_get_contents('php://input');
                    $payload = json_decode($raw, true);
                    if (!is_array($payload) || !isset($payload['recipe'])) {
                        return \Kirby\Http\Response::json(['error' => 'Invalid payload'], 400);
                    }

                    $cover = null;
                    if (!empty($payload['image_token'])) {
                        $token = preg_replace('/[^a-f0-9]/', '', $payload['image_token']);
                        $cacheDir = $kirby->root('cache') . '/imports';
                        foreach (['jpg', 'png', 'webp'] as $ext) {
                            $candidate = $cacheDir . '/' . $token . '.' . $ext;
                            if (file_exists($candidate)) {
                                $cover = [
                                    'path' => $candidate,
                                    'filename' => ($payload['image_filename'] ?? ('cover.' . $ext)),
                                ];
                                break;
                            }
                        }
                    }

                    $pageId = RecipeImporter::saveDraft($payload['recipe'], $cover);

                    if ($cover && file_exists($cover['path'])) {
                        @unlink($cover['path']);
                    }

                    return \Kirby\Http\Response::json([
                        'page_id' => $pageId,
                        'panel_url' => $kirby->url('panel') . '/pages/' . str_replace('/', '+', $pageId),
                    ]);
                } catch (\Throwable $e) {
                    return \Kirby\Http\Response::json(['error' => $e->getMessage()], 500);
                }
            }
        ],

        // Translate an existing draft to another language
        [
            'pattern' => 'api/import/translate',
            'method'  => 'POST',
            'action'  => function () {
                $kirby = kirby();
                $user = $kirby->user();
                if (!$user || $user->role()->name() !== 'admin') {
                    return \Kirby\Http\Response::json(['error' => 'Admin only'], 403);
                }

                try {
                    $raw = file_get_contents('php://input');
                    $payload = json_decode($raw, true);
                    $pageId = $payload['page_id'] ?? null;
                    $lang = $payload['lang'] ?? null;

                    if (!$pageId || !in_array($lang, ['en', 'fr'], true)) {
                        return \Kirby\Http\Response::json(['error' => 'Invalid page_id or lang'], 400);
                    }

                    $page = $kirby->page($pageId);
                    if (!$page) {
                        return \Kirby\Http\Response::json(['error' => 'Draft not found'], 404);
                    }

                    // Reconstruct recipe dict from current page (Spanish base)
                    $recipe = [
                        'title'             => $page->title()->value(),
                        'description'       => $page->description()->value(),
                        'category'          => $page->category()->split(','),
                        'subcategory'       => $page->subcategory()->split(','),
                        'country'           => $page->country()->value(),
                        'region'            => $page->region()->value(),
                        'prep_time_minutes' => $page->prep_time()->toInt() ?: null,
                        'cook_time_minutes' => $page->cook_time()->toInt() ?: null,
                        'total_time_minutes' => $page->total_time()->toInt() ?: null,
                        'servings'          => $page->servings()->toInt() ?: null,
                        'difficulty'        => $page->difficulty()->value() ?: null,
                        'ingredients'       => array_map(fn($i) => [
                            'ingredient'  => (string) $i->ingredient(),
                            'quantity'    => (string) $i->quantity(),
                            'unit'        => (string) $i->unit(),
                            'preparation' => (string) $i->preparation(),
                            'optional'    => $i->optional()->toBool(),
                        ], iterator_to_array($page->ingredients()->toStructure())),
                        'instructions'      => array_map(fn($s) => [
                            'step_title'  => (string) $s->step_title(),
                            'instruction' => (string) $s->instruction(),
                            'tip'         => (string) $s->tip(),
                        ], iterator_to_array($page->instructions()->toStructure())),
                        'tips'              => (string) $page->tips(),
                        'history'           => (string) $page->history(),
                        'tags'              => $page->tags()->split(','),
                    ];

                    $translated = RecipeImporter::translate($recipe, $lang);
                    RecipeImporter::updateDraftLanguage($pageId, $lang, $translated);

                    return \Kirby\Http\Response::json([
                        'ok' => true,
                        'lang' => $lang,
                    ]);
                } catch (\Throwable $e) {
                    return \Kirby\Http\Response::json(['error' => $e->getMessage()], 500);
                }
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
