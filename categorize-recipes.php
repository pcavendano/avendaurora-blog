<?php
/**
 * Recipe Categorization Script v2
 * Uses tags, titles, and descriptions for accurate categorization
 */

$contentPath = __DIR__ . '/content/1_recetas';

// Tag-based category mapping (most reliable)
$tagToCategory = [
    // Sopas y Caldos
    'soup-recipes' => 'sopas-caldos',
    'stew-recipes' => 'sopas-caldos',

    // Postres
    'dessert-recipes' => 'postres',
    'cookie-recipes' => 'postres',
    'cake-recipes' => 'postres',
    'pie-recipes' => 'postres',
    'ice-cream-recipes' => 'postres',

    // Mariscos
    'seafood-recipes' => 'mariscos',
    'seafood-main-recipes' => 'mariscos',
    'fish-recipes' => 'mariscos',
    'shrimp-recipes' => 'mariscos',

    // Antojitos
    'snack-appetizer-recipes' => 'antojitos',
    'taco-recipes' => 'antojitos',
    'dip-recipes' => 'antojitos',
    'appetizer-recipes' => 'antojitos',
    'finger-food-recipes' => 'antojitos',
    'nacho-recipes' => 'antojitos',
    'quesadilla-recipes' => 'antojitos',
    'empanada-recipes' => 'antojitos',

    // Salsas
    'condiment-sauce-recipes' => 'salsas',
    'sauce-recipes' => 'salsas',
    'salsa-recipes' => 'salsas',
    'dressing-recipes' => 'salsas',
    'marinade-recipes' => 'salsas',

    // Desayunos
    'breakfast-recipes' => 'desayunos',
    'brunch-recipes' => 'desayunos',
    'egg-recipes' => 'desayunos',
    'pancake-recipes' => 'desayunos',

    // Bebidas
    'drink-recipes' => 'bebidas',
    'cocktail-recipes' => 'bebidas',
    'smoothie-recipes' => 'bebidas',
    'beverage-recipes' => 'bebidas',

    // Vegetarianos
    'vegetarian-recipes' => 'vegetarianos',
    'vegan-recipes' => 'vegetarianos',
    'vegetable-recipes' => 'vegetarianos',
    'bean-recipes' => 'vegetarianos',
    'tofu-recipes' => 'vegetarianos',

    // Platos Fuertes (main dishes)
    'chicken-main-recipes' => 'platos-fuertes',
    'beef-main-recipes' => 'platos-fuertes',
    'pork-main-recipes' => 'platos-fuertes',
    'lamb-main-recipes' => 'platos-fuertes',
    'meat-recipes' => 'platos-fuertes',
    'poultry-recipes' => 'platos-fuertes',
    'rice-grains-main-recipes' => 'platos-fuertes',
    'pasta-main-recipes' => 'platos-fuertes',
    'casserole-recipes' => 'platos-fuertes',
    'sandwich-recipes' => 'platos-fuertes',
    'burger-recipes' => 'platos-fuertes',
];

// Title keywords for primary category (highest priority)
$titleKeywords = [
    'sopas-caldos' => [
        'soup', 'sopa', 'caldo', 'broth', 'stew', 'chowder', 'bisque',
        'pozole', 'posole', 'menudo', 'pho', 'ramen', 'gumbo', 'chili con'
    ],
    'postres' => [
        'cake', 'pie', 'tart', 'cookie', 'cookies', 'brownie', 'brownies',
        'flan', 'churro', 'churros', 'ice cream', 'pudding', 'custard',
        'cheesecake', 'cupcake', 'muffin', 'donut', 'sweet', 'candy',
        'chocolate', 'caramel', 'tres leches', 'alfajor', 'dessert'
    ],
    'bebidas' => [
        'margarita', 'cocktail', 'smoothie', 'drink', 'agua fresca',
        'horchata', 'lemonade', 'sangria', 'mojito', 'punch', 'tea',
        'coffee', 'milkshake', 'juice', 'licuado', 'champurrado', 'atole'
    ],
    'salsas' => [
        'salsa', 'sauce', 'guacamole', 'pico de gallo', 'chimichurri',
        'dressing', 'vinaigrette', 'aioli', 'mayo', 'chutney', 'relish',
        'marinade', 'glaze', 'pesto', 'mojo', 'crema', 'adobo'
    ],
    'mariscos' => [
        'shrimp', 'fish', 'salmon', 'tuna', 'ceviche', 'lobster', 'crab',
        'clam', 'mussel', 'oyster', 'scallop', 'octopus', 'squid', 'seafood',
        'camarón', 'pescado', 'tilapia', 'cod', 'mahi', 'prawn'
    ],
    'desayunos' => [
        'breakfast', 'pancake', 'pancakes', 'waffle', 'waffles', 'omelet',
        'omelette', 'frittata', 'french toast', 'huevos rancheros',
        'scrambled eggs', 'hash browns', 'brunch'
    ],
    'antojitos' => [
        'taco', 'tacos', 'quesadilla', 'quesadillas', 'nacho', 'nachos',
        'empanada', 'empanadas', 'tamale', 'tamales', 'enchilada', 'enchiladas',
        'burrito', 'burritos', 'tostada', 'tostadas', 'gordita', 'sope',
        'flauta', 'flautas', 'chilaquiles', 'arepa', 'pupusa', 'dip',
        'wings', 'slider', 'appetizer', 'snack'
    ],
    'vegetarianos' => [
        'vegetarian', 'vegan', 'veggie', 'tofu', 'tempeh',
        'cauliflower', 'mushroom', 'eggplant', 'zucchini', 'brussels sprouts',
        'kale', 'quinoa', 'lentil', 'chickpea', 'black bean'
    ],
    'platos-fuertes' => [
        'chicken', 'pollo', 'beef', 'steak', 'pork', 'lamb', 'turkey',
        'duck', 'roast', 'roasted', 'grilled', 'braised', 'fried',
        'baked', 'pulled', 'bbq', 'barbecue', 'carnitas', 'birria',
        'mole', 'curry', 'stir-fry', 'casserole', 'lasagna', 'meatball',
        'burger', 'sandwich', 'thigh', 'thighs', 'breast', 'ribs', 'chop'
    ]
];

