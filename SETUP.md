# Avenda Aurora - Setup & Workflow Instructions

This document contains all the steps to set up and run the project in the correct order.

---

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [Initial Setup](#2-initial-setup)
3. [Data Preparation (HTML to Recipes)](#3-data-preparation-html-to-recipes)
4. [Kirby CMS Installation](#4-kirby-cms-installation)
5. [Running the Development Server](#5-running-the-development-server)
6. [Content Migration](#6-content-migration)
7. [Production Deployment](#7-production-deployment)

---

## 1. Prerequisites

### Required Software

| Software | Version | Purpose | Installation |
|----------|---------|---------|--------------|
| PHP | 8.1+ | Kirby CMS runtime | [php.net](https://php.net) |
| Composer | 2.x | PHP dependency manager | [getcomposer.org](https://getcomposer.org) |
| Python | 3.8+ | Recipe extraction scripts | [python.org](https://python.org) |
| Node.js | 18+ | Frontend build tools (optional) | [nodejs.org](https://nodejs.org) |
| Git | 2.x | Version control | [git-scm.com](https://git-scm.com) |

### Check Your Versions

```bash
# Check PHP
php -v

# Check Composer
composer --version

# Check Python
python --version

# Check Node (optional)
node --version

# Check Git
git --version
```

---

## 2. Initial Setup

### Step 2.1: Clone/Navigate to Project

```bash
cd C:/Users/Cambridge/WebstormProjects/avendaurora-blog
```

### Step 2.2: Create Directory Structure

```bash
# Create required directories
mkdir -p content
mkdir -p content/recipes
mkdir -p content/ingredientes
mkdir -p content/tiendas
mkdir -p site/blueprints/pages
mkdir -p site/blueprints/users
mkdir -p site/templates
mkdir -p site/snippets
mkdir -p site/controllers
mkdir -p site/languages
mkdir -p site/plugins
mkdir -p assets/css
mkdir -p assets/js
mkdir -p assets/images
mkdir -p media
mkdir -p scripts
mkdir -p docs
```

---

## 3. Data Preparation (HTML to Recipes)

### Step 3.1: Preview What Will Be Extracted (Dry Run)

```bash
# See what recipes will be extracted without making changes
python scripts/extract-recipes.py --dry-run --keep-html
```

**Expected output:**
- Shows how many recipe files were found
- Lists what files would be created
- Shows what files would be deleted

### Step 3.2: Extract Recipes (Keep Original HTML)

```bash
# Extract recipes but keep HTML files for reference
python scripts/extract-recipes.py --keep-html
```

**Output:** Creates `recipes/*.md` files with extracted recipe data.

### Step 3.3: Extract Recipes (Delete Original HTML)

```bash
# Extract recipes AND delete original HTML files
python scripts/extract-recipes.py
```

**Warning:** This deletes the original HTML files after extraction.

### Step 3.4: Extract to JSON Format (Alternative)

```bash
# Extract as JSON instead of Markdown
python scripts/extract-recipes.py --output-format json --keep-html
```

### Script Options Reference

| Option | Description |
|--------|-------------|
| `--dry-run` | Preview without making changes |
| `--keep-html` | Don't delete original HTML files |
| `--output-format md` | Output as Markdown (default) |
| `--output-format json` | Output as JSON |
| `--directory /path` | Specify directory (default: current) |

---

## 4. Kirby CMS Installation

### Step 4.1: Install Kirby via Composer

```bash
# Navigate to project root
cd C:/Users/Cambridge/WebstormProjects/avendaurora-blog

# Install Kirby CMS
composer require getkirby/cms
```

### Step 4.2: Create index.php

```bash
# Create the main entry point
cat > index.php << 'EOF'
<?php

require __DIR__ . '/vendor/autoload.php';

$kirby = new Kirby([
    'roots' => [
        'index'    => __DIR__,
        'base'     => $base    = dirname(__DIR__),
        'content'  => __DIR__ . '/content',
        'site'     => __DIR__ . '/site',
        'storage'  => $storage = __DIR__ . '/storage',
        'accounts' => $storage . '/accounts',
        'cache'    => $storage . '/cache',
        'logs'     => $storage . '/logs',
        'sessions' => $storage . '/sessions',
    ]
]);

echo $kirby->render();
EOF
```

### Step 4.3: Create Configuration File

```bash
# Create config file
cat > site/config/config.php << 'EOF'
<?php

return [
    'debug' => true,

    'languages' => true,
    'languages.detect' => true,

    'panel' => [
        'install' => true,
    ],

    'auth' => [
        'methods' => ['password'],
        'challenge' => [
            'timeout' => 10
        ]
    ]
];
EOF
```

### Step 4.4: Create Language Files

```bash
# Spanish (default)
cat > site/languages/es.php << 'EOF'
<?php
return [
    'code' => 'es',
    'default' => true,
    'name' => 'Español',
    'locale' => 'es_MX.utf-8',
    'url' => '/',
    'direction' => 'ltr',
    'translations' => [
        'recipes' => 'Recetas',
        'ingredients' => 'Ingredientes',
        'stores' => 'Tiendas',
    ]
];
EOF

# English
cat > site/languages/en.php << 'EOF'
<?php
return [
    'code' => 'en',
    'name' => 'English',
    'locale' => 'en_US.utf-8',
    'url' => '/en',
    'direction' => 'ltr',
    'translations' => [
        'recipes' => 'Recipes',
        'ingredients' => 'Ingredients',
        'stores' => 'Stores',
    ]
];
EOF

# French
cat > site/languages/fr.php << 'EOF'
<?php
return [
    'code' => 'fr',
    'name' => 'Français',
    'locale' => 'fr_FR.utf-8',
    'url' => '/fr',
    'direction' => 'ltr',
    'translations' => [
        'recipes' => 'Recettes',
        'ingredients' => 'Ingrédients',
        'stores' => 'Boutiques',
    ]
];
EOF
```

### Step 4.5: Create Storage Directories

```bash
mkdir -p storage/accounts
mkdir -p storage/cache
mkdir -p storage/logs
mkdir -p storage/sessions
```

---

## 5. Running the Development Server

### Option A: PHP Built-in Server (Recommended for Development)

```bash
# Start the server
php -S localhost:8000

# Or specify a different port
php -S localhost:3000
```

Then open: http://localhost:8000

### Option B: Using Router Script

```bash
# Create a router for the built-in server
cat > router.php << 'EOF'
<?php
if (file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    return false;
}
include __DIR__ . '/index.php';
EOF

# Run with router
php -S localhost:8000 router.php
```

### First Time Setup - Create Admin Account

1. Open: http://localhost:8000/panel
2. Follow the installation wizard
3. Create your admin account
4. Log in to the Panel

---

## 6. Content Migration

### Step 6.1: Convert Extracted Recipes to Kirby Format

After extracting recipes with the Python script, you need to convert them to Kirby's content format.

```bash
# Run the Kirby migration script (to be created)
python scripts/migrate-to-kirby.py
```

### Step 6.2: Manual Recipe Import

For each recipe in `recipes/*.md`:

1. Create a folder: `content/recipes/[recipe-slug]/`
2. Create the content file: `recipe.es.txt`
3. Copy images to the recipe folder

**Example structure:**
```
content/
└── recipes/
    └── tacos-de-carnitas/
        ├── recipe.es.txt
        ├── recipe.en.txt
        ├── recipe.fr.txt
        ├── hero.jpg
        └── step-1.jpg
```

### Step 6.3: Create Initial Content Files

```bash
# Create home page
mkdir -p content/home
cat > content/home/home.es.txt << 'EOF'
Title: Avenda Aurora
----
Subtitle: Cocina Mexicana Auténtica
----
Description: Descubre recetas auténticas de la cocina mexicana con ingredientes que puedes encontrar en tu tienda local.
EOF

# Create recipes listing page
mkdir -p content/recipes
cat > content/recipes/recipes.es.txt << 'EOF'
Title: Recetas
----
Description: Todas nuestras recetas de cocina mexicana
EOF
```

---

## 7. Production Deployment

### Step 7.1: Prepare for Production

```bash
# Update config for production
cat > site/config/config.php << 'EOF'
<?php

return [
    'debug' => false,

    'languages' => true,
    'languages.detect' => true,

    'panel' => [
        'install' => false,
    ],

    'cache' => [
        'pages' => [
            'active' => true
        ]
    ]
];
EOF
```

### Step 7.2: Install Dependencies for Production

```bash
composer install --no-dev --optimize-autoloader
```

### Step 7.3: Set File Permissions

```bash
# Set permissions (Linux/Mac)
chmod -R 755 .
chmod -R 775 content
chmod -R 775 media
chmod -R 775 storage
```

### Step 7.4: Upload to Server

Upload all files to your web server via FTP/SFTP or Git deployment.

**Required server configuration:**
- PHP 8.1+
- mod_rewrite enabled (Apache) or equivalent (Nginx)
- HTTPS enabled

---

## Quick Reference Commands

### Development Workflow

```bash
# 1. Start development server
php -S localhost:8000

# 2. Open in browser
# http://localhost:8000        (Site)
# http://localhost:8000/panel  (Admin)

# 3. Extract new recipes from HTML
python scripts/extract-recipes.py --keep-html

# 4. Clear cache (if needed)
rm -rf storage/cache/*
```

### Troubleshooting

```bash
# Check PHP errors
tail -f storage/logs/*.log

# Clear all caches
rm -rf storage/cache/*

# Reset sessions
rm -rf storage/sessions/*

# Check file permissions
ls -la content/
ls -la storage/
```

---

## File Structure Reference

```
avendaurora-blog/
├── content/                    # Content files (Kirby)
│   ├── home/
│   ├── recipes/
│   ├── ingredientes/
│   └── tiendas/
├── site/                       # Kirby site configuration
│   ├── blueprints/
│   ├── config/
│   ├── controllers/
│   ├── languages/
│   ├── plugins/
│   ├── snippets/
│   └── templates/
├── assets/                     # Frontend assets
│   ├── css/
│   ├── js/
│   └── images/
├── media/                      # Kirby media cache
├── storage/                    # Kirby storage
│   ├── accounts/
│   ├── cache/
│   ├── logs/
│   └── sessions/
├── scripts/                    # Utility scripts
│   └── extract-recipes.py
├── docs/                       # Documentation
│   ├── README.md
│   ├── recipe-template.md
│   └── recipe-example.md
├── vendor/                     # Composer dependencies
├── index.php                   # Entry point
├── composer.json
├── SETUP.md                    # This file
└── architecture-plan.md        # Project architecture
```

---

## Current Progress

Completed items:

1. [x] Configure blueprints in `site/blueprints/`
   - recipe.yml, ingredient.yml, store.yml
   - home.yml, recipes.yml, ingredients.yml, stores.yml, about.yml
   - site.yml (global settings)
   - users/admin.yml, users/member.yml
2. [x] Create templates in `site/templates/`
   - home.php, default.php, about.php
   - recipes.php, recipe.php
   - ingredients.php, ingredient.php
   - stores.php, store.php
3. [x] Add CSS/JS to `assets/`
   - assets/css/style.css (complete styling)
   - assets/js/app.js (interactivity)
4. [x] Create snippets
   - header.php, footer.php
   - recipe-card.php, ingredient-card.php
   - language-switcher.php
5. [x] Set up multilingual support (es, en, fr)
6. [x] Create sample content
   - Tacos de Carnitas recipe
   - Jalapeño and Chipotle ingredients (with relationship)
   - La Mexicana store

## Next Steps

Remaining tasks:

1. [ ] Run `composer install` to install Kirby CMS
2. [ ] Start PHP server (`php -S localhost:8000`)
3. [ ] Create admin account at `/panel`
4. [ ] Import more recipes using the extraction script
5. [ ] Add images to content
6. [ ] Configure store partnerships
7. [ ] Add e-commerce (Snipcart) - optional
8. [ ] Deploy to production

---

*Last updated: 2026-01-30*
