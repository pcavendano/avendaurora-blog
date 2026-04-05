# Architecture Plan: Avenda Aurora - Mexican Cuisine Blog & Shop

## Project Overview

A multilingual Mexican cuisine website for a chef to showcase authentic recipes with ingredients sourced from specific stores. Users can purchase complete ingredient kits, learn about Mexican ingredients, and follow step-by-step recipes.

**Design Reference:** Serious Eats (www.seriouseats.com)
**CMS:** Kirby CMS (file-based, no database costs)
**Primary Languages:** Spanish (ES), English (EN), French (FR)
**Focus:** Mexican Cuisine with Store-Linked Ingredient Kits

---

## 1. Site Structure

### 1.1 Public Pages (No Authentication Required)
```
/                              # Homepage with featured recipes
/recetas                       # All recipes
/recetas/antojitos             # Street food & snacks
/recetas/platos-fuertes        # Main dishes
/recetas/sopas-caldos          # Soups & broths
/recetas/salsas                # Salsas & condiments
/recetas/postres               # Desserts
/recetas/bebidas               # Drinks (aguas frescas, etc.)
/recetas/desayunos             # Breakfast dishes
/recetas/mariscos              # Seafood dishes
/recetas/vegetarianos          # Vegetarian Mexican
/recetas/{slug}                # Individual recipe page

/ingredientes                  # Ingredients encyclopedia
/ingredientes/chiles           # Chile guide
/ingredientes/hierbas          # Herbs & spices
/ingredientes/maiz             # Corn & masa products
/ingredientes/frijoles         # Beans guide
/ingredientes/{slug}           # Individual ingredient page

/tiendas                       # Partner stores listing
/tiendas/{store-slug}          # Store page with available recipes

/about                         # About the chef
/contact                       # Contact form
/search                        # Recipe & ingredient search
```

### 1.2 Shop Pages
```
/shop                          # Shop landing page
/shop/kits                     # Recipe ingredient kits
/shop/kits/{recipe-slug}       # Specific recipe kit
/shop/productos                # Chef's products (salsas, spices)
/shop/libros                   # Recipe books/PDFs
/cart                          # Shopping cart
```

### 1.3 User Area (Authentication Required)
```
/account                       # User dashboard
/account/login                 # User login
/account/register              # User registration
/account/favorites             # Saved recipes
/account/purchases             # Purchase history
/account/downloads             # Digital downloads
/account/shopping-lists        # Saved shopping lists
```

### 1.4 Admin Area (Chef Only)
```
/panel                         # Kirby Panel (admin)
/panel/recipes                 # Manage recipes
/panel/ingredients             # Manage ingredient database
/panel/stores                  # Manage partner stores
/panel/products                # Manage shop products
```

---

## 2. Mexican Cuisine Categories

### 2.1 Recipe Categories (Tipos de Platillos)
```
Antojitos y Botanas          # Street Food & Snacks
├── Tacos
├── Quesadillas
├── Tostadas
├── Gorditas
├── Sopes
├── Tamales
├── Elotes y Esquites
└── Empanadas

Platos Fuertes               # Main Dishes
├── Moles
├── Enchiladas
├── Chiles Rellenos
├── Carnitas
├── Birria
├── Barbacoa
├── Cochinita Pibil
└── Pozole

Sopas y Caldos               # Soups & Broths
├── Caldo de Pollo
├── Caldo de Res
├── Sopa de Tortilla
├── Menudo
└── Consomé

Salsas y Aderezos            # Salsas & Condiments
├── Salsas Rojas
├── Salsas Verdes
├── Guacamole
├── Pico de Gallo
└── Adobos

Mariscos                     # Seafood
├── Ceviche
├── Aguachile
├── Tacos de Pescado
├── Camarones a la Diabla
└── Coctel de Camarón

Desayunos                    # Breakfast
├── Huevos Rancheros
├── Chilaquiles
├── Molletes
├── Enfrijoladas
└── Machaca

Postres                      # Desserts
├── Churros
├── Flan
├── Arroz con Leche
├── Tres Leches
└── Buñuelos

Bebidas                      # Drinks
├── Aguas Frescas
├── Horchata
├── Atole & Champurrado
├── Micheladas
└── Margaritas
```

