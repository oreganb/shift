# SHIFT Ontology Website

PHP + JavaScript + AJAX website for the SHIFT Ontology, built from the Simple admin template.

## Stack

- **PHP** — server pages and AJAX API
- **MySQL** — optional search/cache (site works from JSON files alone)
- **JSON** — generated ontology and Knowledge Graph data
- **JavaScript** — Knowledge Graph viewer, global search

## Quick start

```bash
cd web

# Generate JSON from OWL modules and Obsidian vault
php scripts/build-ontology-json.php
php scripts/build-vault-json.php
php scripts/build-search-index.php

# Optional: MySQL
mysql -u root < scripts/schema.sql
cp includes/config.local.example.php includes/config.local.php  # edit credentials
php scripts/import-db.php

# Run locally
php -S localhost:8080 -t .
```

Open http://localhost:8080

## Data sources

| Source | Location |
|--------|----------|
| OWL modules | `docs/SHIFT owl/`, `docs/Reasoning Rules/` → copied to `web/data/ontology/` |
| Knowledge Graph vault | `docs/SHIFT_Ontology_KG.zip` → extracted to `web/data/vault/` |
| Logo | `web/images/SHIFT_Logo.png` |

## Routes

- `/` — Home
- `/ontology.php` — Ontology overview
- `/ontology-class.php?name=…` — Class pages
- `/ontology-reference.php` — WIDOCO-style index
- `/ontology-hierarchy.php` — Class tree
- `/rules.php`, `/rule.php?id=…` — Reasoning rules
- `/graph.php` — Knowledge Graph viewer
- `/alignments.php` — Standards alignments
- `/downloads.php` — Downloads
- `/about.php` — About & citation

## API (AJAX)

- `GET api/search.php?q=…`
- `GET api/vault-note.php?slug=…`
- `GET api/vault-tree.php`
- `GET api/vault-graph.php?focus=…`

## Design

Palette from SHIFT logo (SPECS §7): navy `#1F3D7A`, amber `#F5A623`, green `#4A9440`, blue `#2E62B0`.

See `docs/SPECS.md` for full product specification.
