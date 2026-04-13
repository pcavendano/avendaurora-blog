<?php

use Kirby\Cms\App;

App::plugin('avendaurora/recipe-authors', [
    'hooks' => [
        // Auto-populate created_by on every new recipe, unless already set
        // (the importer sets it explicitly before impersonating, so this is a
        // safety net for panel-created recipes).
        'page.create:after' => function ($page) {
            if ($page->intendedTemplate()->name() !== 'recipe') {
                return;
            }
            if ($page->content()->get('created_by')->isNotEmpty()) {
                return;
            }

            $user = kirby()->user();
            if (!$user) {
                return;
            }
            // Skip the impersonated "nobody" system user
            if ($user->role()->id() === 'nobody') {
                return;
            }

            kirby()->impersonate('kirby');
            $page->update(['created_by' => $user->id()]);
            kirby()->impersonate(null);
        }
    ],

    'routes' => [
        // One-shot migration: creates a Sistema user and assigns it as
        // created_by on every recipe that does not have a creator yet.
        [
            'pattern' => 'api/admin/migrate-authors',
            'method'  => 'POST',
            'action'  => function () {
                $kirby = kirby();
                $user = $kirby->user();
                if (!$user || $user->role()->name() !== 'admin') {
                    return \Kirby\Http\Response::json(['error' => 'Admin only'], 403);
                }

                try {
                    $systemEmail = 'sistema@avendaurora.local';
                    $systemUser = $kirby->users()->find($systemEmail);

                    $kirby->impersonate('kirby');

                    if (!$systemUser) {
                        $systemUser = $kirby->users()->create([
                            'email'    => $systemEmail,
                            'password' => bin2hex(random_bytes(32)),
                            'role'     => 'member',
                            'content'  => [
                                'display_name' => 'Sistema',
                            ],
                        ]);
                    }

                    $recetas = $kirby->page('recetas');
                    if (!$recetas) {
                        $kirby->impersonate(null);
                        return \Kirby\Http\Response::json(['error' => 'Recetas parent not found'], 404);
                    }

                    $migrated = 0;
                    $total = 0;
                    foreach ($recetas->children() as $recipe) {
                        $total++;
                        if ($recipe->content()->get('created_by')->isEmpty()) {
                            $recipe->update(['created_by' => $systemUser->id()]);
                            $migrated++;
                        }
                    }

                    $kirby->impersonate(null);

                    return \Kirby\Http\Response::json([
                        'system_user_id' => $systemUser->id(),
                        'system_email'   => $systemUser->email(),
                        'total_recipes'  => $total,
                        'migrated'       => $migrated,
                    ]);
                } catch (\Throwable $e) {
                    $kirby->impersonate(null);
                    return \Kirby\Http\Response::json(['error' => $e->getMessage()], 500);
                }
            }
        ]
    ]
]);