---

## 3. Ingredients Encyclopedia (Ingredientes)

### 3.1 Educational Content Structure

**Key Feature:** Explain ingredient relationships (e.g., jalapeño = chipotle dried & smoked)

```
/ingredientes/chiles
├── Jalapeño
│   └── "Fresh green chile, medium heat (2,500-8,000 SHU)"
│   └── Related: Chipotle (same chile, dried & smoked)
│
├── Chipotle
│   └── "Smoked dried jalapeño, smoky flavor"
│   └── Related: Jalapeño (fresh version)
│   └── Forms: Dried, Canned in Adobo
│
├── Poblano
│   └── "Large mild chile for stuffing"
│   └── Related: Ancho (dried poblano)
│
├── Ancho
│   └── "Dried poblano, sweet & mild"
│   └── Used in: Moles, adobos
│
├── Serrano
│   └── "Smaller, hotter than jalapeño"
│
├── Habanero
│   └── "Very hot, fruity flavor, Yucatan cuisine"
│
├── Guajillo
│   └── "Dried, mild, used in salsas & stews"
│
├── Pasilla
│   └── "Dried chilaca, for moles"
│
└── Chile de Árbol
    └── "Small, very hot, for table salsas"
```

### 3.2 Ingredient Blueprint (`/site/blueprints/pages/ingredient.yml`)
```yaml
title: Ingredient
icon: 🌶️

tabs:
  content:
    columns:
      - width: 2/3
        sections:
          main:
            type: fields
            fields:
              title:
                type: text
                required: true
                translate: true

              spanish_name:
                type: text
                label: Spanish Name

              also_known_as:
                type: tags
                label: Also Known As
                translate: true

              description:
                type: textarea
                translate: true

              featured_image:
                type: files
                max: 1

              gallery:
                type: files

              # Educational content
              origin:
                type: textarea
                label: Origin & History
                translate: true

              flavor_profile:
                type: textarea
                label: Flavor Profile
                translate: true

              heat_level:
                type: range
                min: 0
                max: 10
                label: Heat Level (0-10)

              scoville_range:
                type: text
                label: Scoville Heat Units (SHU)

              culinary_uses:
                type: textarea
                label: How It's Used
                translate: true

              storage_tips:
                type: textarea
                label: Storage Tips
                translate: true

              # Relationships
              related_ingredients:
                type: pages
                query: site.find('ingredientes').children
                label: Related Ingredients
                info: "e.g., Jalapeño → Chipotle"

              relationship_explanation:
                type: textarea
                label: Relationship Explanation
                translate: true
                help: "Explain how this relates to other ingredients"

              substitutes:
                type: pages
                query: site.find('ingredientes').children
                label: Possible Substitutes

      - width: 1/3
        sections:
          meta:
            type: fields
            fields:
              category:
                type: select
                options:
                  chiles: Chiles
                  hierbas: Herbs & Spices
                  maiz: Corn & Masa
                  frijoles: Beans & Legumes
                  verduras: Vegetables
                  frutas: Fruits
                  lacteos: Dairy & Cheese
                  carnes: Meats
                  mariscos: Seafood
                  otros: Other

              available_forms:
                type: multiselect
                label: Available Forms
                options:
                  fresh: Fresh
                  dried: Dried
                  canned: Canned
                  frozen: Frozen
                  powder: Powder/Ground
                  paste: Paste

              season:
                type: multiselect
                label: Best Season
                options:
                  spring: Spring
                  summer: Summer
                  fall: Fall
                  winter: Winter
                  year-round: Year Round

              # Store availability
              available_at:
                type: pages
                query: site.find('tiendas').children
                label: Available at Stores
```

### 3.3 Ingredient Relationship Examples

| Fresh | Dried/Processed | Relationship |
|-------|-----------------|--------------|
| Jalapeño | Chipotle | Same chile, smoked & dried |
| Poblano | Ancho | Same chile, dried |
| Chilaca | Pasilla | Same chile, dried |
| Tomatillo | - | (no dried form commonly used) |
| Corn | Masa Harina | Ground dried corn |
| Cacao | Chocolate Mexicano | Processed with sugar/cinnamon |

---

## 4. Store Integration & Ingredient Kits

