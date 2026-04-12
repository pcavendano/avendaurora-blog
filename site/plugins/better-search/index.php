<?php

use Kirby\Cms\App;
use Kirby\Cms\Search;

/**
 * Override Kirby's default pages search to use AND token matching.
 *
 * Default Kirby behavior: "causa de" matches pages containing "causa" OR "de",
 * which returns noise because short filler words ("de", "la", "el") appear
 * everywhere. This component applies each token as a sequential filter, so
 * each added word narrows results instead of widening them.
 *
 * Affects: panel global search, pages-field pickers, and $pages->search() calls.
 */
App::plugin('avendaurora/better-search', [
    'components' => [
        'search' => function (App $kirby, $collection, string $query = null, $params = []) {
            $query = trim((string) $query);

            if ($query === '') {
                return $collection->limit(0);
            }

            $tokens = array_values(array_filter(
                preg_split('/\s+/', $query) ?: [],
                fn($t) => mb_strlen($t) > 0
            ));

            if (count($tokens) <= 1) {
                // Single token — defer to Kirby's built-in search
                return Search::collection($collection, $query, $params);
            }

            // Multiple tokens: intersect results by filtering sequentially
            $result = $collection;
            foreach ($tokens as $token) {
                $result = Search::collection($result, $token, $params);
                if ($result->count() === 0) {
                    break;
                }
            }

            return $result;
        }
    ]
]);
