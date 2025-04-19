# Template Engines

The Slim4 Vite Integration package supports various template engines. This document provides detailed information about using the package with each supported template engine.

## Table of Contents

- [Twig](#twig)
- [Plates](#plates)
- [Blade](#blade)
- [Volt](#volt)
- [Creating Custom Adapters](#creating-custom-adapters)

## Twig

[Twig](https://twig.symfony.com/) is a modern template engine for PHP.

### Installation

```bash
composer require slim/twig-view
```

### Configuration

```php
<?php

use Slim\Views\Twig;
use Slim4\ViteIntegration\ViteAssetHelper;
use Slim4\ViteIntegration\Middleware\TwigViteMiddleware;

// Create Twig instance
$twig = Twig::create('/path/to/templates', [
    'cache' => '/path/to/cache',
    'debug' => true,
    'auto_reload' => true,
]);

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    __DIR__ . '/public/build/manifest.json',
    false, // Development mode
    'http://localhost:5173',
    __DIR__ . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);

// Add middleware
$app->add(new TwigViteMiddleware($twig, $viteAssetHelper));
```

### Usage

Using the global `vite` variable:

```twig
<!DOCTYPE html>
<html>
<head>
    {{ vite.cssTag('resources/assets/web/js/app.js')|raw }}
    {{ vite.jsTag('resources/assets/web/js/app.js')|raw }}
</head>
<body>
    <img src="{{ vite.image('logo.png') }}" alt="Logo">
    <a href="{{ vite.asset('resources/assets/web/documents/sample.pdf') }}">Download PDF</a>
    <style>
        @font-face {
            font-family: 'MyFont';
            src: url('{{ vite.font('myfont.woff2') }}') format('woff2');
        }
    </style>
</body>
</html>
```

Using Twig functions:

```twig
<!DOCTYPE html>
<html>
<head>
    {{ vite_css('resources/assets/web/js/app.js') }}
    {{ vite_js('resources/assets/web/js/app.js') }}
</head>
<body>
    <img src="{{ vite_image('logo.png') }}" alt="Logo">
    <a href="{{ vite_asset('resources/assets/web/documents/sample.pdf') }}">Download PDF</a>
    <style>
        @font-face {
            font-family: 'MyFont';
            src: url('{{ vite_font('myfont.woff2') }}') format('woff2');
        }
    </style>
</body>
</html>
```

## Plates

[Plates](https://platesphp.com/) is a native PHP template system.

### Installation

```bash
composer require league/plates
```

### Configuration

```php
<?php

use League\Plates\Engine;
use Slim4\ViteIntegration\ViteAssetHelper;
use Slim4\ViteIntegration\Middleware\PlatesViteMiddleware;

// Create Plates instance
$plates = new Engine('/path/to/templates');

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    __DIR__ . '/public/build/manifest.json',
    false, // Development mode
    'http://localhost:5173',
    __DIR__ . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);

// Add middleware
$app->add(new PlatesViteMiddleware($plates, $viteAssetHelper));
```

### Usage

Using the global `vite` variable:

```php
<!DOCTYPE html>
<html>
<head>
    <?= $this->vite->cssTag('resources/assets/web/js/app.js') ?>
    <?= $this->vite->jsTag('resources/assets/web/js/app.js') ?>
</head>
<body>
    <img src="<?= $this->vite->image('logo.png') ?>" alt="Logo">
    <a href="<?= $this->vite->asset('resources/assets/web/documents/sample.pdf') ?>">Download PDF</a>
    <style>
        @font-face {
            font-family: 'MyFont';
            src: url('<?= $this->vite->font('myfont.woff2') ?>') format('woff2');
        }
    </style>
</body>
</html>
```

Using Plates functions:

```php
<!DOCTYPE html>
<html>
<head>
    <?= $this->vite_css('resources/assets/web/js/app.js') ?>
    <?= $this->vite_js('resources/assets/web/js/app.js') ?>
</head>
<body>
    <img src="<?= $this->vite_image('logo.png') ?>" alt="Logo">
    <a href="<?= $this->vite_asset('resources/assets/web/documents/sample.pdf') ?>">Download PDF</a>
    <style>
        @font-face {
            font-family: 'MyFont';
            src: url('<?= $this->vite_font('myfont.woff2') ?>') format('woff2');
        }
    </style>
</body>
</html>
```

## Blade

[Blade](https://laravel.com/docs/blade) is the simple, yet powerful templating engine provided with Laravel.

### Installation

```bash
composer require illuminate/view
```

### Configuration

```php
<?php

use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Filesystem\Filesystem;
use Slim4\ViteIntegration\ViteAssetHelper;
use Slim4\ViteIntegration\Middleware\BladeViteMiddleware;

// Create Blade instance
$filesystem = new Filesystem();
$resolver = new EngineResolver();
$resolver->register('php', function () {
    return new PhpEngine();
});
$resolver->register('blade', function () use ($filesystem) {
    $compiler = new BladeCompiler($filesystem, '/path/to/cache');
    return new CompilerEngine($compiler);
});
$finder = new FileViewFinder($filesystem, ['/path/to/templates']);
$blade = new Factory($resolver, $finder, new \Illuminate\Events\Dispatcher());

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    __DIR__ . '/public/build/manifest.json',
    false, // Development mode
    'http://localhost:5173',
    __DIR__ . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);

// Add middleware
$app->add(new BladeViteMiddleware($blade, $viteAssetHelper));
```

### Usage

Using the global `vite` variable:

```blade
<!DOCTYPE html>
<html>
<head>
    {!! $vite->cssTag('resources/assets/web/js/app.js') !!}
    {!! $vite->jsTag('resources/assets/web/js/app.js') !!}
</head>
<body>
    <img src="{{ $vite->image('logo.png') }}" alt="Logo">
    <a href="{{ $vite->asset('resources/assets/web/documents/sample.pdf') }}">Download PDF</a>
    <style>
        @font-face {
            font-family: 'MyFont';
            src: url('{{ $vite->font('myfont.woff2') }}') format('woff2');
        }
    </style>
</body>
</html>
```

Using Blade directives:

```blade
<!DOCTYPE html>
<html>
<head>
    @viteCss('resources/assets/web/js/app.js')
    @viteJs('resources/assets/web/js/app.js')
</head>
<body>
    <img src="@viteImage('logo.png')" alt="Logo">
    <a href="@viteAsset('resources/assets/web/documents/sample.pdf')">Download PDF</a>
    <style>
        @font-face {
            font-family: 'MyFont';
            src: url('@viteFont('myfont.woff2')') format('woff2');
        }
    </style>
</body>
</html>
```

## Volt

[Volt](https://docs.phalcon.io/4.0/en/volt) is a template engine for the Phalcon framework.

### Installation

```bash
composer require phalcon/cphalcon
```

### Configuration

```php
<?php

use Phalcon\Mvc\View\Engine\Volt\Compiler;
use Slim4\ViteIntegration\ViteAssetHelper;
use Slim4\ViteIntegration\Middleware\VoltViteMiddleware;

// Create Volt instance
$compiler = new Compiler();
$compiler->setOptions([
    'compiledPath' => '/path/to/cache',
    'compiledSeparator' => '_',
    'compiledExtension' => '.php',
    'compileAlways' => true,
]);

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    __DIR__ . '/public/build/manifest.json',
    false, // Development mode
    'http://localhost:5173',
    __DIR__ . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);

// Add middleware
$app->add(new VoltViteMiddleware($compiler, $viteAssetHelper));
```

### Usage

Using the global `vite` variable:

```volt
<!DOCTYPE html>
<html>
<head>
    {{ vite.cssTag('resources/assets/web/js/app.js') }}
    {{ vite.jsTag('resources/assets/web/js/app.js') }}
</head>
<body>
    <img src="{{ vite.image('logo.png') }}" alt="Logo">
    <a href="{{ vite.asset('resources/assets/web/documents/sample.pdf') }}">Download PDF</a>
    <style>
        @font-face {
            font-family: 'MyFont';
            src: url('{{ vite.font('myfont.woff2') }}') format('woff2');
        }
    </style>
</body>
</html>
```

Using Volt functions:

```volt
<!DOCTYPE html>
<html>
<head>
    {{ vite_css('resources/assets/web/js/app.js') }}
    {{ vite_js('resources/assets/web/js/app.js') }}
</head>
<body>
    <img src="{{ vite_image('logo.png') }}" alt="Logo">
    <a href="{{ vite_asset('resources/assets/web/documents/sample.pdf') }}">Download PDF</a>
    <style>
        @font-face {
            font-family: 'MyFont';
            src: url('{{ vite_font('myfont.woff2') }}') format('woff2');
        }
    </style>
</body>
</html>
```

## Creating Custom Adapters

If you want to use a template engine that is not supported out of the box, you can create a custom adapter.

### 1. Create a custom adapter class

```php
<?php

namespace App\Adapter;

use Slim4\ViteIntegration\Adapter\AbstractTemplateAdapter;

class CustomAdapter extends AbstractTemplateAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function registerFunctions(): void
    {
        if (!$this->viteAssetHelper) {
            return;
        }
        
        $viteAssetHelper = $this->viteAssetHelper;
        
        // Register functions for your template engine
        // This will depend on your template engine's API
        
        // Example:
        $this->engine->registerFunction('vite_js', function (string $entrypoint) use ($viteAssetHelper) {
            return $viteAssetHelper->jsTag($entrypoint);
        });
        
        $this->engine->registerFunction('vite_css', function (string $entrypoint, string $fallbackCss = null) use ($viteAssetHelper) {
            return $viteAssetHelper->cssTag($entrypoint, $fallbackCss);
        });
        
        $this->engine->registerFunction('vite_asset', function (string $path) use ($viteAssetHelper) {
            return $viteAssetHelper->asset($path);
        });
        
        $this->engine->registerFunction('vite_image', function (string $path, string $resourcePath = 'resources/assets/web/images', string $placeholder = 'placeholder.jpg') use ($viteAssetHelper) {
            return $viteAssetHelper->image($path, $resourcePath, $placeholder);
        });
        
        $this->engine->registerFunction('vite_font', function (string $path) use ($viteAssetHelper) {
            return $viteAssetHelper->font($path);
        });
        
        // Add global variable
        // This will depend on your template engine's API
        $this->engine->addGlobal('vite', $this->viteAssetHelper);
    }
}
```

### 2. Create a custom middleware class

```php
<?php

namespace App\Middleware;

use App\Adapter\CustomAdapter;
use Slim4\ViteIntegration\Middleware\AbstractViteMiddleware;
use Slim4\ViteIntegration\ViteAssetHelper;

class CustomViteMiddleware extends AbstractViteMiddleware
{
    /**
     * Constructor
     *
     * @param mixed $engine Your template engine instance
     * @param ViteAssetHelper $viteAssetHelper
     */
    public function __construct($engine, ViteAssetHelper $viteAssetHelper)
    {
        $adapter = new CustomAdapter($engine);
        parent::__construct($adapter, $viteAssetHelper);
    }
}
```

### 3. Use your custom middleware

```php
<?php

use App\Middleware\CustomViteMiddleware;
use Slim4\ViteIntegration\ViteAssetHelper;

// Create your template engine instance
$engine = new YourTemplateEngine();

// Create Vite Asset Helper
$viteAssetHelper = new ViteAssetHelper(
    __DIR__ . '/public/build/manifest.json',
    false, // Development mode
    'http://localhost:5173',
    __DIR__ . '/public',
    'build',
    [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
);

// Add middleware
$app->add(new CustomViteMiddleware($engine, $viteAssetHelper));
```