### 4.1 Store Blueprint (`/site/blueprints/pages/store.yml`)
```yaml
title: Partner Store
icon: 🏪

fields:
  title:
    type: text
    required: true

  logo:
    type: files
    max: 1

  description:
    type: textarea
    translate: true

  website:
    type: url

  locations:
    type: structure
    fields:
      address:
        type: textarea
      city:
        type: text
      phone:
        type: tel
      hours:
        type: textarea

  # For affiliate/partnership
  affiliate_link:
    type: url
    label: Affiliate Base URL

  affiliate_code:
    type: text
    label: Affiliate Code

  available_ingredients:
    type: pages
    query: site.find('ingredientes').children
    label: Ingredients Available Here
```

### 4.2 Recipe with Store-Linked Ingredients

**Recipe Blueprint Update:**
```yaml
# Inside recipe.yml
ingredients:
  type: structure
  translate: true
  fields:
    ingredient_ref:
      type: pages
      query: site.find('ingredientes').children
      max: 1
      label: Ingredient (from database)

    quantity:
      type: text
      label: Quantity
      width: 1/3

    unit:
      type: select
      width: 1/3
      options:
        pieces: pieces / piezas
        cups: cups / tazas
        tbsp: tbsp / cucharadas
        tsp: tsp / cucharaditas
        oz: oz / onzas
        lb: lb / libras
        g: grams / gramos
        kg: kg / kilogramos
        ml: ml / mililitros
        l: liters / litros

    preparation:
      type: text
      label: Preparation
      placeholder: "diced, minced, sliced..."
      translate: true

    optional:
      type: toggle
      label: Optional
      width: 1/4

    # Store linking
    preferred_store:
      type: pages
      query: site.find('tiendas').children
      max: 1
      label: Recommended Store

    product_link:
      type: url
      label: Direct Product Link (for cart)

    estimated_price:
      type: number
      step: 0.01
      before: "$"
```

### 4.3 Ingredient Kit Concept

```
┌─────────────────────────────────────────────────────────────┐
│  RECIPE: Tacos de Carnitas                                  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  INGREDIENT KIT - Available at: [La Michoacana]            │
│  ───────────────────────────────────────────────────────── │
│  ☑ Pork Shoulder (2 lbs)              $8.99                │
│  ☑ Corn Tortillas (30 pack)           $3.49                │
│  ☑ White Onion (2)                    $1.20                │
│  ☑ Cilantro (1 bunch)                 $0.99                │
│  ☑ Limes (6)                          $2.00                │
│  ☑ Salsa Verde                        $3.99                │
│  ───────────────────────────────────────────────────────── │
│  Kit Total: $20.66                                         │
│                                                             │
│  [Add Full Kit to Cart]  [Customize Kit]                   │
│                                                             │
│  💡 Missing: Orange (available at any grocery)             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### 4.4 Store Availability Display

```
┌─────────────────────────────────────────────────────────────┐
│  RECIPE: Mole Poblano                                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  WHERE TO BUY INGREDIENTS:                                  │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🏪 La Michoacana          [95% Available]           │   │
│  │    Missing: Chocolate Mexicano                       │   │
│  │    [View Kit - $34.50]                              │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🏪 El Super                [100% Available]         │   │
│  │    All ingredients in stock!                        │   │
│  │    [View Kit - $38.20]                              │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🏪 Amazon Grocery          [85% Available]          │   │
│  │    Missing: Fresh Tomatillos, Epazote              │   │
│  │    [View Kit - $42.00]                              │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. Authentication Strategy

### 5.1 Three User Levels
```
┌─────────────────────────────────────────────────────────────┐
│                        VISITORS                              │
│                     (No Login Required)                      │
│  - Browse all recipes                                        │
│  - Read ingredient guides                                    │
│  - View store information                                    │
│  - See recipe kits & prices                                 │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    REGISTERED USERS                          │
│                   (Free Account)                             │
│  - Save favorite recipes                                     │
│  - Create shopping lists                                     │
│  - Purchase ingredient kits                                  │
│  - Access purchase history                                   │
│  - Rate/review recipes                                       │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      ADMIN (Chef)                            │
│                 (Kirby Panel Access)                         │
│  - Create/Edit recipes                                       │
│  - Manage ingredient database                                │
│  - Update store partnerships                                 │
│  - Manage translations                                       │
│  - View sales & analytics                                    │
└─────────────────────────────────────────────────────────────┘
```

