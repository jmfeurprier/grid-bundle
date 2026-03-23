# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
bin/phpunit     # Run tests (config: dev/phpunit.xml)
bin/phpstan     # Static analysis, level 10 (config: dev/phpstan.neon)
bin/phpcs       # Code style check, PSR-12 (config: dev/phpcs.xml)
bin/rector      # Automated code quality (config: dev/rector.php)
bin/phplint     # PHP syntax check (config: dev/phplint.yaml)
```

Run a single test class:
```bash
bin/phpunit tests/Path/To/FooTest.php
```

## Architecture

This is a Symfony bundle for rendering HTML table grids from YAML-configured definitions and runtime data.

### Two-phase design

**Phase 1 — Configuration loading (app boot):**
- YAML config → `GridConfigurationCollectionLoader` → `GridConfigurationCollection`
- Grids stored in `GridConfigurationRepository` (cached via `CacheableGridConfigurationRepository` decorator)
- Rendering presets applied to columns/footers via `PresetApplier` (from `jmf/rendering-preset-bundle`)

**Phase 2 — Grid generation (request time):**
- Twig calls `grid(gridId, items, arguments)` → `GridExtension` → `GridGenerator`
- `GridGenerator` retrieves config, validates arguments, then delegates to:
  - `ColumnCollectionGenerator` → `Column[]`
  - `RowCollectionGenerator` → `RowGenerator` → `RowCellGenerator` + `RowLinkGenerator` → `Row[]`
  - `FooterGenerator` → `Footer`
- Returns immutable `Grid` object, passed to `jmf/template-rendering` for HTML output

### Key dependency: `symfony/property-access`
Cell values are extracted from data items using Symfony's PropertyAccess component. The `source` field in column config is a property path (e.g., `user.name`).

### Bundle configuration entry points
- `src/JmfGridBundle.php` — registers config from `config/definition.php` and `config/services.yaml`
- Three config keys: `grids` (grid definitions), `template_path`, `twig_functions_prefix`

### External dependencies
- `jmf/rendering-preset-bundle` — preset system; columns and footers implement `WithPresetInterface`
- `jmf/template-rendering` — rendering engine used by `GridExtension`
- `webmozart/assert` — used throughout for internal validation
