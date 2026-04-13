<?php

use Kirby\Cms\App;

/**
 * Taxonomy helpers: categories and countries are managed as a structure field
 * on the Recetas page. This plugin exposes them via page methods, admin API
 * routes (for the importer wizard), and a localized label lookup.
 */

class Taxonomy
{
    public static function all(string $field): array
    {
        $recetas = kirby()->page('recetas');
        if (!$recetas) {
            return [];
        }
        $items = [];
        foreach ($recetas->$field()->toStructure() as $item) {
            $slug = (string) $item->slug();
            if ($slug === '') continue;
            $items[] = [
                'slug'     => $slug,
                'label_es' => (string) $item->label_es(),
                'label_en' => (string) $item->label_en(),
                'label_fr' => (string) $item->label_fr(),
            ];
        }
        return $items;
    }

    public static function label(string $field, string $slug, ?string $lang = null): string
    {
        $lang = $lang ?: kirby()->language()?->code() ?: 'es';
        $labelKey = 'label_' . $lang;
        foreach (self::all($field) as $item) {
            if ($item['slug'] === $slug) {
                return $item[$labelKey] ?: $item['label_es'] ?: $slug;
            }
        }
        return $slug;
    }

    public static function slugs(string $field): array
    {
        return array_map(fn($i) => $i['slug'], self::all($field));
    }
}

App::plugin('avendaurora/taxonomy', [
    'pageMethods' => [
        'categoryLabel' => function (string $slug) {
            return Taxonomy::label('categories', $slug);
        },
        'countryLabel' => function (string $slug) {
            return Taxonomy::label('countries', $slug);
        }
    ],
    'routes' => [
        [
            'pattern' => 'api/taxonomy/(:any)',
            'method'  => 'GET',
            'action'  => function ($field) {
                $kirby = kirby();
                $user = $kirby->user();
                if (!$user || $user->role()->name() !== 'admin') {
                    return \Kirby\Http\Response::json(['error' => 'Admin only'], 403);
                }
                if (!in_array($field, ['categories', 'countries'], true)) {
                    return \Kirby\Http\Response::json(['error' => 'Unknown taxonomy'], 404);
                }
                return \Kirby\Http\Response::json([
                    'items' => Taxonomy::all($field),
                ]);
            }
        ]
    ]
]);