### 5.2 Authentication Solution
- **Admin:** Kirby Panel (built-in, free)
- **Users:** Kirby frontend auth or `bnomei/kirby3-auth` plugin
- **Cost:** Free

---

## 6. Recipe Blueprint (Complete)

```yaml
# /site/blueprints/pages/recipe.yml
title: Recipe / Receta
icon: 🍽️

tabs:
  recipe:
    label: Recipe Content
    columns:
      - width: 2/3
        sections:
          main:
            type: fields
            fields:
              title:
                type: text
                required: true
                translate: true

              description:
                type: textarea
                translate: true
                help: Brief description for cards

              featured_image:
                type: files
                max: 1
                layout: cards

              gallery:
                type: files
                layout: cards

              video_url:
                type: url
                label: Recipe Video (YouTube/Vimeo)

              # Timing
              prep_time:
                type: number
                after: "minutes"
                width: 1/3

              cook_time:
                type: number
                after: "minutes"
                width: 1/3

              total_time:
                type: number
                after: "minutes"
                width: 1/3

              servings:
                type: number
                width: 1/2

              difficulty:
                type: select
                width: 1/2
                options:
                  facil: Easy / Fácil / Facile
                  medio: Medium / Medio / Moyen
                  dificil: Hard / Difícil / Difficile

              # Ingredients with store linking
              ingredients:
                type: structure
                translate: true
                fields:
                  ingredient_ref:
                    type: pages
                    query: site.find('ingredientes').children
                    max: 1

                  custom_name:
                    type: text
                    label: Custom Name (if not in database)
                    translate: true

                  quantity:
                    type: text
                    width: 1/4

                  unit:
                    type: select
                    width: 1/4
                    options:
                      piezas: pieces
                      tazas: cups
                      cucharadas: tbsp
                      cucharaditas: tsp
                      gramos: g
                      kg: kg
                      ml: ml
                      litros: L

                  preparation:
                    type: text
                    label: Prep (diced, minced...)
                    translate: true
                    width: 1/4

                  optional:
                    type: toggle
                    width: 1/4

              # Instructions
              instructions:
                type: structure
                translate: true
                fields:
                  step_title:
                    type: text
                    label: Step Title (optional)
                    translate: true

                  instruction:
                    type: textarea
                    required: true
                    translate: true

                  tip:
                    type: text
                    label: Pro Tip
                    translate: true

                  image:
                    type: files
                    max: 1

              # Chef's notes
              tips:
                type: textarea
                label: Chef's Tips & Variations
                translate: true

              history:
                type: textarea
                label: Dish History/Background
                translate: true

      - width: 1/3
        sections:
          meta:
            type: fields
            fields:
              is_featured:
                type: toggle
                text: Feature on Homepage

              category:
                type: select
                required: true
                options:
                  antojitos: Antojitos y Botanas
                  platos-fuertes: Platos Fuertes
                  sopas-caldos: Sopas y Caldos
                  salsas: Salsas y Aderezos
                  mariscos: Mariscos
                  desayunos: Desayunos
                  postres: Postres
                  bebidas: Bebidas
                  vegetarianos: Vegetarianos

              subcategory:
                type: tags
                label: Subcategory
                options:
                  tacos: Tacos
                  quesadillas: Quesadillas
                  enchiladas: Enchiladas
                  tamales: Tamales
                  moles: Moles
                  ceviches: Ceviches
                  # ... more

              region:
                type: select
                label: Mexican Region
                options:
                  oaxaca: Oaxaca
                  yucatan: Yucatán
                  jalisco: Jalisco
                  veracruz: Veracruz
                  puebla: Puebla
                  norte: Norte de México
                  centro: Centro
                  sur: Sur
                  costeño: Costeño

              tags:
                type: tags
                translate: true

  kits:
    label: Store Kits
    sections:
      store_kits:
        type: fields
        fields:
          enable_kits:
            type: toggle
            text: Enable Ingredient Kits for this Recipe

          store_availability:
            type: structure
            label: Store Availability
            fields:
              store:
                type: pages
                query: site.find('tiendas').children
                max: 1

              availability_percent:
                type: range
                min: 0
                max: 100
                label: "% Ingredients Available"

              kit_price:
                type: number
                step: 0.01
                before: "$"

              kit_link:
                type: url
                label: Direct Cart Link

              missing_items:
                type: text
                label: Missing Items

  seo:
    label: SEO
    sections:
      seo:
        type: fields
        fields:
          seo_title:
            type: text
            translate: true

          seo_description:
            type: textarea
            translate: true
            maxlength: 160
```

