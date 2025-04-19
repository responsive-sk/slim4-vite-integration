# Getting Started with Slim4 Vite Integration

This guide will help you get started with the Slim4 Vite Integration package.

## Table of Contents

- [Installation](#installation)
- [Basic Configuration](#basic-configuration)
- [Development vs Production](#development-vs-production)
- [Integration with Slim4 Root](#integration-with-slim4-root)
- [Troubleshooting](#troubleshooting)

## Installation

### 1. Install the package via Composer

```bash
composer require slim4/vite-integration
```

### 2. Install required dependencies

Depending on which template engine you want to use, you'll need to install the corresponding package:

- Twig: `composer require slim/twig-view:^3.0`
- Plates: `composer require league/plates:^3.0`
- Blade: `composer require illuminate/view:^8.0|^9.0|^10.0`
- Volt: `composer require phalcon/cphalcon:^4.0|^5.0`

### 3. Install Vite and required dependencies

```bash
npm install -D vite @vitejs/plugin-legacy
# or with yarn
yarn add -D vite @vitejs/plugin-legacy
# or with pnpm
pnpm add -D vite @vitejs/plugin-legacy
```

## Basic Configuration

### 1. Create a Vite configuration file

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

### 2. Create a PostCSS configuration file

Create a `postcss.config.js` file in your project root:

```js
module.exports = {
  plugins: {
    'tailwindcss': {},
    'autoprefixer': {},
  }
}
```

### 3. Create your asset files

Create the following directory structure:

```
resources/
  assets/
    web/
      js/
        app.js
      css/
        app.css
    admin/
      js/
        app.js
      css/
        app.css
```

Example `resources/assets/web/js/app.js`:

```js
// Import CSS
import '../css/app.css';

// Import Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
  // Theme toggle functionality will be handled by Alpine.js
});
```

Example `resources/assets/web/css/app.css`:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom styles */
body {
  @apply bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200;
}
```

### 4. Configure the ViteAssetHelper in your application

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

### 5. Register the middleware for your template engine

For Twig:

```php
<?php

use Slim4\ViteIntegration\Middleware\TwigViteMiddleware;

// Add middleware
$app->add(new TwigViteMiddleware($twig, $viteAssetHelper));
```

For other template engines, see the [Template Engines](../template-engines/README.md) documentation.

### 6. Use in your templates

For Twig:

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

For other template engines, see the [Template Engines](../template-engines/README.md) documentation.

## Development vs Production

### Development Mode

In development mode, the package will use the Vite dev server to serve your assets. This provides hot module replacement (HMR) and other development features.

To enable development mode:

1. Start the Vite dev server:

```bash
npx vite
# or with npm script
npm run dev
# or with yarn
yarn dev
# or with pnpm
pnpm dev
```

2. Set the development mode to `true` in the ViteAssetHelper:

```php
$viteAssetHelper = new ViteAssetHelper(
    __DIR__ . '/public/build/manifest.json',
    true, // Development mode
    'http://localhost:5173',
    __DIR__ . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);
```

### Production Mode

In production mode, the package will use the built assets from the `public/build` directory.

To build your assets for production:

```bash
npx vite build
# or with npm script
npm run build
# or with yarn
yarn build
# or with pnpm
pnpm build
```

Then set the development mode to `false` in the ViteAssetHelper:

```php
$viteAssetHelper = new ViteAssetHelper(
    __DIR__ . '/public/build/manifest.json',
    false, // Production mode
    'http://localhost:5173',
    __DIR__ . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);
```

## Integration with Slim4 Root

The package is fully compatible with the `responsive-sk/slim4-root` package. Here's how to integrate them:

### 1. Install both packages

```bash
composer require responsive-sk/slim4-root
composer require slim4/vite-integration
```

### 2. Configure in index.php

```php
<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim4\Root\PathsInterface;
use Slim4\Root\PathsProvider;
use Slim4\ViteIntegration\ViteAssetHelper;
use Slim4\ViteIntegration\Middleware\TwigViteMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// Define root path
$rootPath = dirname(__DIR__);

// Create container
$containerBuilder = new ContainerBuilder();

// Load configuration
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');

$container = $containerBuilder->build();

// Register paths services with custom paths and auto-discovery
$customPaths = [
    'views' => $rootPath . '/app/Infrastructure/Web/View/templates',
    'config' => $rootPath . '/config',
    'database' => $rootPath . '/data',
    'cache' => $rootPath . '/var/cache',
    'logs' => $rootPath . '/var/logs',
    'public' => $rootPath . '/public',
    'storage' => $rootPath . '/data',
];

// Register paths provider
PathsProvider::register($container, $rootPath, $customPaths, true, false);

// Get app from container
$app = $container->get(\Slim\App::class);

// Add Vite middleware
$twig = $container->get(Twig::class);
$viteAssetHelper = new ViteAssetHelper(
    $rootPath . '/public/build/manifest.json',
    false, // Set to true for development mode
    'http://localhost:5173',
    $rootPath . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);
$app->add(new TwigViteMiddleware($twig, $viteAssetHelper));

// Run app
$app->run();
```

### 3. Configure in container.php

You can add configuration for Vite to your container:

```php
<?php

use Slim4\ViteIntegration\ViteAssetHelper;

return [
    // Vite asset helper
    ViteAssetHelper::class => function (\Psr\Container\ContainerInterface $container) {
        $paths = $container->get(PathsInterface::class);
        $rootPath = $paths->getRootPath();

        return new ViteAssetHelper(
            $rootPath . '/public/build/manifest.json',
            false, // Set to true for development mode
            'http://localhost:5173',
            $rootPath . '/public',
            'build',
            [
                'images' => 'assets/images',
                'fonts' => 'assets/fonts',
            ]
        );
    },
];
```

## Troubleshooting

### Assets not loading in development mode

1. Make sure the Vite dev server is running
2. Check that the development mode is set to `true` in the ViteAssetHelper
3. Verify that the Vite dev server URL is correct
4. Check the browser console for errors

### Assets not loading in production mode

1. Make sure you have built your assets with `npx vite build`
2. Check that the development mode is set to `false` in the ViteAssetHelper
3. Verify that the manifest.json file exists in the specified location
4. Check the browser console for errors

### CSS not loading in production mode

1. Make sure you are importing your CSS file in your JavaScript file
2. Check that the manifest.json file contains entries for your CSS files
3. Verify that the cssTag method is being called with the correct entry point

### Images not loading

1. Check that the images directory is correctly configured in the ViteAssetHelper
2. Verify that the images exist in the specified location
3. Check the browser console for 404 errors

For more troubleshooting tips, see the [Examples](../examples/README.md) documentation.
