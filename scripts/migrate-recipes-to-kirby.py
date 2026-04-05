#!/usr/bin/env python3
"""
Migrate extracted recipes from Markdown to Kirby CMS content format.
Focuses on Mexican cuisine recipes for Avenda Aurora.
"""

import os
import re
import sys
from pathlib import Path
from datetime import datetime

# Mexican cuisine keywords to identify relevant recipes
MEXICAN_KEYWORDS = [
    'taco', 'tacos', 'burrito', 'enchilada', 'quesadilla', 'tamale', 'tamales',
    'salsa', 'guacamole', 'chile', 'chili', 'chipotle', 'jalapeno', 'jalapeño',
    'carnitas', 'barbacoa', 'birria', 'pozole', 'menudo', 'mole', 'elote',
    'churro', 'flan', 'tres leches', 'horchata', 'margarita', 'michelada',
    'tortilla', 'tostada', 'nachos', 'mexican', 'mexico', 'baja', 'oaxaca',
    'yucatan', 'veracruz', 'poblano', 'ancho', 'pasilla', 'guajillo', 'habanero',
    'serrano', 'arbol', 'chorizo', 'carne asada', 'al pastor', 'cochinita',
    'pico de gallo', 'crema', 'queso', 'cotija', 'oaxaqueño', 'fresco',
    'refried', 'frijoles', 'arroz', 'cilantro', 'lime', 'avocado', 'aguacate',
    'sope', 'huarache', 'gordita', 'flautas', 'taquito', 'chilaquiles',
    'empanada', 'ceviche', 'aguachile', 'camarones', 'pescado', 'mariscos'
]

# Category mapping based on keywords
CATEGORY_MAP = {
    'antojitos': ['taco', 'tacos', 'quesadilla', 'nachos', 'tostada', 'sope',
                  'huarache', 'gordita', 'flautas', 'taquito', 'empanada'],
    'platos-fuertes': ['burrito', 'enchilada', 'carnitas', 'barbacoa', 'birria',
                       'carne asada', 'al pastor', 'cochinita', 'mole', 'tamale', 'tamales'],
    'sopas-caldos': ['pozole', 'menudo', 'caldo', 'sopa', 'soup'],
    'salsas': ['salsa', 'guacamole', 'pico de gallo', 'sauce', 'chipotle'],
    'mariscos': ['ceviche', 'aguachile', 'camarones', 'pescado', 'fish', 'shrimp', 'seafood', 'mariscos'],
    'desayunos': ['chilaquiles', 'huevos', 'breakfast', 'egg'],
    'postres': ['churro', 'flan', 'tres leches', 'dessert', 'cookie', 'cake', 'pie'],
    'bebidas': ['horchata', 'margarita', 'michelada', 'drink', 'cocktail', 'smoothie'],
    'vegetarianos': ['vegetarian', 'vegan', 'bean', 'frijoles']
}


def slugify(text):
    """Convert text to URL-friendly slug."""
    text = text.lower()
    text = re.sub(r'[^\w\s-]', '', text)
    text = re.sub(r'[-\s]+', '-', text)
    return text.strip('-')


def is_mexican_recipe(title, content):
    """Check if recipe is Mexican cuisine related."""
    combined = (title + ' ' + content).lower()
    for keyword in MEXICAN_KEYWORDS:
        if keyword in combined:
            return True
    return False


def determine_category(title, content):
    """Determine recipe category based on keywords."""
    combined = (title + ' ' + content).lower()

    for category, keywords in CATEGORY_MAP.items():
        for keyword in keywords:
            if keyword in combined:
                return category

    return 'platos-fuertes'  # Default category