---

## 7. Multilingual Setup

### 7.1 Language Configuration
```php
// /site/config/config.php
return [
    'languages' => true,
    'languages.detect' => true,

    'languages' => [
        [
            'code'    => 'es',
            'name'    => 'Español',
            'default' => true,
            'locale'  => 'es_MX.utf-8',
            'url'     => '/',
        ],
        [
            'code'    => 'en',
            'name'    => 'English',
            'locale'  => 'en_US.utf-8',
            'url'     => '/en',
        ],
        [
            'code'    => 'fr',
            'name'    => 'Français',
            'locale'  => 'fr_FR.utf-8',
            'url'     => '/fr',
        ]
    ]
];
```

### 7.2 URL Examples
```
Spanish (default):
  /recetas/tacos-de-carnitas
  /ingredientes/chiles/jalapeno

English:
  /en/recipes/carnitas-tacos
  /en/ingredients/chiles/jalapeno

French:
  /fr/recettes/tacos-de-carnitas
  /fr/ingredients/piments/jalapeno
```

---

## 8. Design Components (Serious Eats Style)

### 8.1 Homepage Layout
```
┌────────────────────────────────────────────────────────────┐
│  HEADER                                                     │
│  [Logo]  Recetas | Ingredientes | Tiendas | Shop | [ES▼]   │
│                                            🔍  👤  🛒      │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  HERO: Featured Recipe                                     │
│  ┌────────────────────────────────────────────────────┐   │
│  │  [Large Image: Mole Poblano]                       │   │
│  │                                                    │   │
│  │  PLATO FUERTE                                     │   │
│  │  Mole Poblano Tradicional                         │   │
│  │  "El rey de la cocina mexicana..."               │   │
│  │  [Ver Receta]  [Comprar Kit - $34.50]            │   │
│  └────────────────────────────────────────────────────┘   │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  RECETAS RECIENTES                                         │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐                      │
│  │ img  │ │ img  │ │ img  │ │ img  │                      │
│  │Tacos │ │Pozole│ │Salsa │ │Churro│                      │
│  │ $12  │ │ $18  │ │ $8   │ │ $10  │                      │
│  └──────┘ └──────┘ └──────┘ └──────┘                      │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  APRENDE SOBRE INGREDIENTES                                │
│  ┌─────────────────────────────────────────────────────┐  │
│  │ 🌶️ ¿Sabías que el chipotle es un jalapeño        │  │
│  │    ahumado? Descubre más...                        │  │
│  └─────────────────────────────────────────────────────┘  │
│  [Guía de Chiles] [Hierbas] [Maíz y Masa] [Frijoles]      │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  EXPLORA POR CATEGORÍA                                     │
│  ┌────────┐ ┌────────┐ ┌────────┐ ┌────────┐             │
│  │Antojito│ │ Platos │ │ Sopas  │ │Mariscos│             │
│  │   s    │ │Fuertes │ │        │ │        │             │
│  └────────┘ └────────┘ └────────┘ └────────┘             │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  NUESTRAS TIENDAS ASOCIADAS                                │
│  [La Michoacana] [El Super] [Northgate] [Amazon]          │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  FOOTER                                                    │
│  About | Contact | Newsletter | Social Links               │
└────────────────────────────────────────────────────────────┘
```

