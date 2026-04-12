<?php

use Kirby\Cms\App;
use Kirby\Data\Data;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;

class RecipeImporter
{
    private const EXTRACT_SYSTEM_PROMPT = <<<PROMPT
You are a recipe extraction assistant. Given an image of a recipe, extract the content as structured JSON matching the provided schema. Always respond in Spanish (es) unless the source is clearly written in another language, in which case translate fields that make sense into Spanish while keeping ingredient brand/name proper nouns. Be conservative: if a field is not clearly present, leave it empty or null. For times, infer in minutes. Always populate ingredients and instructions if present. Categories must come from the allowed enum.
PROMPT;

    private const ALLOWED_CATEGORIES = [
        'antojitos', 'platos-fuertes', 'sopas-caldos', 'salsas',
        'mariscos', 'desayunos', 'postres', 'bebidas', 'vegetarianos'
    ];

    private const ALLOWED_UNITS = [
        '', 'piezas', 'tazas', 'cucharadas', 'cucharaditas',
        'gramos', 'kg', 'ml', 'litros', 'lb', 'oz'
    ];

    private const ALLOWED_DIFFICULTY = ['', 'easy', 'medium', 'hard'];

    private const ALLOWED_REGIONS = [
        '', 'oaxaca', 'yucatan', 'jalisco', 'michoacan',
        'veracruz', 'puebla', 'norte', 'centro', 'sur', 'costeno'
    ];

    public static function apiKey(): ?string
    {
        $key = getenv('OPENAI_API_KEY') ?: null;
        if (!$key) {
            $secretsFile = kirby()->root('config') . '/secrets.php';
            if (file_exists($secretsFile)) {
                $secrets = require $secretsFile;
                $key = $secrets['OPENAI_API_KEY'] ?? null;
            }
        }
        return $key;
    }

