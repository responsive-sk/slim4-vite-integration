# Slim4 Vite Integration

This package provides integration between Vite and various template engines in Slim 4 projects. It allows you to easily use Vite for frontend asset building in your Slim 4 projects with different template engines.

## Documentation

- [Getting Started](docs/getting-started/README.md)
- [Template Engines](docs/template-engines/README.md)
- [API Reference](docs/api/README.md)
- [Examples](docs/examples/README.md)

## Supported Template Engines

- Twig
- Plates
- Blade
- Volt

## Installation

```bash
composer require slim4/vite-integration
```

### Dependencies

This package requires the following dependencies:

- PHP 7.4 or higher
- Slim Framework 4.0 or higher
- PSR-7, PSR-15, and PSR-17 implementations

Depending on which template engine you want to use, you'll need to install the corresponding package:

- Twig: `composer require slim/twig-view:^3.0`
- Plates: `composer require league/plates:^3.0`
- Blade: `composer require illuminate/view:^8.0|^9.0|^10.0`
- Volt: `composer require phalcon/cphalcon:^4.0|^5.0`

## Usage

### 1. Create a Vite Asset Helper

```php
<?php

use Slim4\ViteIntegration\ViteAssetHelper;

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    __DIR__ . '/public/build/manifest.json', // Path to manifest.json
    false, // Development mode (true/false)
    'http://localhost:5173', // Vite dev server URL
    __DIR__ . '/public', // Public directory path
    'build', // Build directory (relative to public path)
    [
        'images' => 'assets/images', // Images directory (relative to public path)
        'fonts' => 'assets/fonts', // Fonts directory (relative to public path)
    ]
);
```

### 2. Register the middleware for your template engine

#### Twig

```php
<?php

use Slim4\ViteIntegration\Middleware\TwigViteMiddleware;

// Add middleware
$app->add(new TwigViteMiddleware($twig, $viteAssetHelper));
```

#### Plates

```php
<?php

use Slim4\ViteIntegration\Middleware\PlatesViteMiddleware;

// Add middleware
$app->add(new PlatesViteMiddleware($plates, $viteAssetHelper));
```

#### Blade

```php
<?php

use Slim4\ViteIntegration\Middleware\BladeViteMiddleware;

// Add middleware
$app->add(new BladeViteMiddleware($blade, $viteAssetHelper));
```

#### Volt

```php
<?php

use Slim4\ViteIntegration\Middleware\VoltViteMiddleware;

// Add middleware
$app->add(new VoltViteMiddleware($volt, $viteAssetHelper));
```

### 3. Use in templates

#### Twig

```twig
<!DOCTYPE html>
<html>
<head>
    {{ vite.cssTag('resources/assets/web/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/web/js/app.js')|raw }}
</head>
<body>
    <img src="{{ vite.image('logo.png') }}" alt="Logo">
</body>
</html>
```

Or using functions:

```twig
<!DOCTYPE html>
<html>
<head>
    {{ vite_css('resources/assets/web/js/app.js') }}
    {{ vite_js('resources/assets/web/js/app.js') }}
</head>
<body>
    <img src="{{ vite_image('logo.png') }}" alt="Logo">
</body>
</html>
```

#### Plates

```php
<!DOCTYPE html>
<html>
<head>
    <?= $this->vite_css('resources/assets/web/js/app.js') ?>
    <?= $this->vite_js('resources/assets/web/js/app.js') ?>
</head>
<body>
    <img src="<?= $this->vite_image('logo.png') ?>" alt="Logo">
</body>
</html>
```

Or using the global variable:

```php
<!DOCTYPE html>
<html>
<head>
    <?= $this->vite->cssTag('resources/assets/web/js/app.js') ?>
    <?= $this->vite->jsTag('resources/assets/web/js/app.js') ?>
</head>
<body>
    <img src="<?= $this->vite->image('logo.png') ?>" alt="Logo">
</body>
</html>
```