### 8.2 Recipe Page Layout
```
┌────────────────────────────────────────────────────────────┐
│  Breadcrumb: Inicio > Recetas > Antojitos > Tacos         │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  [ANTOJITOS]                                               │
│  Tacos de Carnitas                                         │
│  ★★★★☆ (42 reviews)                                       │
│                                                            │
│  ┌────────────────────────────────────────────────────┐   │
│  │                                                    │   │
│  │              [Hero Image]                          │   │
│  │                                                    │   │
│  └────────────────────────────────────────────────────┘   │
│                                                            │
│  [♡ Guardar] [🖨️ Imprimir] [📤 Compartir]                │
│                                                            │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐     │
│  │  Prep    │ │  Cook    │ │  Total   │ │ Porciones│     │
│  │  30 min  │ │  3 hrs   │ │ 3.5 hrs  │ │    8     │     │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘     │
│                                                            │
├──────────────────────────────┬─────────────────────────────┤
│  INGREDIENTES                │  COMPRA TU KIT              │
│  ─────────────               │  ─────────────              │
│  ☐ 2 lb puerco              │  🏪 La Michoacana           │
│    └─ [Ver en tienda]       │     100% disponible         │
│  ☐ 30 tortillas de maíz     │     Total: $20.66           │
│    └─ [Ver en tienda]       │     [Agregar al Carrito]    │
│  ☐ 1 cebolla blanca         │                             │
│  ☐ 1 manojo cilantro        │  🏪 El Super                │
│  ☐ 6 limones                │     95% disponible          │
│  ☐ Salsa verde              │     Total: $22.30           │
│                              │     [Ver Kit]               │
│  [Agregar a Lista de        │                             │
│   Compras]                   │                             │
│                              │                             │
├──────────────────────────────┴─────────────────────────────┤
│  INSTRUCCIONES                                             │
│  ─────────────                                             │
│  1. Corta el cerdo en cubos de 2 pulgadas...              │
│     💡 Tip: Usa cortes con grasa para más sabor          │
│                                                            │
│  2. En una olla grande...                                 │
│     [Step Image]                                          │
│                                                            │
│  3. Cocina a fuego lento por 3 horas...                   │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  SOBRE ESTE PLATILLO                                       │
│  Las carnitas originan de Michoacán...                    │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  INGREDIENTES DESTACADOS                                   │
│  ┌────────┐ ┌────────┐                                    │
│  │Cilantro│ │ Limón  │                                    │
│  │ [Leer] │ │ [Leer] │                                    │
│  └────────┘ └────────┘                                    │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  RECETAS RELACIONADAS                                      │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐                      │
│  │Tacos │ │Salsa │ │Arroz │ │Frijol│                      │
│  │Pastor│ │Verde │ │Rojo  │ │Negro │                      │
│  └──────┘ └──────┘ └──────┘ └──────┘                      │
└────────────────────────────────────────────────────────────┘
```

### 8.3 Ingredient Page Layout
```
┌────────────────────────────────────────────────────────────┐
│  Breadcrumb: Inicio > Ingredientes > Chiles > Jalapeño    │
├────────────────────────────────────────────────────────────┤
│                                                            │
│  ┌────────────────┐  JALAPEÑO                             │
│  │                │  Chile Jalapeño                        │
│  │   [Image]      │  Also: Cuaresmeño, Chile Gordo        │
│  │                │                                        │
│  └────────────────┘  Heat Level: ████████░░ 6/10          │
│                      Scoville: 2,500 - 8,000 SHU          │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  DESCRIPCIÓN                                               │
│  El jalapeño es uno de los chiles más populares...        │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  🔗 RELACIÓN CON OTROS INGREDIENTES                       │
│  ┌─────────────────────────────────────────────────────┐  │
│  │  ⚠️ ¿Sabías que...?                                 │  │
│  │                                                      │  │
│  │  El CHIPOTLE es el mismo chile jalapeño,            │  │
│  │  pero secado y ahumado. Este proceso le da          │  │
│  │  su característico sabor ahumado y lo hace          │  │
│  │  más picante y concentrado.                         │  │
│  │                                                      │  │
│  │  Jalapeño (fresco) ──────► Chipotle (ahumado)       │  │
│  │                                                      │  │
│  │  [Ver página del Chipotle →]                        │  │
│  └─────────────────────────────────────────────────────┘  │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  FORMAS DISPONIBLES                                        │
│  [Fresco] [En escabeche] [Chipotle en adobo] [En polvo]   │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  USOS CULINARIOS                                           │
│  • Salsas frescas (pico de gallo)                         │
│  • Rellenos (jalapeños rellenos)                          │
│  • En escabeche para tacos                                │
│  • Rajas con crema                                        │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  DÓNDE COMPRAR                                             │
│  🏪 La Michoacana - $2.99/lb                              │
│  🏪 El Super - $3.49/lb                                   │
│  🏪 Walmart - $4.99/lb                                    │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  RECETAS CON JALAPEÑO                                      │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐                      │
│  │Salsa │ │Rajas │ │Jalap.│ │Guaca │                      │
│  │Verde │ │Crema │ │Rellen│ │mole  │                      │
│  └──────┘ └──────┘ └──────┘ └──────┘                      │
│                                                            │
├────────────────────────────────────────────────────────────┤
│  SUSTITUTOS                                                │
│  Si no encuentras jalapeño: Serrano (más picante),        │
│  Poblano (más suave), o Anaheim                           │
└────────────────────────────────────────────────────────────┘
```