def parse_recipe_md(filepath):
    """Parse a recipe markdown file."""
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    recipe = {
        'title': '',
        'description': '',
        'author': '',
        'image_url': '',
        'tags': [],
        'ingredients': [],
        'instructions': [],
        'category': 'platos-fuertes'
    }

    lines = content.split('\n')
    current_section = None

    for line in lines:
        line = line.strip()

        # Title
        if line.startswith('# '):
            recipe['title'] = line[2:].strip()

        # Description (blockquote)
        elif line.startswith('> '):
            recipe['description'] = line[2:].strip()

        # Author
        elif line.startswith('**Author:**'):
            recipe['author'] = line.replace('**Author:**', '').strip()

        # Image URL
        elif line.startswith('**Original Image:**'):
            recipe['image_url'] = line.replace('**Original Image:**', '').strip()

        # Tags
        elif line.startswith('**Tags:**'):
            tags_str = line.replace('**Tags:**', '').strip()
            recipe['tags'] = [t.strip() for t in tags_str.split(',')]

        # Section headers
        elif line == '## Ingredients':
            current_section = 'ingredients'
        elif line == '## Instructions':
            current_section = 'instructions'
        elif line.startswith('## '):
            current_section = None

        # Ingredients
        elif current_section == 'ingredients' and line.startswith('- '):
            ingredient = line[2:].strip()
            if ingredient:
                recipe['ingredients'].append(ingredient)

        # Instructions
        elif current_section == 'instructions' and re.match(r'^\d+\.', line):
            instruction = re.sub(r'^\d+\.\s*', '', line).strip()
            if instruction:
                recipe['instructions'].append(instruction)

    # Determine category
    recipe['category'] = determine_category(recipe['title'], content)

    return recipe


def parse_ingredient(ingredient_text):
    """Parse ingredient text into structured format."""
    # Common patterns: "1 cup flour", "2 tablespoons oil", "1/2 teaspoon salt"

    # Try to extract quantity and unit
    quantity = ''
    unit = ''
    ingredient = ingredient_text
    preparation = ''

    # Check for preparation instructions in parentheses at the end
    prep_match = re.search(r'\(([^)]+)\)\s*$', ingredient_text)
    if prep_match:
        preparation = prep_match.group(1)
        ingredient_text = ingredient_text[:prep_match.start()].strip()

    # Match quantity (including fractions)
    qty_match = re.match(r'^([\d\s/½¼¾⅓⅔⅛]+)', ingredient_text)
    if qty_match:
        quantity = qty_match.group(1).strip()
        ingredient_text = ingredient_text[qty_match.end():].strip()

    # Common units
    units = ['cup', 'cups', 'tablespoon', 'tablespoons', 'teaspoon', 'teaspoons',
             'pound', 'pounds', 'ounce', 'ounces', 'gram', 'grams', 'kg', 'lb',
             'ml', 'liter', 'liters', 'quart', 'quarts', 'pint', 'pints',
             'clove', 'cloves', 'piece', 'pieces', 'slice', 'slices',
             'small', 'medium', 'large', 'inch', 'inches']

    for u in units:
        if ingredient_text.lower().startswith(u + ' ') or ingredient_text.lower().startswith(u + 's '):
            unit_match = re.match(r'^(\w+)\s+', ingredient_text, re.IGNORECASE)
            if unit_match:
                unit = unit_match.group(1)
                ingredient = ingredient_text[unit_match.end():].strip()
                break
    else:
        ingredient = ingredient_text

    # Clean up ingredient name
    ingredient = re.sub(r'\s*\([^)]*\)\s*', ' ', ingredient).strip()
    ingredient = re.sub(r'\s+', ' ', ingredient)

    return {
        'quantity': quantity,
        'unit': unit,
        'ingredient': ingredient,
        'preparation': preparation
    }


