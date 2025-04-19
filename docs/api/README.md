# API Reference

This document provides detailed information about the API of the Slim4 Vite Integration package.

## Dependencies

This package has the following dependencies:

### Required Dependencies

- `php`: ^7.4|^8.0
- `slim/slim`: ^4.0
- `psr/http-message`: ^1.0
- `psr/http-server-middleware`: ^1.0
- `psr/http-server-handler`: ^1.0
- `psr/container`: ^1.0|^2.0

### Optional Dependencies

Depending on which template engine you want to use, you'll need to install the corresponding package:

- Twig: `slim/twig-view:^3.0`
- Plates: `league/plates:^3.0`
- Blade: `illuminate/view:^8.0|^9.0|^10.0`
- Volt: `phalcon/cphalcon:^4.0|^5.0`

## Table of Contents

- [ViteAssetHelper](#viteassethelper)
- [TemplateEngineInterface](#templateengineinterface)
- [AbstractTemplateAdapter](#abstracttemplateadapter)
- [ViteMiddlewareInterface](#vitemiddlewareinterface)
- [AbstractViteMiddleware](#abstractvitemiddleware)
- [Template Engine Adapters](#template-engine-adapters)
- [Template Engine Middlewares](#template-engine-middlewares)

## ViteAssetHelper

The `ViteAssetHelper` class is the core of the package. It provides methods for generating asset URLs and HTML tags.

### Constructor

```php
public function __construct(
    ?string $manifestPath = null,
    bool $isDev = false,
    string $devServerUrl = 'http://localhost:5173',
    string $publicPath = null,
    string $buildDirectory = 'build',
    array $assetDirectories = [
        'images' => 'assets/images',
        'fonts' => 'assets/fonts',
    ]
)
```

#### Parameters

- `$manifestPath` - Path to the manifest.json file. If null, the package will try to find it in the default locations.
- `$isDev` - Whether to use development mode. In development mode, assets are served from the Vite dev server.
- `$devServerUrl` - URL of the Vite dev server. Only used in development mode.
- `$publicPath` - Path to the public directory. If null, the package will try to determine it automatically.
- `$buildDirectory` - Directory where Vite builds assets (relative to public path).
- `$assetDirectories` - Directories where assets are stored (relative to public path).

### Methods

#### getManifest

```php
public function getManifest(): array
```

Get the Vite manifest.

#### jsTag

```php
public function jsTag(string $entrypoint): string
```

Generate a script tag for a JavaScript entry point.

#### Parameters

- `$entrypoint` - Path to the JavaScript entry point.

#### Returns

HTML script tag.

#### cssTag

```php
public function cssTag(string $entrypoint, string $fallbackCss = null): string
```

Generate a link tag for a CSS entry point.

#### Parameters

- `$entrypoint` - Path to the JavaScript entry point that imports the CSS.
- `$fallbackCss` - Fallback CSS file if no CSS is found in the manifest.

#### Returns

HTML link tag.

#### asset

```php
public function asset(string $path): string
```

Get the path to an asset.

#### Parameters

- `$path` - Path to the asset.

#### Returns

URL to the asset.

#### image

```php
public function image(
    string $path,
    string $resourcePath = 'resources/assets/web/images',
    string $placeholder = 'placeholder.jpg'
): string
```

Get the path to an image.

#### Parameters

- `$path` - Path to the image.
- `$resourcePath` - Path to the resource in the source directory.
- `$placeholder` - Placeholder image to use if the image is not found.

#### Returns

URL to the image.

#### font

```php
public function font(string $path): string
```

Get the path to a font.

#### Parameters

- `$path` - Path to the font.

#### Returns

URL to the font.

## TemplateEngineInterface

The `TemplateEngineInterface` interface defines the contract for template engine adapters.

### Methods

#### registerViteAssetHelper

```php
public function registerViteAssetHelper(ViteAssetHelper $viteAssetHelper): void
```

Register the Vite asset helper with the template engine.

#### Parameters

- `$viteAssetHelper` - The Vite asset helper instance.

#### getEngine

```php
public function getEngine()
```

Get the underlying template engine instance.

#### Returns

The template engine instance.

## AbstractTemplateAdapter

The `AbstractTemplateAdapter` class is an abstract implementation of the `TemplateEngineInterface` interface.

### Constructor

```php
public function __construct($engine)
```

#### Parameters

- `$engine` - The template engine instance.

### Methods

#### getEngine

```php
public function getEngine()
```

Get the underlying template engine instance.

#### Returns

The template engine instance.

#### registerViteAssetHelper

```php
public function registerViteAssetHelper(ViteAssetHelper $viteAssetHelper): void
```

Register the Vite asset helper with the template engine.

#### Parameters

- `$viteAssetHelper` - The Vite asset helper instance.

#### registerFunctions

```php
abstract protected function registerFunctions(): void
```

Register template functions/helpers.

## ViteMiddlewareInterface

The `ViteMiddlewareInterface` interface defines the contract for Vite middlewares.

### Methods

#### process

```php
public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
```

Process an incoming server request.

#### Parameters

- `$request` - The request.
- `$handler` - The handler.

#### Returns

The response.

## AbstractViteMiddleware

The `AbstractViteMiddleware` class is an abstract implementation of the `ViteMiddlewareInterface` interface.

### Constructor

```php
public function __construct(TemplateEngineInterface $templateEngine, ViteAssetHelper $viteAssetHelper)
```

#### Parameters

- `$templateEngine` - The template engine adapter.
- `$viteAssetHelper` - The Vite asset helper instance.

### Methods

#### process

```php
public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
```

Process an incoming server request.

#### Parameters

- `$request` - The request.
- `$handler` - The handler.

#### Returns

The response.

## Template Engine Adapters

The package provides adapters for various template engines.

### TwigAdapter

Adapter for the Twig template engine.

```php
use Slim4\ViteIntegration\Adapter\TwigAdapter;
use Twig\Environment;

$adapter = new TwigAdapter($twig);
```

### PlatesAdapter

Adapter for the Plates template engine.

```php
use Slim4\ViteIntegration\Adapter\PlatesAdapter;
use League\Plates\Engine;

$adapter = new PlatesAdapter($plates);
```

### BladeAdapter

Adapter for the Blade template engine.

```php
use Slim4\ViteIntegration\Adapter\BladeAdapter;
use Illuminate\View\Factory;

$adapter = new BladeAdapter($blade);
```

### VoltAdapter

Adapter for the Volt template engine.

```php
use Slim4\ViteIntegration\Adapter\VoltAdapter;
use Phalcon\Mvc\View\Engine\Volt\Compiler;

$adapter = new VoltAdapter($volt);
```

## Template Engine Middlewares

The package provides middlewares for various template engines.

### TwigViteMiddleware

Middleware for the Twig template engine.

```php
use Slim4\ViteIntegration\Middleware\TwigViteMiddleware;
use Slim\Views\Twig;

$middleware = new TwigViteMiddleware($twig, $viteAssetHelper);
```

### PlatesViteMiddleware

Middleware for the Plates template engine.

```php
use Slim4\ViteIntegration\Middleware\PlatesViteMiddleware;
use League\Plates\Engine;

$middleware = new PlatesViteMiddleware($plates, $viteAssetHelper);
```

### BladeViteMiddleware

Middleware for the Blade template engine.

```php
use Slim4\ViteIntegration\Middleware\BladeViteMiddleware;
use Illuminate\View\Factory;

$middleware = new BladeViteMiddleware($blade, $viteAssetHelper);
```

### VoltViteMiddleware

Middleware for the Volt template engine.

```php
use Slim4\ViteIntegration\Middleware\VoltViteMiddleware;
use Phalcon\Mvc\View\Engine\Volt\Compiler;

$middleware = new VoltViteMiddleware($volt, $viteAssetHelper);
```