---

## 9. E-Commerce: Ingredient Kits & Products

### 9.1 What Can Be Sold
| Type | Description | Delivery |
|------|-------------|----------|
| **Ingredient Kits** | All ingredients for a recipe | Link to store cart |
| **Premium Recipes** | Exclusive detailed recipes (PDF) | Digital download |
| **Recipe Collections** | E-books by theme/region | Digital download |
| **Chef's Products** | House salsas, spice blends | Physical shipping |
| **Cooking Classes** | Video tutorials | Online access |

### 9.2 E-Commerce Solution: Snipcart
- **Cost:** Free under $500/month sales, then 2% fee
- **Features:** Cart, checkout, digital & physical products
- **Integration:** Easy with Kirby

### 9.3 Affiliate/Partner Model for Kits
Instead of selling ingredients directly:
1. Partner with local Mexican grocery stores
2. Create shopping lists/carts on their sites
3. Earn affiliate commission on purchases
4. Reduces inventory/shipping complexity

---

## 10. Technology Stack

| Component | Technology | Cost |
|-----------|------------|------|
| CMS | Kirby CMS | ~$99 one-time |
| Frontend | HTML/Tailwind CSS | Free |
| E-commerce | Snipcart | Free < $500/mo |
| Hosting | Shared PHP hosting | $5-15/month |
| SSL | Let's Encrypt | Free |
| Email | Mailgun/Sendgrid | Free tier |

---

## 11. Cost Summary

### One-Time Costs
| Item | Cost |
|------|------|
| Kirby CMS License | ~$99 |
| Domain Name | ~$12/year |
| **Total** | **~$111** |

### Monthly Costs
| Item | Cost |
|------|------|
| Hosting | $5-15 |
| E-commerce (Snipcart) | Free* |
| **Total Monthly** | **$5-15** |

*Free until $500/month in sales, then 2% transaction fee

---

## 12. Implementation Phases

### Phase 1: Foundation (Week 1-2)
- [ ] Set up Kirby CMS
- [ ] Configure multilingual (ES, EN, FR)
- [ ] Create recipe & ingredient blueprints
- [ ] Set up admin authentication

### Phase 2: Content Structure (Week 3)
- [ ] Build ingredient encyclopedia structure
- [ ] Create chile relationship content (jalapeño/chipotle)
- [ ] Set up recipe categories
- [ ] Add initial store partners

### Phase 3: Frontend (Week 4-5)
- [ ] Design homepage (Serious Eats inspired)
- [ ] Build recipe pages with kit integration
- [ ] Create ingredient pages with relationships
- [ ] Implement responsive design

### Phase 4: E-Commerce & Users (Week 6)
- [ ] Integrate Snipcart
- [ ] Build user registration/login
- [ ] Add favorites & shopping lists
- [ ] Set up store affiliate links

### Phase 5: Launch (Week 7)
- [ ] SEO optimization
- [ ] Content population
- [ ] Testing all languages
- [ ] Deploy to production

---

## 13. Future Enhancements

- [ ] Video recipe tutorials
- [ ] User recipe ratings & reviews
- [ ] Meal planning feature
- [ ] Mobile app (PWA)
- [ ] Subscription box service
- [ ] Live cooking classes
- [ ] Community recipe submissions