#### Blade

```blade
<!DOCTYPE html>
<html>
<head>
    @viteJs('resources/assets/web/js/app.js')
    @viteCss('resources/assets/web/js/app.js')
</head>
<body>
    <img src="@viteImage('logo.png')" alt="Logo">
</body>
</html>
```

Or using the global variable:

```blade
<!DOCTYPE html>
<html>
<head>
    {!! $vite->cssTag('resources/assets/web/js/app.js') !!}
    {!! $vite->jsTag('resources/assets/web/js/app.js') !!}
</head>
<body>
    <img src="{{ $vite->image('logo.png') }}" alt="Logo">
</body>
</html>
```

#### Volt

```volt
<!DOCTYPE html>
<html>
<head>
    {{ vite_css('resources/assets/web/js/app.js') }}
    {{ vite_js('resources/assets/web/js/app.js') }}
</head>
<body>
    <img src="{{ vite_image('logo.png') }}" alt="Logo">
</body>
</html>
```

Or using the global variable:

```volt
<!DOCTYPE html>
<html>
<head>
    {{ vite.cssTag('resources/assets/web/js/app.js') }}
    {{ vite.jsTag('resources/assets/web/js/app.js') }}
</head>
<body>
    <img src="{{ vite.image('logo.png') }}" alt="Logo">
</body>
</html>
```

### 4. Configure Vite

Create a `vite.config.js` file in your project root:

```js
import { defineConfig } from 'vite';
import { resolve } from 'path';
import legacy from '@vitejs/plugin-legacy';

export default defineConfig({
  plugins: [
    legacy({
      targets: ['defaults', 'not IE 11']
    })
  ],
  css: {
    postcss: './postcss.config.js'
  },
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        'web': resolve(__dirname, 'resources/assets/web/js/app.js'),
        'admin': resolve(__dirname, 'resources/assets/admin/js/app.js'),
        'web-css': resolve(__dirname, 'resources/assets/web/css/app.css'),
        'admin-css': resolve(__dirname, 'resources/assets/admin/css/app.css')
      },
      output: {
        entryFileNames: `assets/[name].[hash].js`,
        chunkFileNames: `assets/[name].[hash].js`,
        assetFileNames: `assets/[name].[hash].[ext]`
      }
    }
  },
  resolve: {
    alias: {
      '@web': resolve(__dirname, 'resources/assets/web'),
      '@admin': resolve(__dirname, 'resources/assets/admin')
    }
  }
});
```

## Available Methods

### ViteAssetHelper

- `jsTag(string $entrypoint): string` - Generate a script tag for a JavaScript entry point
- `cssTag(string $entrypoint, string $fallbackCss = null): string` - Generate a link tag for a CSS entry point
- `asset(string $path): string` - Get the path to an asset
- `image(string $path, string $resourcePath = 'resources/assets/web/images', string $placeholder = 'placeholder.jpg'): string` - Get the path to an image
- `font(string $path): string` - Get the path to a font

### Template Functions

- `vite_js(string $entrypoint): string` - Generate a script tag for a JavaScript entry point
- `vite_css(string $entrypoint, string $fallbackCss = null): string` - Generate a link tag for a CSS entry point
- `vite_asset(string $path): string` - Get the path to an asset
- `vite_image(string $path, string $resourcePath = 'resources/assets/web/images', string $placeholder = 'placeholder.jpg'): string` - Get the path to an image
- `vite_font(string $path): string` - Get the path to a font

## Development

### Running Tests

```bash
composer test
```

Or run specific test suites:

```bash
composer test:unit
composer test:integration
```

### Code Style

This package follows the PSR-12 coding standard. To check your code:

```bash
composer cs:check
```

To automatically fix code style issues:

```bash
composer cs:fix
```

### Static Analysis

To run static analysis with PHPStan:

```bash
composer phpstan
```

## License

MIT