// Category priority (for resolving conflicts)
$categoryPriority = [
    'sopas-caldos' => 10,    // Soups are very specific
    'postres' => 10,         // Desserts are very specific
    'bebidas' => 10,         // Drinks are very specific
    'mariscos' => 8,         // Seafood is specific
    'desayunos' => 8,        // Breakfast is specific
    'salsas' => 7,           // Sauces are specific
    'antojitos' => 6,        // Appetizers
    'vegetarianos' => 4,     // Can overlap with others
    'platos-fuertes' => 3    // Default for main dishes
];

// Exclusion rules - if recipe matches these, exclude from category
$exclusions = [
    'mariscos' => ['chicken', 'pollo', 'beef', 'pork', 'lamb', 'turkey', 'duck'],
    'vegetarianos' => ['chicken', 'pollo', 'beef', 'pork', 'fish', 'shrimp', 'bacon', 'sausage', 'lamb', 'meat', 'turkey', 'duck', 'seafood', 'crab', 'lobster'],
    'bebidas' => ['soup', 'stew', 'roast', 'baked', 'fried', 'grilled', 'chicken', 'beef', 'pork'],
    'postres' => ['chicken', 'beef', 'pork', 'fish', 'soup', 'taco', 'salsa', 'shrimp']
];

function matchWord($text, $keyword) {
    $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';
    return preg_match($pattern, $text);
}

function categorizeRecipe($title, $description, $tags) {
    global $tagToCategory, $titleKeywords, $categoryPriority, $exclusions;

    $scores = [];
    $titleLower = strtolower($title);
    $text = strtolower($title . ' ' . $description);
    $tagsLower = strtolower($tags);
    $tagList = array_map('trim', explode(',', $tagsLower));

    // STEP 1: Check tags (most reliable)
    foreach ($tagList as $tag) {
        $tag = trim($tag);
        if (isset($tagToCategory[$tag])) {
            $cat = $tagToCategory[$tag];
            $scores[$cat] = ($scores[$cat] ?? 0) + 15;
        }
    }

    // STEP 2: Check title keywords (very reliable)
    foreach ($titleKeywords as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (matchWord($titleLower, $keyword)) {
                $priority = $categoryPriority[$category] ?? 5;
                $scores[$category] = ($scores[$category] ?? 0) + (10 + $priority);
            }
        }
    }

    // STEP 3: Check description keywords (less reliable, lower score)
    foreach ($titleKeywords as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (!matchWord($titleLower, $keyword) && matchWord($text, $keyword)) {
                $scores[$category] = ($scores[$category] ?? 0) + 2;
            }
        }
    }

    // STEP 4: Apply exclusions
    foreach ($exclusions as $category => $excludeWords) {
        if (isset($scores[$category])) {
            foreach ($excludeWords as $word) {
                if (matchWord($titleLower, $word)) {
                    $scores[$category] -= 20; // Strong penalty for title match
                } elseif (matchWord($text, $word)) {
                    $scores[$category] -= 5;
                }
            }
        }
    }

    // STEP 5: Special rules
    // Salad rule: if title contains "salad", it's either salsas or vegetarianos
    if (matchWord($titleLower, 'salad') || matchWord($titleLower, 'ensalada')) {
        if (!isset($scores['salsas']) || $scores['salsas'] < 10) {
            $scores['salsas'] = ($scores['salsas'] ?? 0) + 12;
        }
    }

    // Stock/broth rule: goes with sopas-caldos
    if (matchWord($titleLower, 'stock') || matchWord($titleLower, 'broth')) {
        $scores['sopas-caldos'] = ($scores['sopas-caldos'] ?? 0) + 15;
    }

    // Sort by score
    arsort($scores);

    // Select categories
    $categories = [];
    $primaryScore = 0;

    foreach ($scores as $cat => $score) {
        if ($score > 0) {
            if (empty($categories)) {
                $categories[] = $cat;
                $primaryScore = $score;
            } elseif (count($categories) < 2 && $score >= 10 && $score >= $primaryScore * 0.6) {
                // Second category needs good score and at least 60% of primary
                $categories[] = $cat;
            }
        }
    }

    // Default fallback
    if (empty($categories)) {
        // Check if it's clearly a main dish
        if (preg_match('/chicken|beef|pork|lamb|turkey/i', $titleLower)) {
            $categories[] = 'platos-fuertes';
        } else {
            $categories[] = 'platos-fuertes';
        }
    }

    return $categories;
}

