# jmf/grid-bundle

A Symfony bundle for rendering HTML table grids from declarative YAML configuration and runtime data.

## Requirements

- PHP >= 8.3
- Symfony 7 or 8

## Installation

```bash
composer require jmf/grid-bundle
```

Register the bundle in `config/bundles.php`:

```php
return [
    // ...
    Jmf\Grid\JmfGridBundle::class => ['all' => true],
];
```

## Configuration

Create `config/packages/jmf_grid.yaml`:

```yaml
jmf_grid:
    # template_path: '@JmfGrid/grid.html.twig'   # default
    # twig_functions_prefix: ''                  # default (e.g. set to 'jmf_' → jmf_grid())

    grids:
        articles:
            grid:
                variables:
                    entityType: 'article'
            rows:
                link: '{{ path("article.read", {"id": _item.id}) }}'
                variables:
                    entityId: '{{ _item.id }}'
            columns:
                -
                    label:  'Title'
                    source: 'title'
                -
                    preset: 'date'
                    source: 'publishedAt'
                -
                    preset: 'button_show'
```

### Splitting grids configuration into separate files

Large projects can split grid definitions into one file per grid. Place YAML files under the configured paths and name each file after the grid id:

```yaml
jmf_grid:
    paths:
        - '%kernel.project_dir%/config/packages/jmf_grid'
```

File at `config/packages/jmf_grid/articles.yaml` then becomes the `articles` grid configuration.

### Grid options

| Key | Description |
|-----|-------------|
| `grid.variables` | Key-value pairs available in all column/footer templates |
| `grid.arguments` | Required runtime arguments (passed when calling `grid()`) |
| `rows.link` | Twig template for the row link URL. `_item` refers to the current data item |
| `rows.variables` | Key-value Twig templates evaluated per row. `_item` is available |
| `columns` | List of column definitions (see below) |
| `footer` | List of footer row definitions, each a list of footer cell definitions |

### Column options

| Key | Description |
|-----|-------------|
| `source` | Property path on the data item (uses Symfony PropertyAccess) |
| `label` | Column header label |
| `align` | Cell alignment (`left`, `center`, `right`, `start`, `end`) |
| `template` | Twig template for cell content. `_value` holds the extracted value |
| `preset` | Preset ID from `jmf/rendering-preset-bundle` (provides defaults for the above) |

Column-level settings override preset values when both are defined.

### Footer cell options

| Key | Description |
|-----|-------------|
| `align` | Cell alignment |
| `template` | Twig template for cell content |
| `value` | Static string value |
| `merge` | Number of columns to span |
| `preset` | Preset ID |

## Usage

In a Twig template:

```twig
{{ grid('articles', articles) }}

{# With runtime arguments: #}
{{ grid('articles', articles, {someArgument: 'value'}) }}

{# With extra template parameters: #}
{{ grid('articles', articles, {}, {class: 'table-striped'}) }}
```

The `grid()` function signature:

```
grid(gridId, items, arguments = [], parameters = [])
```

- `gridId` : key from your `jmf_grid.grids` config
- `items` : array of objects or associative arrays
- `arguments` : runtime values for argument-gated grids
- `parameters` : extra variables passed to the grid template

## Presets

Columns and footer cells support a `preset` key that references a named preset from [`jmf/rendering-preset-bundle`](https://github.com/jmfeurprier/rendering-preset-bundle). Presets define reusable defaults for `align`, `label`, `source`, and `template`. Column-level settings override preset values.

Example preset configuration (`config/packages/jmf_rendering_preset.yaml`):

```yaml
jmf_rendering_preset:
    properties:
        align:
            choices: [center, left, right, start, end]
            default: left
            required: false
        label:
            required: false

    presets:
        date:
            align:    'center'
            template: '{% if (_value is not null) %}{{ _value.format("Y-m-d") }}{% endif %}'

        button_show:
            align:    'end'
            template: '<a href="{{ path(entityType ~ ".show", {"id": entityId}) }}">Show</a>'

        button_edit:
            align:    'end'
            template: '<a href="{{ path(entityType ~ ".edit", {"id": entityId}) }}">Edit</a>'
```

## Architecture

```
src/
├── Configuration/    # YAML loader: assembles grid configs from paths + inline keys (used by the bundle)
├── Compilation/      # Compilers: transform raw config arrays into Definition objects (once, at container build)
│   └── PresetApplier.php
├── Definition/       # Value objects: compiled grid/column/row/footer definitions and their collection
├── Generation/       # Generators: produce Model objects from Definitions and runtime data
├── Model/            # Runtime model: Grid, Column, Row, RowCell, Footer, … (returned by generators)
├── Twig/             # GridExtension: exposes the grid() Twig function
└── JmfGridBundle.php
```

The flow at runtime:
1. **Configuration** → raw `array<string, mixed>` assembled from YAML at container build
2. **Compilation** → `GridDefinitionCollection` (value objects) built once by `GridDefinitionCollectionCompiler`, held as a DI singleton via `GridGeneratorFactory`
3. **Generation** → `GridGenerator::generate()` walks the definitions and runtime `$items`, producing a `Grid` model
4. **Twig** → `GridExtension` calls `GridGenerator` and renders the result through the configured template

## License

MIT