    public static function extractFromImage(string $imagePath, string $mime): array
    {
        $apiKey = self::apiKey();
        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not configured. Set OPENAI_API_KEY in site/config/secrets.php');
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        $dataUrl = 'data:' . $mime . ';base64,' . $imageData;

        $payload = [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => self::EXTRACT_SYSTEM_PROMPT],
                ['role' => 'user', 'content' => [
                    ['type' => 'text', 'text' => 'Extract this recipe.'],
                    ['type' => 'image_url', 'image_url' => ['url' => $dataUrl]],
                ]],
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'recipe',
                    'strict' => true,
                    'schema' => self::recipeSchema(),
                ],
            ],
        ];

        $response = self::callOpenAI($payload, $apiKey);
        $content = $response['choices'][0]['message']['content'] ?? null;
        if (!$content) {
            throw new \RuntimeException('Empty response from OpenAI');
        }

        $recipe = json_decode($content, true);
        if (!$recipe) {
            throw new \RuntimeException('Failed to parse recipe JSON: ' . json_last_error_msg());
        }

        $recipe['_usage'] = $response['usage'] ?? null;
        return $recipe;
    }

    public static function translate(array $recipe, string $targetLang): array
    {
        $apiKey = self::apiKey();
        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not configured.');
        }

        $langName = match ($targetLang) {
            'en' => 'English',
            'fr' => 'French',
            'es' => 'Spanish',
            default => $targetLang,
        };

        unset($recipe['_usage']);
        $recipeJson = json_encode($recipe, JSON_UNESCAPED_UNICODE);

        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => "You translate recipe JSON into {$langName}. Keep the exact same JSON structure and keys. Translate all human-readable fields (title, description, ingredient names, instructions, tips, history, step_title, preparation, tags). Leave enum fields (category, unit, difficulty, region) untouched — they must stay in Spanish. Keep quantities as-is. Keep cover, subcategory as-is unless tags need translation. Return JSON only."],
                ['role' => 'user', 'content' => $recipeJson],
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'recipe',
                    'strict' => true,
                    'schema' => self::recipeSchema(),
                ],
            ],
        ];

        $response = self::callOpenAI($payload, $apiKey);
        $content = $response['choices'][0]['message']['content'] ?? null;
        if (!$content) {
            throw new \RuntimeException('Empty translation response');
        }

        $translated = json_decode($content, true);
        if (!$translated) {
            throw new \RuntimeException('Failed to parse translation JSON');
        }
        $translated['_usage'] = $response['usage'] ?? null;
        return $translated;
    }

    private static function callOpenAI(array $payload, string $apiKey): array
    {
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 120,
        ]);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            throw new \RuntimeException('OpenAI request failed: ' . $err);
        }

        $decoded = json_decode($body, true);
        if ($status !== 200) {
            $msg = $decoded['error']['message'] ?? $body;
            throw new \RuntimeException("OpenAI HTTP {$status}: {$msg}");
        }

        return $decoded;
    }

    public static function saveDraft(array $recipe, ?array $coverImage = null): string
    {
        $title = trim((string)($recipe['title'] ?? ''));
        if ($title === '') {
            throw new \RuntimeException('Recipe title is required');
        }

        $slug = Str::slug($title);
        $recetas = kirby()->page('recetas');
        if (!$recetas) {
            throw new \RuntimeException('Recetas parent page not found');
        }

        $existing = $recetas->find($slug);
        if ($existing) {
            $slug .= '-' . date('YmdHis');
        }

        kirby()->impersonate('kirby');
        $page = $recetas->createChild([
            'slug' => $slug,
            'template' => 'recipe',
            'draft' => true,
            'content' => self::recipeToContent($recipe),
        ]);

        if ($coverImage && file_exists($coverImage['path'])) {
            $page->createFile([
                'source' => $coverImage['path'],
                'filename' => $coverImage['filename'],
                'template' => 'recipe-image',
            ]);
            $coverFile = $page->files()->first();
            if ($coverFile) {
                $page = $page->update(['cover' => $coverFile->filename()]);
            }
        }

        kirby()->impersonate(null);
        return $page->id();
    }

    public static function updateDraftLanguage(string $pageId, string $lang, array $recipe): void
    {
        $page = kirby()->page($pageId);
        if (!$page) {
            throw new \RuntimeException('Draft not found: ' . $pageId);
        }
        kirby()->impersonate('kirby');
        $page->update(self::recipeToContent($recipe), $lang);
        kirby()->impersonate(null);
    }

    private static function recipeToContent(array $r): array
    {
        return array_filter([
            'title'       => $r['title'] ?? '',
            'description' => $r['description'] ?? '',
            'category'    => self::cleanEnumList($r['category'] ?? [], self::ALLOWED_CATEGORIES),
            'subcategory' => self::listToString($r['subcategory'] ?? []),
            'region'      => self::cleanEnum($r['region'] ?? '', self::ALLOWED_REGIONS),
            'prep_time'   => $r['prep_time_minutes'] ?? '',
            'cook_time'   => $r['cook_time_minutes'] ?? '',
            'total_time'  => $r['total_time_minutes'] ?? '',
            'servings'    => $r['servings'] ?? '',
            'difficulty'  => self::cleanEnum($r['difficulty'] ?? '', self::ALLOWED_DIFFICULTY),
            'ingredients' => array_map(fn($i) => [
                'quantity'        => (string)($i['quantity'] ?? ''),
                'unit'            => self::cleanEnum($i['unit'] ?? '', self::ALLOWED_UNITS),
                'ingredient'      => $i['ingredient'] ?? '',
                'preparation'     => $i['preparation'] ?? '',
                'optional'        => !empty($i['optional']) ? 'true' : 'false',
                'ingredient_link' => '',
            ], $r['ingredients'] ?? []),
            'instructions' => array_map(fn($s) => [
                'step_title'  => $s['step_title'] ?? '',
                'instruction' => $s['instruction'] ?? '',
                'tip'         => $s['tip'] ?? '',
                'step_image'  => '',
            ], $r['instructions'] ?? []),
            'tips'    => $r['tips'] ?? '',
            'history' => $r['history'] ?? '',
            'tags'    => self::listToString($r['tags'] ?? []),
            'date'    => date('Y-m-d'),
        ], fn($v) => $v !== '' && $v !== null && $v !== []);
    }

    private static function listToString($value): string
    {
        if (is_array($value)) {
            return implode(', ', array_filter(array_map('trim', $value)));
        }
        return (string) $value;
    }

    private static function cleanEnum($value, array $allowed): string
    {
        $v = (string) $value;
        return in_array($v, $allowed, true) ? $v : '';
    }

    private static function cleanEnumList($value, array $allowed): string
    {
        if (!is_array($value)) {
            return '';
        }
        $clean = array_values(array_filter($value, fn($v) => in_array($v, $allowed, true)));
        return implode(', ', $clean);
    }

    private static function recipeSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title'             => ['type' => 'string'],
                'description'       => ['type' => 'string'],
                'category'          => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => self::ALLOWED_CATEGORIES]],
                'subcategory'       => ['type' => 'array', 'items' => ['type' => 'string']],
                'region'            => ['type' => ['string', 'null'], 'enum' => array_merge(self::ALLOWED_REGIONS, [null])],
                'prep_time_minutes' => ['type' => ['integer', 'null']],
                'cook_time_minutes' => ['type' => ['integer', 'null']],
                'total_time_minutes' => ['type' => ['integer', 'null']],
                'servings'          => ['type' => ['integer', 'null']],
                'difficulty'        => ['type' => ['string', 'null'], 'enum' => array_merge(self::ALLOWED_DIFFICULTY, [null])],
                'ingredients'       => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'ingredient'  => ['type' => 'string'],
                            'quantity'    => ['type' => 'string'],
                            'unit'        => ['type' => 'string', 'enum' => self::ALLOWED_UNITS],
                            'preparation' => ['type' => 'string'],
                            'optional'    => ['type' => 'boolean'],
                        ],
                        'required' => ['ingredient', 'quantity', 'unit', 'preparation', 'optional'],
                        'additionalProperties' => false,
                    ],
                ],
                'instructions' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'step_title'  => ['type' => 'string'],
                            'instruction' => ['type' => 'string'],
                            'tip'         => ['type' => 'string'],
                        ],
                        'required' => ['step_title', 'instruction', 'tip'],
                        'additionalProperties' => false,
                    ],
                ],
                'tips'    => ['type' => 'string'],
                'history' => ['type' => 'string'],
                'tags'    => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
            'required' => [
                'title', 'description', 'category', 'subcategory', 'region',
                'prep_time_minutes', 'cook_time_minutes', 'total_time_minutes',
                'servings', 'difficulty', 'ingredients', 'instructions',
                'tips', 'history', 'tags',
            ],
            'additionalProperties' => false,
        ];
    }
}

App::plugin('avendaurora/recipe-importer', []);
