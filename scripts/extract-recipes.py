#!/usr/bin/env python3
"""
Recipe Extractor for Serious Eats HTML files
Extracts relevant recipe information and renames files to recipe names.

Usage:
    python scripts/extract-recipes.py [--dry-run] [--output-format md|json]

Options:
    --dry-run       Show what would be done without making changes
    --output-format Output format: 'md' for Markdown (default), 'json' for JSON
    --keep-html     Keep original HTML files (default: delete them)
"""

import os
import re
import sys
import json
import glob
import argparse
from html.parser import HTMLParser
from pathlib import Path


class RecipeExtractor(HTMLParser):
    """Extract recipe data from Serious Eats HTML."""

    def __init__(self):
        super().__init__()
        self.recipe = {
            'title': '',
            'description': '',
            'prep_time': '',
            'cook_time': '',
            'total_time': '',
            'servings': '',
            'ingredients': [],
            'instructions': [],
            'author': '',
            'tags': [],
            'image_url': '',
        }

        # Parser state
        self.current_tag = ''
        self.current_attrs = {}
        self.in_ingredients = False
        self.in_instructions = False
        self.in_ingredient_item = False
        self.in_instruction_item = False
        self.current_ingredient = ''
        self.current_instruction = ''
        self.capture_text = False
        self.text_buffer = ''

    def handle_starttag(self, tag, attrs):
        self.current_tag = tag
        self.current_attrs = dict(attrs)
        attrs_dict = dict(attrs)

        # Get meta tags
        if tag == 'meta':
            name = attrs_dict.get('name', '')
            prop = attrs_dict.get('property', '')
            content = attrs_dict.get('content', '')
            itemprop = attrs_dict.get('itemprop', '')

            if name == 'description' or itemprop == 'description':
                self.recipe['description'] = content
            elif name == 'sailthru.author':
                self.recipe['author'] = content
            elif name == 'sailthru.tags':
                self.recipe['tags'] = [t.strip() for t in content.split(',')]
            elif prop == 'og:image' and not self.recipe['image_url']:
                self.recipe['image_url'] = content

        # Get title
        if tag == 'title':
            self.capture_text = True
            self.text_buffer = ''

        # Check for ingredient section
        class_attr = attrs_dict.get('class', '')
        id_attr = attrs_dict.get('id', '')

        if 'section--ingredients' in class_attr or 'section--ingredients' in id_attr:
            self.in_ingredients = True
        elif 'section--instructions' in class_attr or 'section--instructions' in id_attr:
            self.in_instructions = True

        # Ingredient items
        if self.in_ingredients and 'structured-ingredients__list-item' in class_attr:
            self.in_ingredient_item = True
            self.current_ingredient = ''

        # Instruction items (LI in ordered list)
        if self.in_instructions and tag == 'li':
            self.in_instruction_item = True
            self.current_instruction = ''

        # Recipe meta (times, servings)
        if 'project-meta__time' in class_attr or 'recipe-meta__time' in class_attr:
            self.capture_text = True
            self.text_buffer = ''

        if 'recipe-serving' in class_attr:
            self.capture_text = True
            self.text_buffer = ''

        # Data attributes for ingredient parts
        if attrs_dict.get('data-ingredient-quantity'):
            self.capture_text = True
            self.text_buffer = ''
        if attrs_dict.get('data-ingredient-unit'):
            self.capture_text = True
            self.text_buffer = ''
        if attrs_dict.get('data-ingredient-name'):
            self.capture_text = True
            self.text_buffer = ''

    def handle_endtag(self, tag):
        if tag == 'title' and self.capture_text:
            self.recipe['title'] = self.text_buffer.strip()
            # Clean up title (remove " Recipe" suffix and site name)
            title = self.recipe['title']
            title = re.sub(r'\s*\|\s*Serious Eats.*$', '', title)
            title = re.sub(r'\s*Recipe\s*$', '', title)
            self.recipe['title'] = title.strip()
            self.capture_text = False

        if tag == 'section':
            self.in_ingredients = False
            self.in_instructions = False

        if tag == 'li':
            if self.in_ingredient_item and self.current_ingredient.strip():
                # Clean up ingredient text
                ingredient = ' '.join(self.current_ingredient.split())
                if ingredient and len(ingredient) > 2:
                    self.recipe['ingredients'].append(ingredient)
                self.in_ingredient_item = False
                self.current_ingredient = ''

            if self.in_instruction_item and self.current_instruction.strip():
                # Clean up instruction text
                instruction = ' '.join(self.current_instruction.split())
                if instruction and len(instruction) > 10:
                    self.recipe['instructions'].append(instruction)
                self.in_instruction_item = False
                self.current_instruction = ''

        if tag == 'p' and self.in_ingredient_item:
            # End of ingredient paragraph
            pass

    def handle_data(self, data):
        text = data.strip()

        if self.capture_text:
            self.text_buffer += data

        if self.in_ingredient_item:
            self.current_ingredient += ' ' + data

        if self.in_instruction_item:
            self.current_instruction += ' ' + data

        # Capture time values
        if text:
            if 'Prep Time' in text or 'prep time' in text.lower():
                self.capture_text = True
                self.text_buffer = ''
            elif 'Cook Time' in text or 'cook time' in text.lower():
                self.capture_text = True
                self.text_buffer = ''
            elif 'Total Time' in text or 'total time' in text.lower():
                self.capture_text = True
                self.text_buffer = ''