def create_kirby_recipe(recipe, output_dir):
    """Create Kirby CMS content files for a recipe."""
    slug = slugify(recipe['title'])
    recipe_dir = output_dir / slug
    recipe_dir.mkdir(parents=True, exist_ok=True)

    # Parse ingredients into structured format
    ingredients_yaml = []
    for ing in recipe['ingredients']:
        parsed = parse_ingredient(ing)
        ingredients_yaml.append(f"""-
  quantity: "{parsed['quantity']}"
  unit: {parsed['unit']}
  ingredient: {parsed['ingredient']}
  preparation: {parsed['preparation']}
  optional: false
  ingredient_link: """)

    # Create instructions YAML
    instructions_yaml = []
    for i, inst in enumerate(recipe['instructions']):
        # Limit notes/tips (items 10+) to separate section
        if i < 9:  # Only include first 9 as main instructions
            instructions_yaml.append(f"""-
  step_title: ""
  instruction: {inst}
  tip: ""
  step_image: """)

    # Collect tips from remaining instructions
    tips = []
    for inst in recipe['instructions'][9:]:
        if inst and len(inst) > 10:
            tips.append(f"- {inst}")

    # Create the content file
    content = f"""Title: {recipe['title']}

----

Category: {recipe['category']}

----

Description: {recipe['description']}

----

Cover:

----

Prep_time:

----

Cook_time:

----

Total_time:

----

Servings: 4

----

Difficulty: medium

----

Ingredients:
{chr(10).join(ingredients_yaml)}

----

Instructions:
{chr(10).join(instructions_yaml)}

----

Tips:

{chr(10).join(tips) if tips else ''}

----

History:

----

Enable_kits: false

----

Store_kits:

----

Tags: {', '.join(recipe['tags'][:5]) if recipe['tags'] else recipe['category']}

----

Seo_title: {recipe['title']} | Avenda Aurora

----

Seo_description: {recipe['description'][:160] if recipe['description'] else ''}

----

Original_author: {recipe['author']}

----

Original_image: {recipe['image_url']}
"""

    # Write the content file
    content_file = recipe_dir / 'recipe.txt'
    with open(content_file, 'w', encoding='utf-8') as f:
        f.write(content)

    return slug


def main():
    # Paths
    script_dir = Path(__file__).parent
    project_dir = script_dir.parent
    recipes_dir = project_dir / 'recipes'
    output_dir = project_dir / 'content' / '1_recetas'

    if not recipes_dir.exists():
        print(f"[ERROR] Recipes directory not found: {recipes_dir}")
        sys.exit(1)

    # Get all markdown files
    md_files = list(recipes_dir.glob('*.md'))
    print(f"[INFO] Found {len(md_files)} recipe files")

    # Filter for Mexican recipes
    mexican_recipes = []
    other_recipes = []

    for md_file in md_files:
        try:
            with open(md_file, 'r', encoding='utf-8') as f:
                content = f.read()

            # Get title from first line
            title_match = re.search(r'^# (.+)$', content, re.MULTILINE)
            title = title_match.group(1) if title_match else md_file.stem

            if is_mexican_recipe(title, content):
                mexican_recipes.append(md_file)
            else:
                other_recipes.append(md_file)
        except Exception as e:
            print(f"[WARN] Could not read {md_file.name}: {e}")

    print(f"[INFO] Found {len(mexican_recipes)} Mexican cuisine recipes")
    print(f"[INFO] Found {len(other_recipes)} other recipes")

    # Ask user what to import
    if '--all' in sys.argv:
        recipes_to_import = md_files
        print("[INFO] Importing ALL recipes")
    elif '--mexican-only' in sys.argv or len(sys.argv) == 1:
        recipes_to_import = mexican_recipes
        print("[INFO] Importing Mexican recipes only")
    else:
        print("\nUsage:")
        print("  python migrate-recipes-to-kirby.py           # Mexican recipes only")
        print("  python migrate-recipes-to-kirby.py --all     # All recipes")
        print("  python migrate-recipes-to-kirby.py --list    # List Mexican recipes")
        sys.exit(0)

    if '--list' in sys.argv:
        print("\nMexican recipes found:")
        for r in mexican_recipes:
            print(f"  - {r.stem}")
        sys.exit(0)

    # Create output directory
    output_dir.mkdir(parents=True, exist_ok=True)

    # Migrate recipes
    migrated = 0
    errors = 0

    for md_file in recipes_to_import:
        try:
            recipe = parse_recipe_md(md_file)
            if recipe['title']:
                slug = create_kirby_recipe(recipe, output_dir)
                print(f"[OK] {recipe['title']} -> {slug}/")
                migrated += 1
            else:
                print(f"[SKIP] No title found in {md_file.name}")
        except Exception as e:
            print(f"[ERROR] {md_file.name}: {e}")
            errors += 1

    print(f"\n[DONE] Migrated {migrated} recipes, {errors} errors")
    print(f"[INFO] Recipes saved to: {output_dir}")


if __name__ == '__main__':
    main()
