<?php

use Kirby\Cms\App;

/**
 * Override Kirby's search component to use AND-style token matching.
 *
 * The default Kirby search treats multi-word queries as OR across tokens,
 * so typing "causa de" returns every page containing "causa" or "de" —
 * which for Spanish content means hundreds of irrelevant matches. This
 * replacement filters the collection so that every token must appear in
 * one of the searchable fields. It does all matching inline (no call to
 * Kirby's built-in Search::collection), so there's no risk of recursing
 * back into this same component.
 *
 * Applies to panel global search, pages-field pickers, and $pages->search().
 */
App::plugin('avendaurora/better-search', [
    'components' => [
        'search' => function (App $kirby, $collection, string $query = null, $params = []) {
            $query = trim((string) $query);
            if ($query === '') {
                return $collection->limit(0);
            }

            $fields = $params['fields'] ?? ['title'];
            if (!is_array($fields)) {
                $fields = preg_split('/\s*,\s*/', (string) $fields) ?: ['title'];
            }

            $tokens = array_values(array_filter(
                preg_split('/\s+/', $query) ?: [],
                fn($t) => mb_strlen($t) >= 2
            ));
            if (empty($tokens)) {
                $tokens = [$query];
            }
            $tokens = array_map('mb_strtolower', $tokens);

            $matched = $collection->filter(function ($item) use ($tokens, $fields) {
                $parts = [];

                if (method_exists($item, 'title')) {
                    $parts[] = (string) $item->title();
                }
                if (method_exists($item, 'id')) {
                    $parts[] = (string) $item->id();
                }
                if (method_exists($item, 'email')) {
                    try { $parts[] = (string) $item->email(); } catch (\Throwable $e) {}
                }
                if (method_exists($item, 'filename')) {
                    try { $parts[] = (string) $item->filename(); } catch (\Throwable $e) {}
                }

                foreach ($fields as $field) {
                    try {
                        if (method_exists($item, $field)) {
                            $parts[] = (string) $item->$field();
                        } elseif (method_exists($item, 'content')) {
                            $v = $item->content()->get($field);
                            if ($v !== null) {
                                $parts[] = (string) $v;
                            }
                        }
                    } catch (\Throwable $e) {
                        // skip missing fields
                    }
                }

                $haystack = mb_strtolower(implode(' ', $parts));
                foreach ($tokens as $token) {
                    if (mb_strpos($haystack, $token) === false) {
                        return false;
                    }
                }
                return true;
            });

            if (isset($params['limit']) && (int) $params['limit'] > 0) {
                $matched = $matched->limit((int) $params['limit']);
            }

            return $matched;
        }
    ]
]);
