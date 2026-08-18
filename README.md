# Laravel Minify

Laravel Minify combines and minifies CSS and JavaScript files that are served from your Laravel application's `public` directory. It generates a stable, cache-friendly build filename and returns the corresponding HTML tag for use in Blade views.

## Maintained fork

This is a maintained fork of [DevFactoryCH/minify](https://github.com/DevFactoryCH/minify), published as [`theqdev/laravel-minify`](https://packagist.org/packages/theqdev/laravel-minify).

The upstream package enables the CSS minifier's legacy variable processor. That processor is not needed by this project and can interfere with modern native CSS custom properties such as `--brand-color` and `var(--brand-color)`. This fork disables that processor so native CSS custom properties are not interpreted as legacy variable syntax during minification.

The public PHP namespace is `Qdev\\LaravelMinify`. The Blade-facing `Minify` facade name remains unchanged.

## Requirements

- PHP 8.1 or later
- Laravel 10, 11, 12, or 13

Laravel 13 requires PHP 8.3 or later. The package retains its PHP 8.1 minimum so it can continue to support Laravel 10.

This package minifies already-built CSS and JavaScript. It does not compile Sass, bundle JavaScript modules, or replace Vite, Mix, or another asset build pipeline.

## Installation

Install the package with Composer:

```shell
composer require theqdev/laravel-minify
```

Laravel package discovery registers the service provider and `Minify` facade automatically. If package discovery is disabled, register `Qdev\\LaravelMinify\\MinifyServiceProvider` and the `Qdev\\LaravelMinify\\Facades\\MinifyFacade` facade in your application.

Publish the configuration file if you need to change the defaults:

```shell
php artisan vendor:publish --provider="Qdev\\LaravelMinify\\MinifyServiceProvider" --tag=config
```

This creates `config/minify.php`. Make sure the configured build directories exist, or can be created, and are writable by the PHP process in deployed environments.

### Moving from the upstream package

The fork keeps the same `Minify` facade name, configuration keys, and Blade API as the upstream v2 package. To replace it in an existing application, remove the upstream Composer package and then require this one:

```shell
composer remove devfactory/minify
composer require theqdev/laravel-minify
```

Update direct PHP imports and any manual provider or facade registrations from `Devfactory\\Minify` to `Qdev\\LaravelMinify`. Review your dependency lock file and run your application test suite as usual. Do not install both packages together.

## Usage

Use the `Minify` facade in Blade templates. Pass a path relative to `public/`, an array of paths, or—where appropriate—an external HTTP(S) URL.

### Stylesheets

```blade
<head>
    {!! Minify::stylesheet('/css/main.css') !!}

    {{-- Combine multiple files. --}}
    {!! Minify::stylesheet(['/css/main.css', '/css/theme.css']) !!}

    {{-- Add attributes to the generated link. --}}
    {!! Minify::stylesheet('/css/main.css', ['media' => 'print']) !!}

    {{-- Produce an absolute asset URL. --}}
    {!! Minify::stylesheet('/css/main.css')->withFullUrl() !!}

    {{-- Return only the generated build URL. --}}
    {{ Minify::stylesheet('/css/main.css')->onlyUrl() }}
</head>
```

Native CSS custom properties are supported and are preserved during minification:

```css
:root {
    --brand-color: #2563eb;
}

.button {
    color: var(--brand-color);
}
```

### JavaScript

```blade
<body>
    {{-- Your page content. --}}

    {!! Minify::javascript('/js/app.js') !!}
    {!! Minify::javascript(['/js/vendor.js', '/js/app.js'], ['defer' => true]) !!}
    {!! Minify::javascript('/js/app.js')->withFullUrl() !!}
</body>
```

### Whole directories

`stylesheetDir()` and `javascriptDir()` recursively collect matching files below a directory, combine them, and return one tag. File order is controlled by `reverse_sort` in the configuration, so set it deliberately when files depend on one another.

```blade
{!! Minify::stylesheetDir('/css/') !!}
{!! Minify::javascriptDir('/js/', ['defer' => true]) !!}
```

## Configuration

The published `config/minify.php` contains these options:

| Option | Default | Purpose |
| --- | --- | --- |
| `reverse_sort` | `true` | Sort directory files in descending order before combining them. |
| `ignore_environments` | `['local']` | Return separate original-file tags instead of creating a minified build. |
| `css_build_path` / `js_build_path` | `/css/builds/` / `/js/builds/` | Directories, relative to `public/`, where generated files are written. |
| `css_url_path` / `js_url_path` | same as the corresponding build path | Public URL paths used in generated tags; useful when the filesystem path and public URL differ. |
| `disable_mtime` | `false` | Exclude source modification times from generated filenames. |
| `hash_salt` | `''` | Append an application-specific value to generated filename hashes. |
| `base_url` | `''` | Base URL used by `withFullUrl()`; when empty, Laravel's request root is used. |

Generated asset filenames incorporate their source paths and, by default, source modification times. A source change therefore produces a new filename suitable for long-lived HTTP caching. If you disable modification times, set `hash_salt` to a value that changes with each deployment or asset release.

## Operational notes

- Generated files are written on demand. The web process needs permission to create and replace files in the configured build directories.
- Add the generated build directories to your deployment strategy as appropriate. They can be generated again, but they should not be treated as hand-authored source files.
- External URLs are fetched by the server and included in the generated build. Prefer locally managed assets when availability, privacy, or repeatable deployments matter.
- Test output after upgrading either CSS or JavaScript minifier dependencies, especially for syntax produced by newer build tools.

## Maintenance and releases

This fork is maintained by Qdev Tech for ongoing use in its Laravel projects and will be released through Packagist under `theqdev/laravel-minify`. Please report reproducible issues and compatibility requests in the [GitHub issue tracker](https://github.com/theqdev/laravel-minify/issues).

## License

Laravel Minify is open-sourced software licensed under the [MIT license](LICENSE).