def extract_recipe_simple(html_content):
    """Simple regex-based extraction as fallback."""
    recipe = {
        'title': '',
        'description': '',
        'prep_time': '',
        'cook_time': '',
        'total_time': '',
        'servings': '',
        'ingredients': [],
        'instructions': [],
        'author': '',
        'tags': [],
        'image_url': '',
    }

    # Title
    title_match = re.search(r'<title>([^<]+)</title>', html_content)
    if title_match:
        title = title_match.group(1)
        title = re.sub(r'\s*\|\s*Serious Eats.*$', '', title)
        title = re.sub(r'\s*Recipe\s*$', '', title)
        recipe['title'] = title.strip()

    # Description
    desc_match = re.search(r'<meta\s+name="description"\s+content="([^"]+)"', html_content)
    if desc_match:
        recipe['description'] = desc_match.group(1)

    # Author
    author_match = re.search(r'sailthru\.author"\s+content="([^"]+)"', html_content)
    if author_match:
        recipe['author'] = author_match.group(1)

    # Image
    img_match = re.search(r'og:image"\s+content="([^"]+)"', html_content)
    if img_match:
        recipe['image_url'] = img_match.group(1)

    # Tags
    tags_match = re.search(r'sailthru\.tags"\s+content="([^"]+)"', html_content)
    if tags_match:
        recipe['tags'] = [t.strip() for t in tags_match.group(1).split(',')]

    # Ingredients - extract from structured ingredients
    ingredients_pattern = r'<li class="structured-ingredients__list-item">\s*<p>([^<]+(?:<[^>]+>[^<]*</[^>]+>)*[^<]*)</p>'
    ingredient_matches = re.findall(r'<li class="structured-ingredients__list-item"[^>]*>\s*<p>(.*?)</p>', html_content, re.DOTALL)
    for ing in ingredient_matches:
        # Remove HTML tags
        clean_ing = re.sub(r'<[^>]+>', ' ', ing)
        clean_ing = ' '.join(clean_ing.split())
        if clean_ing and len(clean_ing) > 2:
            recipe['ingredients'].append(clean_ing)

    # Instructions - extract from mntl-sc-block-html paragraphs inside instruction section
    # Find the instructions section first
    instr_section = re.search(r'section--instructions.*?</section>', html_content, re.DOTALL)
    if instr_section:
        instr_html = instr_section.group(0)
        # Extract paragraphs
        instr_matches = re.findall(r'<p[^>]*class="[^"]*mntl-sc-block-html[^"]*"[^>]*>([^<]+(?:<[^>]+>[^<]*</[^>]+>)*[^<]*)</p>', instr_html, re.DOTALL)
        for instr in instr_matches:
            clean_instr = re.sub(r'<[^>]+>', ' ', instr)
            clean_instr = ' '.join(clean_instr.split())
            if clean_instr and len(clean_instr) > 15:
                recipe['instructions'].append(clean_instr)

    # Times and servings - look for specific patterns
    prep_match = re.search(r'Prep Time:?\s*</dt>\s*<dd[^>]*>([^<]+)', html_content)
    if prep_match:
        recipe['prep_time'] = prep_match.group(1).strip()

    cook_match = re.search(r'Cook Time:?\s*</dt>\s*<dd[^>]*>([^<]+)', html_content)
    if cook_match:
        recipe['cook_time'] = cook_match.group(1).strip()

    total_match = re.search(r'Total Time:?\s*</dt>\s*<dd[^>]*>([^<]+)', html_content)
    if total_match:
        recipe['total_time'] = total_match.group(1).strip()

    # Alternative time format
    if not recipe['prep_time']:
        prep_match = re.search(r'(\d+)\s*mins?\s*prep', html_content, re.IGNORECASE)
        if prep_match:
            recipe['prep_time'] = f"{prep_match.group(1)} mins"

    servings_match = re.search(r'Servings:?\s*</dt>\s*<dd[^>]*>([^<]+)', html_content)
    if servings_match:
        recipe['servings'] = servings_match.group(1).strip()
    else:
        servings_match = re.search(r'recipe-serving[^>]*>([^<]*\d+[^<]*)<', html_content)
        if servings_match:
            recipe['servings'] = servings_match.group(1).strip()

    return recipe


