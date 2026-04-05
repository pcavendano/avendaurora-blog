# Aurora - Project Architecture

A multilingual cuisine blog and shop built on **Kirby CMS**, featuring an automated recipe pipeline that scrapes, extracts, and imports recipes from external sources.

**Stack:** Python (data pipeline) + Kirby CMS (PHP, file-based) + Tailwind CSS
**Languages:** Spanish (default), English, French

---

## System Architecture

```mermaid
flowchart TD
    subgraph Sources["External Sources"]
        SHE["simplehomeedit.com"]
        SE["Serious Eats HTML files"]
    end

    subgraph Pipeline["Python Data Pipeline"]
        S1["scrape-simplehomeedit.py<br/>Web Scraper"]
        S2["scripts/extract-recipes.py<br/>HTML Extractor"]
        S3["scripts/migrate-recipes-to-kirby.py<br/>Kirby Migrator"]
    end

    subgraph Storage["Intermediate Storage"]
        MONGO[("MongoDB")]
        CSV["CSV files"]
        MD["recipes/<br/>Markdown files"]
    end

    subgraph Kirby["Kirby CMS"]
        CONTENT["content/1_recetas/<br/>recipe.txt files"]
        PANEL["Kirby Panel<br/>Admin UI"]
        TEMPLATES["Site Templates<br/>+ Blueprints"]
    end

    WEBSITE["Avenda Aurora Website"]

    SHE -->|"HTTP requests"| S1
    S1 -->|"Store raw data"| MONGO
    S1 -->|"Export"| CSV

    SE -->|"Local HTML files"| S2
    S2 -->|"Parse and extract"| MD

    MD -->|"Read .md files"| S3
    S3 -->|"Filter and convert"| CONTENT

    CONTENT --> TEMPLATES
    PANEL -->|"Manual editing"| CONTENT
    TEMPLATES --> WEBSITE

    style Sources fill:#fef3c7,stroke:#d97706
    style Pipeline fill:#dbeafe,stroke:#2563eb
    style Storage fill:#f3e8ff,stroke:#7c3aed
    style Kirby fill:#dcfce7,stroke:#16a34a
    style WEBSITE fill:#fee2e2,stroke:#dc2626
```

---

## Pipeline Sequence Diagram

```mermaid
sequenceDiagram
    autonumber
    participant User
    participant Scraper
    participant Web
    participant DB
    participant Extractor
    participant HTML
    participant FS
    participant Migrator
    participant Kirby

    Note over User,Kirby: Stage 1 - Web Scraping (scrape-simplehomeedit.py)

    User->>Scraper: Run scraper
    Scraper->>Web: HTTP GET simplehomeedit.com pages
    Web-->>Scraper: HTML responses
    Scraper->>DB: Save raw data to MongoDB
    Scraper->>FS: Export to CSV

    Note over User,Kirby: Stage 2 - HTML Extraction (extract-recipes.py)

    User->>Extractor: Run extractor
    Extractor->>HTML: Read Serious Eats HTML files
    Extractor->>Extractor: Detect recipe files
    Extractor->>Extractor: Parse with regex + HTMLParser
    Extractor->>Extractor: Extract title, ingredients, instructions
    Extractor->>FS: Write recipes/slug.md
    Extractor->>FS: Delete original HTML

    Note over User,Kirby: Stage 3 - Kirby Migration (migrate-recipes-to-kirby.py)

    User->>Migrator: Run migrator
    Migrator->>FS: Read recipes .md files
    Migrator->>Migrator: Parse markdown structure
    Migrator->>Migrator: Filter by Mexican cuisine keywords
    Migrator->>Migrator: Categorize into 9 categories
    Migrator->>Migrator: Parse ingredients into structured YAML
    Migrator->>FS: Write content/1_recetas/recipe.txt

    Note over User,Kirby: Stage 4 - Serving the Website

    User->>Kirby: Browse /recetas
    Kirby->>FS: Read recipe content files
    Kirby-->>User: Render recipe pages (ES/EN/FR)
```

---

## Component Details

### Stage 1: Web Scraper (`scrape-simplehomeedit.py`)

| | |
|---|---|
| **Source** | simplehomeedit.com |
| **Output** | MongoDB collection + CSV export |
| **Purpose** | Scrape recipe content from external website for later processing |

### Stage 2: HTML Extractor (`scripts/extract-recipes.py`)

| | |
|---|---|
| **Input** | `www.seriouseats.com_*.html` files in project root |
| **Output** | `recipes/{slug}.md` Markdown files |
| **Key logic** | Uses `HTMLParser` + regex fallback to extract title, description, author, tags, image URL, prep/cook/total time, servings, ingredients, and instructions |
| **Filtering** | Skips non-recipe pages (articles, roundups) by checking for ingredient/instruction markers |

### Stage 3: Kirby Migrator (`scripts/migrate-recipes-to-kirby.py`)

| | |
|---|---|
| **Input** | `recipes/*.md` |
| **Output** | `content/1_recetas/{slug}/recipe.txt` (Kirby content format) |
| **Key logic** | Parses Markdown into structured fields, filters for Mexican cuisine using 60+ keywords (taco, mole, pozole...), auto-categorizes into 9 categories (antojitos, platos-fuertes, sopas-caldos, salsas, mariscos, desayunos, postres, bebidas, vegetarianos), and generates structured YAML for ingredients |

### Stage 4: Kirby CMS

| | |
|---|---|
| **Content** | File-based (`content/` directory, no database) |
| **Admin** | Kirby Panel at `/panel` |
| **Features** | Multilingual (ES/EN/FR), recipe categories, ingredient encyclopedia, store-linked ingredient kits, e-commerce via Snipcart |

---

## Directory Structure

```
aurora-blog/
├── scripts/
│   ├── extract-recipes.py        # Stage 2: HTML → Markdown
│   └── migrate-recipes-to-kirby.py  # Stage 3: Markdown → Kirby
├── scrape-simplehomeedit.py      # Stage 1: Web → MongoDB/CSV
├── recipes/                      # Intermediate .md files
├── content/
│   └── 1_recetas/                # Kirby recipe content
│       └── {recipe-slug}/
│           └── recipe.txt
├── site/
│   ├── blueprints/               # Kirby field definitions
│   └── templates/                # Kirby page templates
├── kirby/                        # Kirby core
└── architecture-plan.md          # Full project spec
```
