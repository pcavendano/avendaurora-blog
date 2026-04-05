# Avenda Aurora - Documentation

Welcome to the documentation for the Avenda Aurora Mexican cuisine blog.

## Contents

| File | Description |
|------|-------------|
| [recipe-template.md](./recipe-template.md) | Blank template for creating new recipes |
| [recipe-example.md](./recipe-example.md) | Example filled-out recipe (Tacos de Carnitas) |
| [ingredient-template.md](./ingredient-template.md) | Template for ingredient encyclopedia entries |

---

## Quick Start: Creating a New Recipe

### 1. Copy the Template
```bash
cp docs/recipe-template.md content/recipes/my-new-recipe.md
```

### 2. Fill in the Sections (in order)

| Order | Section | Required | Notes |
|-------|---------|----------|-------|
| 1 | Recipe Name | Yes | In all 3 languages |
| 2 | Overview | Yes | 2-3 sentences |
| 3 | Recipe Details | Yes | Times, servings, difficulty |
| 4 | Category & Tags | Yes | Check the boxes |
| 5 | Ingredients | Yes | Use the table format |
| 6 | Instructions | Yes | Step by step with tips |
| 7 | Chef's Tips | Yes | Variations, make-ahead |
| 8 | History | Recommended | Cultural context |
| 9 | Store Kit Info | If applicable | For selling kits |
| 10 | SEO Info | Yes | For search engines |
| 11 | Media | Yes | Photos checklist |

### 3. Add Photos

Required photos:
- `[recipe-slug]-hero.jpg` - Main image (16:9)
- `[recipe-slug]-final.jpg` - Finished dish

Optional:
- `[recipe-slug]-step-N.jpg` - Step photos
- `[recipe-slug]-ingredients.jpg` - Flat lay

### 4. Translate

Once Spanish version is complete:
1. Copy content to English fields
2. Translate
3. Repeat for French

---

## Recipe Writing Guidelines

### Ingredients Format

**Always include:**
- Quantity (number)
- Unit (cups, tablespoons, pieces, etc.)
- Ingredient name (bilingual)
- Preparation (diced, minced, etc.)

**Good example:**
```
| 2 | lb | pork shoulder / espaldilla de cerdo | cut into 2" cubes |
```

**Bad example:**
```
| | | some pork | |
```

### Instructions Format

**Always:**
- Start with an action verb
- Be specific about times and temperatures
- Include visual cues ("until golden brown")
- Add chef's tips for tricky steps

**Good example:**
```
### Step 2: Sear the Meat
**Heat** the oil in a large pot over high heat until shimmering.
Add the pork pieces in a single layer and cook without moving
for 3-4 minutes until deeply browned on the bottom.

> **Chef's Tip:** Don't overcrowd the pan. Work in batches
> if needed for better browning.
```

### Ingredient Relationships

When using chiles or ingredients with dried/fresh versions, always link to the ingredient guide:

```
> **Note:** Chipotle is a dried, smoked jalapeño.
> See: /ingredientes/chiles/chipotle
```

---

## File Naming Convention

### Recipes
```
/content/recipes/[category]/[recipe-slug]/
  recipe.es.txt      # Spanish (default)
  recipe.en.txt      # English
  recipe.fr.txt      # French
  hero.jpg           # Main image
  step-1.jpg         # Step images
  ...
```

### Ingredients
```
/content/ingredientes/[category]/[ingredient-slug]/
  ingredient.es.txt
  ingredient.en.txt
  ingredient.fr.txt
  main.jpg
```

---

## Categories Reference

| Spanish | English | Slug |
|---------|---------|------|
| Antojitos y Botanas | Street Food & Snacks | antojitos |
| Platos Fuertes | Main Dishes | platos-fuertes |
| Sopas y Caldos | Soups & Broths | sopas-caldos |
| Salsas y Aderezos | Salsas & Condiments | salsas |
| Mariscos | Seafood | mariscos |
| Desayunos | Breakfast | desayunos |
| Postres | Desserts | postres |
| Bebidas | Drinks | bebidas |
| Vegetarianos | Vegetarian | vegetarianos |

---

## Common Measurements

| Spanish | English | Abbreviation |
|---------|---------|--------------|
| taza | cup | c |
| cucharada | tablespoon | tbsp |
| cucharadita | teaspoon | tsp |
| pieza | piece | pc |
| libra | pound | lb |
| onza | ounce | oz |
| gramo | gram | g |
| kilogramo | kilogram | kg |
| mililitro | milliliter | ml |
| litro | liter | L |

---

## Need Help?

- Check the [recipe-example.md](./recipe-example.md) for a complete example
- Review the [architecture-plan.md](../architecture-plan.md) for the full site structure