def is_recipe_file(html_content):
    """Check if the HTML file contains a recipe."""
    # Look for recipe indicators
    indicators = [
        'structured-ingredients',
        'section--ingredients',
        'section--instructions',
        'recipeScTemplate',
        'recipe-decision-block',
        'data-ingredient-quantity',
    ]
    return any(indicator in html_content for indicator in indicators)


def slugify(text):
    """Convert text to URL-friendly slug."""
    text = text.lower()
    text = re.sub(r'[áàäâ]', 'a', text)
    text = re.sub(r'[éèëê]', 'e', text)
    text = re.sub(r'[íìïî]', 'i', text)
    text = re.sub(r'[óòöô]', 'o', text)
    text = re.sub(r'[úùüû]', 'u', text)
    text = re.sub(r'[ñ]', 'n', text)
    text = re.sub(r'[^a-z0-9]+', '-', text)
    text = re.sub(r'-+', '-', text)
    text = text.strip('-')
    return text


def recipe_to_markdown(recipe):
    """Convert recipe dict to Markdown format."""
    md = []

    md.append(f"# {recipe['title']}\n")

    if recipe['description']:
        md.append(f"> {recipe['description']}\n")

    if recipe['author']:
        md.append(f"**Author:** {recipe['author']}\n")

    if recipe['image_url']:
        md.append(f"**Original Image:** {recipe['image_url']}\n")

    # Recipe details
    md.append("\n## Recipe Details\n")
    details = []
    if recipe['prep_time']:
        details.append(f"- **Prep Time:** {recipe['prep_time']}")
    if recipe['cook_time']:
        details.append(f"- **Cook Time:** {recipe['cook_time']}")
    if recipe['total_time']:
        details.append(f"- **Total Time:** {recipe['total_time']}")
    if recipe['servings']:
        details.append(f"- **Servings:** {recipe['servings']}")
    if details:
        md.append('\n'.join(details) + '\n')

    # Tags
    if recipe['tags']:
        md.append(f"\n**Tags:** {', '.join(recipe['tags'][:10])}\n")

    # Ingredients
    if recipe['ingredients']:
        md.append("\n## Ingredients\n")
        for ing in recipe['ingredients']:
            md.append(f"- {ing}")
        md.append('')

    # Instructions
    if recipe['instructions']:
        md.append("\n## Instructions\n")
        for i, instr in enumerate(recipe['instructions'], 1):
            md.append(f"{i}. {instr}\n")

    md.append("\n---\n")
    md.append("*Extracted from Serious Eats*\n")

    return '\n'.join(md)


def recipe_to_json(recipe):
    """Convert recipe dict to JSON format."""
    return json.dumps(recipe, indent=2, ensure_ascii=False)


