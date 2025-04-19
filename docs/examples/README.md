# Examples

This document provides examples of using the Slim4 Vite Integration package in various scenarios.

## Table of Contents

- [Basic Example](#basic-example)
- [Development Mode Example](#development-mode-example)
- [Production Mode Example](#production-mode-example)
- [Multiple Entry Points Example](#multiple-entry-points-example)
- [Tailwind CSS Example](#tailwind-css-example)
- [Alpine.js Example](#alpinejs-example)
- [Theme Toggle Example](#theme-toggle-example)
- [Image Handling Example](#image-handling-example)
- [Font Handling Example](#font-handling-example)
- [Integration with Slim4 Root Example](#integration-with-slim4-root-example)

## Basic Example

This example shows how to use the package with Twig in a basic setup.

### index.php

```php
<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim4\ViteIntegration\ViteAssetHelper;
use Slim4\ViteIntegration\Middleware\TwigViteMiddleware;

require_once __DIR__ . '/../vendor/autoload.php';

// Define root path
$rootPath = dirname(__DIR__);

// Create container
$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

// Create Twig instance
$twig = Twig::create($rootPath . '/templates', [
    'cache' => $rootPath . '/var/cache/twig',
    'debug' => true,
    'auto_reload' => true,
]);

// Create app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    $rootPath . '/public/build/manifest.json',
    false, // Development mode
    'http://localhost:5173',
    $rootPath . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);

// Add middleware
$app->add(new TwigViteMiddleware($twig, $viteAssetHelper));

// Define routes
$app->get('/', function ($request, $response) use ($twig) {
    return $twig->render($response, 'index.twig');
});

// Run app
$app->run();
```

### templates/index.twig

```twig
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slim4 Vite Integration</title>
    {{ vite.cssTag('resources/assets/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/js/app.js')|raw }}
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Slim4 Vite Integration</h1>
        <p class="mb-4">This is a basic example of using the Slim4 Vite Integration package.</p>
        <img src="{{ vite.image('logo.png') }}" alt="Logo" class="w-32 h-32">
    </div>
</body>
</html>
```

### resources/assets/js/app.js

```js
// Import CSS
import '../css/app.css';

// Your JavaScript code here
console.log('Slim4 Vite Integration');
```

### resources/assets/css/app.css

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom styles */
body {
    @apply bg-gray-100;
}
```

### vite.config.js

```js
import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        'app': resolve(__dirname, 'resources/assets/js/app.js'),
      },
    },
  },
});
```

## Development Mode Example

This example shows how to use the package in development mode.

### index.php

```php
<?php

// ... (same as basic example)

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    $rootPath . '/public/build/manifest.json',
    true, // Development mode
    'http://localhost:5173',
    $rootPath . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);

// ... (same as basic example)
```

### package.json

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "devDependencies": {
    "vite": "^4.0.0"
  }
}
```

### Running the development server

```bash
npm run dev
```

## Production Mode Example

This example shows how to use the package in production mode.

### index.php

```php
<?php

// ... (same as basic example)

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    $rootPath . '/public/build/manifest.json',
    false, // Production mode
    'http://localhost:5173',
    $rootPath . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);

// ... (same as basic example)
```

### Building for production

```bash
npm run build
```

## Multiple Entry Points Example

This example shows how to use multiple entry points.

### vite.config.js

```js
import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
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

### templates/web.twig

```twig
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web</title>
    {{ vite.cssTag('resources/assets/web/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/web/js/app.js')|raw }}
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Web</h1>
    </div>
</body>
</html>
```

### templates/admin.twig

```twig
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    {{ vite.cssTag('resources/assets/admin/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/admin/js/app.js')|raw }}
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Admin</h1>
    </div>
</body>
</html>
```

## Tailwind CSS Example

This example shows how to use the package with Tailwind CSS.

### package.json

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "devDependencies": {
    "autoprefixer": "^10.4.0",
    "postcss": "^8.4.0",
    "tailwindcss": "^3.0.0",
    "vite": "^4.0.0"
  }
}
```

### tailwind.config.js

```js
module.exports = {
  content: [
    './templates/**/*.twig',
    './resources/assets/**/*.js',
  ],
  darkMode: 'class',
  theme: {
    extend: {},
  },
  plugins: [],
}
```

### postcss.config.js

```js
module.exports = {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  }
}
```

### resources/assets/css/app.css

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom styles */
body {
    @apply bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200;
}
```

## Alpine.js Example

This example shows how to use the package with Alpine.js.

### package.json

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "devDependencies": {
    "alpinejs": "^3.0.0",
    "vite": "^4.0.0"
  }
}
```

### resources/assets/js/app.js

```js
// Import CSS
import '../css/app.css';

// Import Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

### templates/index.twig

```twig
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine.js Example</title>
    {{ vite.cssTag('resources/assets/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/js/app.js')|raw }}
</head>
<body>
    <div class="container mx-auto p-4">
        <div x-data="{ open: false }">
            <button @click="open = !open" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Toggle
            </button>
            <div x-show="open" class="mt-4 p-4 bg-gray-200 dark:bg-gray-800">
                This content is hidden by default.
            </div>
        </div>
    </div>
</body>
</html>
```

## Theme Toggle Example

This example shows how to implement a theme toggle using Alpine.js.

### resources/assets/js/app.js

```js
// Import CSS
import '../css/app.css';

// Import Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

### templates/index.twig

```twig
<!DOCTYPE html>
<html x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Toggle Example</title>
    {{ vite.cssTag('resources/assets/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/js/app.js')|raw }}
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Theme Toggle Example</h1>
            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="p-2 rounded-md bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
                <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
            </button>
        </div>
        <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-md shadow">
            <p>This is a theme toggle example using Alpine.js.</p>
        </div>
    </div>
</body>
</html>
```

## Image Handling Example

This example shows how to handle images.

### templates/index.twig

```twig
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Handling Example</title>
    {{ vite.cssTag('resources/assets/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/js/app.js')|raw }}
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Image Handling Example</h1>
        
        <!-- Basic image -->
        <img src="{{ vite.image('logo.png') }}" alt="Logo" class="w-32 h-32 mb-4">
        
        <!-- Image with custom resource path -->
        <img src="{{ vite.image('banner.jpg', 'resources/assets/images') }}" alt="Banner" class="w-full h-64 object-cover mb-4">
        
        <!-- Image with placeholder -->
        <img src="{{ vite.image('non-existent.jpg', 'resources/assets/images', 'placeholder.jpg') }}" alt="Placeholder" class="w-32 h-32">
    </div>
</body>
</html>
```

## Font Handling Example

This example shows how to handle fonts.

### resources/assets/css/app.css

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom font */
@font-face {
    font-family: 'MyFont';
    src: url('../fonts/myfont.woff2') format('woff2');
    font-weight: normal;
    font-style: normal;
}

/* Custom styles */
body {
    @apply bg-gray-100;
    font-family: 'MyFont', sans-serif;
}
```

### templates/index.twig

```twig
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Font Handling Example</title>
    {{ vite.cssTag('resources/assets/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/js/app.js')|raw }}
    <style>
        @font-face {
            font-family: 'AnotherFont';
            src: url('{{ vite.font('anotherfont.woff2') }}') format('woff2');
            font-weight: normal;
            font-style: normal;
        }
        
        .another-font {
            font-family: 'AnotherFont', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Font Handling Example</h1>
        <p class="mb-4">This text uses the default font (MyFont).</p>
        <p class="another-font">This text uses AnotherFont.</p>
    </div>
</body>
</html>
```

## Integration with Slim4 Root Example

This example shows how to integrate the package with the Slim4 Root package.

### composer.json

```json
{
    "require": {
        "php": "^7.4|^8.0",
        "slim/slim": "^4.0",
        "slim/psr7": "^1.0",
        "php-di/php-di": "^6.0",
        "responsive-sk/slim4-root": "dev-main",
        "slim4/vite-integration": "dev-main"
    }
}
```

### public/index.php

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

### config/container/views.php

```php
<?php

use Slim\Views\Twig;
use Slim4\Root\PathsInterface;
use Slim4\ViteIntegration\ViteAssetHelper;

return [
    // Twig template engine
    Twig::class => function (\Psr\Container\ContainerInterface $container) {
        $paths = $container->get(PathsInterface::class);
        $rootPath = $paths->getRootPath();
        $twigPath = $rootPath . '/app/Infrastructure/Web/View/templates';
        
        $twig = Twig::create($twigPath, [
            'cache' => false,
            'debug' => true,
            'auto_reload' => true,
        ]);
        
        return $twig;
    },
    
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
