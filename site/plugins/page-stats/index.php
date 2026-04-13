<?php

use Kirby\Cms\App;

/**
 * Lightweight page view tracker. Stores a counter per page id in a single
 * JSON file under storage/stats. Uses flock() for concurrent safety.
 * Skips admin users and common bots so stats reflect real visitors.
 */
class PageStats
{
    private static function file(): string
    {
        return kirby()->root('storage') . '/stats/page-views.json';
    }

    public static function shouldRecord(): bool
    {
        // Skip admin — their own clicks shouldn't skew the stats
        $user = kirby()->user();
        if ($user && $user->role()->name() === 'admin') {
            return false;
        }

        // Skip obvious bots
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if ($ua === '' || preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|whatsapp|curl|wget|headlesschrome/i', $ua)) {
            return false;
        }

        return true;
    }

    public static function record(string $pageId): void
    {
        if (!self::shouldRecord()) {
            return;
        }

        $file = self::file();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $fp = @fopen($file, 'c+');
        if (!$fp) {
            return;
        }
        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return;
        }

        $content = stream_get_contents($fp);
        $data = json_decode($content ?: '{}', true);
        if (!is_array($data)) {
            $data = [];
        }
        $data[$pageId] = ($data[$pageId] ?? 0) + 1;

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    public static function get(string $pageId): int
    {
        $data = self::all();
        return (int) ($data[$pageId] ?? 0);
    }

    public static function all(): array
    {
        $file = self::file();
        if (!file_exists($file)) {
            return [];
        }
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    public static function top(int $limit = 10): array
    {
        $all = self::all();
        arsort($all);
        return array_slice($all, 0, $limit, true);
    }
}

App::plugin('avendaurora/page-stats', [
    'pageMethods' => [
        'views' => function () {
            return PageStats::get($this->id());
        }
    ],
    'routes' => [
        // Admin endpoint: return full stats for a dashboard
        [
            'pattern' => 'api/admin/stats',
            'method'  => 'GET',
            'action'  => function () {
                $user = kirby()->user();
                if (!$user || $user->role()->name() !== 'admin') {
                    return \Kirby\Http\Response::json(['error' => 'Admin only'], 403);
                }
                $all = PageStats::all();
                arsort($all);
                $total = array_sum($all);
                return \Kirby\Http\Response::json([
                    'total_views' => $total,
                    'pages'       => $all,
                    'top_10'      => array_slice($all, 0, 10, true),
                ]);
            }
        ]
    ]
]);