def process_files(directory, dry_run=False, output_format='md', keep_html=False):
    """Process all Serious Eats HTML files in the directory."""

    pattern = os.path.join(directory, 'www.seriouseats.com_*.html')
    files = glob.glob(pattern)

    print(f"\nFound {len(files)} files matching pattern\n")
    print("=" * 60)

    stats = {
        'recipes_found': 0,
        'non_recipes': 0,
        'errors': 0,
        'processed': [],
        'skipped': [],
    }

    for filepath in sorted(files):
        filename = os.path.basename(filepath)
        print(f"\nProcessing: {filename}")

        try:
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                html_content = f.read()

            # Check if it's a recipe
            if not is_recipe_file(html_content):
                print(f"  [SKIP] Not a recipe file, skipping")
                stats['non_recipes'] += 1
                stats['skipped'].append(filename)
                continue

            # Extract recipe data
            recipe = extract_recipe_simple(html_content)

            if not recipe['title']:
                print(f"  [WARN] Could not extract title, skipping")
                stats['errors'] += 1
                continue

            if not recipe['ingredients'] and not recipe['instructions']:
                print(f"  [WARN] No ingredients or instructions found, skipping")
                stats['errors'] += 1
                continue

            # Generate new filename
            slug = slugify(recipe['title'])
            if output_format == 'json':
                new_filename = f"{slug}.json"
                content = recipe_to_json(recipe)
            else:
                new_filename = f"{slug}.md"
                content = recipe_to_markdown(recipe)

            new_filepath = os.path.join(directory, 'recipes', new_filename)

            print(f"  Title: {recipe['title']}")
            print(f"  Ingredients: {len(recipe['ingredients'])}")
            print(f"  Instructions: {len(recipe['instructions'])} steps")
            print(f"  New file: recipes/{new_filename}")

            if dry_run:
                print(f"  [DRY RUN] Would create: {new_filepath}")
                if not keep_html:
                    print(f"  [DRY RUN] Would delete: {filepath}")
            else:
                # Create recipes directory if needed
                recipes_dir = os.path.join(directory, 'recipes')
                os.makedirs(recipes_dir, exist_ok=True)

                # Write new file
                with open(new_filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"  [OK] Created: {new_filepath}")

                # Delete original HTML
                if not keep_html:
                    os.remove(filepath)
                    print(f"  [DEL] Deleted: {filename}")

            stats['recipes_found'] += 1
            stats['processed'].append({
                'original': filename,
                'new': new_filename,
                'title': recipe['title'],
            })

        except Exception as e:
            print(f"  [ERROR] {str(e)}")
            stats['errors'] += 1

    # Print summary
    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"[OK] Recipes extracted: {stats['recipes_found']}")
    print(f"[SKIP] Non-recipe files: {stats['non_recipes']}")
    print(f"[ERROR] Errors: {stats['errors']}")

    if stats['processed']:
        print(f"\nExtracted recipes:")
        for item in stats['processed']:
            print(f"   - {item['title']}")

    if stats['skipped']:
        print(f"\nSkipped files (not recipes):")
        for name in stats['skipped'][:10]:
            print(f"   - {name}")
        if len(stats['skipped']) > 10:
            print(f"   ... and {len(stats['skipped']) - 10} more")

    return stats


def main():
    parser = argparse.ArgumentParser(
        description='Extract recipe information from Serious Eats HTML files'
    )
    parser.add_argument(
        '--dry-run',
        action='store_true',
        help='Show what would be done without making changes'
    )
    parser.add_argument(
        '--output-format',
        choices=['md', 'json'],
        default='md',
        help='Output format: md (Markdown) or json'
    )
    parser.add_argument(
        '--keep-html',
        action='store_true',
        help='Keep original HTML files instead of deleting them'
    )
    parser.add_argument(
        '--directory',
        default='.',
        help='Directory containing the HTML files'
    )

    args = parser.parse_args()

    # Determine directory
    if args.directory == '.':
        # Try to find the project root
        script_dir = os.path.dirname(os.path.abspath(__file__))
        project_dir = os.path.dirname(script_dir)
        if os.path.exists(os.path.join(project_dir, 'www.seriouseats.com.html')):
            directory = project_dir
        else:
            directory = os.getcwd()
    else:
        directory = args.directory

    print(f"Recipe Extractor for Serious Eats")
    print(f"Directory: {directory}")
    print(f"Output format: {args.output_format}")
    print(f"Dry run: {args.dry_run}")
    print(f"Keep HTML: {args.keep_html}")

    process_files(
        directory,
        dry_run=args.dry_run,
        output_format=args.output_format,
        keep_html=args.keep_html
    )


if __name__ == '__main__':
    main()