function parseKirbyFile($content) {
    $fields = [];
    $parts = preg_split('/\n----\n/', $content);

    foreach ($parts as $part) {
        $part = trim($part);
        if (empty($part)) continue;

        $colonPos = strpos($part, ':');
        if ($colonPos !== false) {
            $key = trim(substr($part, 0, $colonPos));
            $value = trim(substr($part, $colonPos + 1));
            $fields[$key] = $value;
        }
    }

    return $fields;
}

// Process all recipes
$dirs = glob($contentPath . '/*', GLOB_ONLYDIR);
$updated = 0;
$total = count($dirs);

echo "Processing $total recipes...\n\n";

$sampleOutput = [];

foreach ($dirs as $dir) {
    $recipeName = basename($dir);
    $filePath = $dir . '/recipe.es.txt';

    if (!file_exists($filePath)) {
        continue;
    }

    $content = file_get_contents($filePath);
    $fields = parseKirbyFile($content);

    $title = $fields['Title'] ?? '';
    $description = $fields['Description'] ?? '';
    $tags = $fields['Tags'] ?? '';

    $newCategories = categorizeRecipe($title, $description, $tags);
    $oldCategory = $fields['Category'] ?? '';
    $newCategoryStr = implode(', ', $newCategories);

    // Update the file
    $newContent = preg_replace(
        '/^Category:.*$/m',
        'Category: ' . $newCategoryStr,
        $content
    );

    file_put_contents($filePath, $newContent);

    if ($oldCategory !== $newCategoryStr) {
        // Show first 30 changes as samples
        if (count($sampleOutput) < 30) {
            $sampleOutput[] = "[$title]\n  Old: $oldCategory\n  New: $newCategoryStr";
        }
        $updated++;
    }
}

// Show sample changes
echo "Sample changes:\n";
echo implode("\n\n", $sampleOutput);
echo "\n\n---\nDone! Updated $updated of $total recipes.\n";

// Show new distribution
echo "\nNew category distribution:\n";
$categoryCount = [];
$recipesWithoutCategory = 0;

foreach ($dirs as $dir) {
    $filePath = $dir . '/recipe.es.txt';
    if (!file_exists($filePath)) continue;

    $content = file_get_contents($filePath);
    if (preg_match('/^Category:\s*(.*)$/m', $content, $matches)) {
        $catStr = trim($matches[1]);
        if (empty($catStr)) {
            $recipesWithoutCategory++;
        } else {
            $cats = array_map('trim', explode(',', $catStr));
            foreach ($cats as $cat) {
                if (!empty($cat)) {
                    $categoryCount[$cat] = ($categoryCount[$cat] ?? 0) + 1;
                }
            }
        }
    }
}

arsort($categoryCount);
foreach ($categoryCount as $cat => $count) {
    $pct = round($count / $total * 100, 1);
    echo "  $cat: $count ($pct%)\n";
}

if ($recipesWithoutCategory > 0) {
    echo "\n  WARNING: $recipesWithoutCategory recipes without category!\n";
}

// Verify all recipes have at least one category
echo "\nVerifying all recipes have categories...\n";
$missing = [];
foreach ($dirs as $dir) {
    $filePath = $dir . '/recipe.es.txt';
    if (!file_exists($filePath)) continue;

    $content = file_get_contents($filePath);
    if (preg_match('/^Category:\s*$/m', $content)) {
        $missing[] = basename($dir);
    }
}

if (empty($missing)) {
    echo "All recipes have at least one category.\n";
} else {
    echo "Recipes missing categories: " . count($missing) . "\n";
    foreach (array_slice($missing, 0, 10) as $m) {
        echo "  - $m\n";
    }
}
