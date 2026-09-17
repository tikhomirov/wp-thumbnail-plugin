# WP Thumbnails

[![Version](https://img.shields.io/badge/version-1.0.3-blue.svg)](https://github.com/tikhomirov/wp-thumbnail-plugin/releases)
[![WordPress](https://img.shields.io/badge/WordPress-5.6%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)

On-the-fly thumbnail generation and caching for WordPress. A modern fork of Kama Thumbnail with WebP support, PSR-4 architecture, and both legacy (`kama_thumb_*`) and modern (`thumb_*`) APIs.

## Requirements

| Component | Minimum | Tested |
|-----------|---------|--------|
| **WordPress** | 5.6 | 6.6 – 6.9 |
| **PHP** | 7.4 | 7.4, 8.0 – 8.4 |
| **Image library** | GD or Imagick | GD, Imagick |

## Features

- **On-the-fly thumbnails** — resize and crop images with filesystem caching
- **WebP output** — optional WebP generation when supported by the server
- **Smart source resolution** — featured image, first content image, attachments, or custom source
- **Admin settings** — quality, format, cache directory, placeholder image
- **Dual API** — modern `thumb_*` functions and legacy `kama_thumb_*` aliases
- **Filters** — `get_post_thumbnail` and other hooks for integration with term covers and themes
- **PSR-4 architecture** — domain/application/infrastructure layers with Pest tests

## Installation

### From Git (submodule)

```bash
git submodule add git@github.com:tikhomirov/wp-thumbnail-plugin.git wp-content/plugins/wp-thumbnail-plugin
```

### Manual

1. Download the [latest release](https://github.com/tikhomirov/wp-thumbnail-plugin/releases).
2. Upload to `wp-content/plugins/wp-thumbnail-plugin/`.
3. Activate **Thumbnails** in WordPress admin.
4. Configure options under **Settings → Thumbnails**.

### Composer (inside the plugin)

```bash
cd wp-content/plugins/wp-thumbnail-plugin
composer install
```

## Usage

### Get thumbnail URL

```php
$url = thumb_src([
    'width'  => 480,
    'height' => 340,
    'crop'   => true,
]);

// With explicit source (attachment ID or URL)
$url = thumb_src([
    'width'  => 300,
    'height' => 200,
], get_post_thumbnail_id());
```

### Output `<img>` tag

```php
echo thumb_img([
    'width'  => 480,
    'height' => 340,
    'crop'   => true,
    'class'  => 'img-fluid rounded',
    'alt'    => 'Image description',
]);
```

### Output linked image

```php
echo thumb_a_img([
    'width'  => 480,
    'height' => 340,
    'crop'   => true,
]);
```

## Arguments

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `width` | int | 0 | Target width in pixels |
| `height` | int | 0 | Target height in pixels |
| `crop` | bool | true | Crop to exact dimensions |
| `class` | string | '' | CSS class for `<img>` |
| `alt` | string | '' | Alt text for `<img>` |
| `attr` | string | '' | Extra HTML attributes |
| `src` | string\|int | '' | Attachment ID or image URL |

## Legacy API

For backward compatibility with Kama Thumbnail themes and snippets:

- `kama_thumb_src()`
- `kama_thumb_img()`
- `kama_thumb_a_img()`

## Development

```bash
composer install
composer test          # Pest unit + integration tests
composer test:coverage
composer phpstan
composer lint
composer quality       # phpstan + lint
```

## Architecture

```
wp-thumbnail-plugin/
├── wp-thumbnail-plugin.php       # Bootstrap, constants
├── autoload.php
├── src/
│   ├── Application/
│   │   └── ThumbnailService.php  # Orchestration
│   ├── Domain/
│   │   ├── Contracts/            # Storage, ImageProcessor interfaces
│   │   ├── Services/             # ThumbnailGenerator
│   │   └── ValueObjects/         # ThumbnailProfile, ImageSource
│   └── Infrastructure/
│       ├── ImageProcessors/      # GD, Imagick
│       ├── Storage/              # FileSystemStorage
│       └── WordPress/            # Plugin, Settings, TemplateFunctions
├── views/admin/                  # Settings page
├── assets/js/                    # Admin scripts
└── tests/                        # Pest tests
```

Template functions (`thumb_src`, `thumb_img`, `thumb_a_img`) are registered by `TemplateFunctions` and delegate to `ThumbnailService`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

GPL-2.0-or-later

## Author

Aleksey Tikhomirov — [rwsite.ru](https://rwsite.ru)

---

## Русский

**WP Thumbnails** — плагин для генерации и кэширования миниатюр «на лету» в WordPress. Современный форк Kama Thumbnail с WebP, PSR-4 и двумя API.

### Совместимость

- **WordPress:** от 5.6, протестировано на 6.6 – 6.9
- **PHP:** от 7.4, протестировано на 7.4, 8.0 – 8.4
- **Библиотека изображений:** GD или Imagick

### Что умеет

- Генерация и кэширование миниатюр с обрезкой и изменением размера
- WebP при поддержке сервером
- Поиск исходника: featured image, первое изображение в контенте, вложения
- Настройки в **Настройки → Thumbnails**
- Современный API (`thumb_*`) и legacy (`kama_thumb_*`)
- Фильтр `get_post_thumbnail` для интеграции с обложками терминов

### Установка

Скачайте [релиз](https://github.com/tikhomirov/wp-thumbnail-plugin/releases) или подключите как git submodule. После активации настройте параметры в админке.

### Пример в шаблоне темы

```php
<?php echo thumb_img([
    'width'  => 480,
    'height' => 340,
    'crop'   => true,
    'class'  => 'img-fluid',
]); ?>
```
