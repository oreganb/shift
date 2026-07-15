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

# Copy ontology OWL modules and Obsidian vault into web/data/ (see below)
# Then generate JSON:
php scripts/build-ontology-json.php
php scripts/build-vault-json.php
php scripts/build-search-index.php

# Optional: MySQL
mysql -u root -e "CREATE DATABASE IF NOT EXISTS shift_ontology"
mysql -u root shift_ontology < scripts/schema.sql
cp includes/config.local.example.php includes/config.local.php   # add credentials locally
php scripts/import-db.php

# Run locally
php -S localhost:8080 -t .
```

Open http://localhost:8080

## Data layout

Place source content under `web/data/` before building JSON:

| Content | Path |
|---------|------|
| OWL modules | `web/data/ontology/*.owl` |
| Obsidian vault | `web/data/vault/SHIFT Ontology/` |
| Generated JSON | `web/data/json/` (created by build scripts) |
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

Palette from SHIFT logo: navy `#1F3D7A`, amber `#F5A623`, green `#4A9440`, blue `#2E62B0`.

## Deployment

Do not commit server credentials or FTP/deploy scripts. Configure `includes/config.local.php` on the host only (gitignored).
