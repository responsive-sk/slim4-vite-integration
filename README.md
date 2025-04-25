# Slim4 Vite Integration

A simple and lightweight integration of Vite with Slim 4 framework.

## Installation

```bash
composer require responsive-sk/slim4-vite-integration
```

## Usage

### Register the service in your container

```php
use Slim4\Vite\ViteService;
use Slim4\Vite\ViteServiceInterface;
use Slim4\Vite\TwigExtension;
use Slim4\Vite\VitePaths;
use Slim4\Vite\VitePathsInterface;
use Slim4\Root\PathsInterface;

// In your container definitions
return [
    // Register VitePaths
    VitePathsInterface::class => function ($container) {
        $paths = $container->get(PathsInterface::class);
        return new VitePaths($paths, 'build');
    },

    // Register ViteService
    ViteServiceInterface::class => ViteService::class,

    ViteService::class => function ($container) {
        $paths = $container->get(VitePathsInterface::class);
        $isDev = $_ENV['APP_ENV'] === 'development';

        // Basic configuration
        return new ViteService($paths, 'build', $isDev);

        // Advanced configuration with custom asset directories
        /*
        return new ViteService(
            $paths,
            'build',
            $isDev,
            'http://localhost:5173',
            [
                'images' => 'assets/images',
                'fonts' => 'assets/fonts',
            ]
        );
        */
    },

    // Register Twig extension
    Twig::class => function ($container) {
        $paths = $container->get(PathsInterface::class);
        $twig = new Twig(
            $paths->getViewsPath(),
            [
                'cache' => $paths->getCachePath() . '/twig',
                'debug' => true,
                'auto_reload' => true,
            ]
        );

        // Add Vite extension
        $viteService = $container->get(ViteServiceInterface::class);
        $twig->addExtension(new TwigExtension($viteService));

        return $twig;
    },
];
```

### Use in Twig templates

```twig
{# Load CSS for an entry point #}
{{ vite_entry_link_tags('css') }}

{# Load JavaScript for an entry point #}
{{ vite_entry_script_tags('app') }}

{# Get URL for an asset #}
<img src="{{ vite_asset('resources/images/logo.png') }}" alt="Logo">

{# Get URL for an image #}
<img src="{{ vite_image('logo.png') }}" alt="Logo">

{# Get URL for an image with custom resource path and placeholder #}
<img src="{{ vite_image('hero.jpg', 'resources/assets/images', 'default.jpg') }}" alt="Hero">

{# Get URL for a font #}
<style>
    @font-face {
        font-family: 'CustomFont';
        src: url('{{ vite_font('custom.woff2') }}') format('woff2');
    }
</style>
```

## Configuration

The `ViteService` constructor accepts the following parameters:

- `PathsInterface $paths`: The paths service from slim4/root
- `string $buildDirectory = 'build'`: The build directory relative to public path
- `bool $isDev = false`: Whether to use dev server
- `string $devServerUrl = 'http://localhost:5173'`: Dev server URL
- `array $assetDirectories = ['images' => 'assets/images', 'fonts' => 'assets/fonts']`: Directories where assets are stored

## Available Methods

### ViteServiceInterface

- `asset(string $entry): string` - Get the path to an asset
- `entryLinkTags(string $entry): string` - Generate link tags for CSS files from an entry
- `entryScriptTags(string $entry): string` - Generate script tags for JS files from an entry
- `image(string $path, string $resourcePath = 'resources/images', string $placeholder = 'placeholder.jpg'): string` - Get the path to an image
- `font(string $path): string` - Get the path to a font
- `getManifest(): array` - Get the raw manifest data
- `getBuildPath(): string` - Get the build path
- `getBuildAssetsPath(): string` - Get the build assets path
- `getViteManifestPath(): string` - Get the Vite manifest path

### VitePathsInterface

- `getBuildPath(): string` - Get the build path
- `getBuildAssetsPath(): string` - Get the build assets path
- `getViteManifestPath(): string` - Get the Vite manifest path

### TwigExtension

- `vite_asset(string $entry): string` - Get the path to an asset
- `vite_entry_link_tags(string $entry): string` - Generate link tags for CSS files from an entry
- `vite_entry_script_tags(string $entry): string` - Generate script tags for JS files from an entry
- `vite_image(string $path, string $resourcePath = 'resources/images', string $placeholder = 'placeholder.jpg'): string` - Get the path to an image
- `vite_font(string $path): string` - Get the path to a font
- `vite_css(string $entry): string` - Alias for vite_entry_link_tags (legacy)
- `vite_js(string $entry): string` - Alias for vite_entry_script_tags (legacy)

## License

MIT
