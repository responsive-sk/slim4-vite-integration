<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration;

class ViteAssetHelper
{
    private string $manifestPath;
    private ?array $manifest = null;
    private bool $isDev;
    private string $devServerUrl;
    private string $publicPath;
    private string $buildDirectory;
    private array $assetDirectories;

    /**
     * Constructor
     *
     * @param string|null $manifestPath Path to the manifest.json file
     * @param bool $isDev Whether to use development mode
     * @param string $devServerUrl URL of the Vite dev server
     * @param string $publicPath Path to the public directory
     * @param string $buildDirectory Directory where Vite builds assets (relative to public path)
     * @param array $assetDirectories Directories where assets are stored
     */
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
    ) {
        $this->manifestPath = $manifestPath;
        $this->isDev = $isDev;
        $this->devServerUrl = $devServerUrl;
        $this->publicPath = $publicPath ?: dirname(__DIR__, 5) . '/public';
        $this->buildDirectory = $buildDirectory;
        $this->assetDirectories = $assetDirectories;
    }

    /**
     * Get the manifest
     *
     * @return array
     * @throws \RuntimeException
     */
    public function getManifest(): array
    {
        if ($this->manifest === null) {
            // Try to find manifest in different locations
            $possiblePaths = [
                $this->manifestPath,
                $this->publicPath . '/' . $this->buildDirectory . '/manifest.json',
                $this->publicPath . '/' . $this->buildDirectory . '/.vite/manifest.json',
            ];

            $manifestFound = false;
            foreach ($possiblePaths as $path) {
                if ($path && file_exists($path)) {
                    $this->manifest = json_decode(file_get_contents($path), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $manifestFound = true;
                        break;
                    }
                }
            }

            if (!$manifestFound) {
                // Return empty manifest in development mode
                if ($this->isDev) {
                    return [];
                }
                throw new \RuntimeException("Vite manifest file not found");
            }
        }

        return $this->manifest;
    }

    /**
     * Generate a script tag for a JavaScript entry point
     *
     * @param string $entrypoint Path to the JavaScript entry point
     * @return string Generated script tag
     */
    public function jsTag(string $entrypoint): string 
    {
        if ($this->isDev) {
            return sprintf(
                '<script type="module" src="%s/%s"></script>',
                $this->devServerUrl,
                $entrypoint
            );
        }

        $path = $this->resolveAsset($entrypoint);
        return sprintf('<script type="module" src="%s"></script>', $path);
    }

    /**
     * Resolve asset path from manifest
     * 
     * @param string $entry Asset entry path
     * @return string Resolved asset path
     */
    private function resolveAsset(string $entry): string
    {
        $manifest = $this->getManifest();
        
        if (isset($manifest[$entry])) {
            return "/{$this->buildDirectory}/" . $manifest[$entry]['file'];
        }

        foreach ($manifest as $key => $value) {
            if (basename($key) === basename($entry)) {
                return "/{$this->buildDirectory}/" . $value['file'];
            }
        }

        return "/{$this->buildDirectory}/assets/" . basename($entry);
    }

    /**
     * Generate a link tag for a CSS entry point
     *
     * @param string $entrypoint
     * @param string $fallbackCss Fallback CSS file if no CSS is found in the manifest
     * @return string
     */
    public function cssTag(string $entrypoint, string $fallbackCss = null): string
    {
        if ($this->isDev) {
            // In dev mode, CSS is injected by Vite via JS
            return '';
        }

        try {
            $manifest = $this->getManifest();
            $entry = $manifest[$entrypoint] ?? null;

            if (!$entry) {
                // Fallback to provided CSS or default
                return $fallbackCss ? 
                    sprintf('<link rel="stylesheet" href="%s">', $fallbackCss) : 
                    '';
            }

            $tags = '';
            if (isset($entry['css']) && is_array($entry['css'])) {
                foreach ($entry['css'] as $cssFile) {
                    $tags .= sprintf('<link rel="stylesheet" href="/%s/%s">', $this->buildDirectory, $cssFile);
                }
            }

            return $tags ?: ($fallbackCss ? sprintf('<link rel="stylesheet" href="%s">', $fallbackCss) : '');
        } catch (\Exception $e) {
            // Fallback to provided CSS or default
            return $fallbackCss ? 
                sprintf('<link rel="stylesheet" href="%s">', $fallbackCss) : 
                '';
        }
    }

    /**
     * Get the path to an asset
     *
     * @param string $path
     * @return string
     */
    public function asset(string $path): string
    {
        if ($this->isDev) {
            return $this->devServerUrl . '/' . $path;
        }

        try {
            $manifest = $this->getManifest();
            $file = $manifest[$path]['file'] ?? null;

            if (!$file) {
                // Fallback to direct path
                return '/' . $path;
            }

            return '/' . $this->buildDirectory . '/' . $file;
        } catch (\Exception $e) {
            // Fallback to direct path
            return '/' . $path;
        }
    }

    /**
     * Get the path to an image
     *
     * @param string $path
     * @param string $resourcePath Path to the resource in the source directory
     * @param string $placeholder Placeholder image to use if the image is not found
     * @return string
     */
    public function image(
        string $path, 
        string $resourcePath = 'resources/assets/web/images', 
        string $placeholder = 'placeholder.jpg'
    ): string {
        if ($this->isDev) {
            // In development mode, use the dev server
            return $this->devServerUrl . '/' . $resourcePath . '/' . $path;
        }

        // In production mode, check if the image is in the manifest
        try {
            $manifest = $this->getManifest();
            $key = $resourcePath . '/' . $path;

            if (isset($manifest[$key]) && isset($manifest[$key]['file'])) {
                return '/' . $this->buildDirectory . '/' . $manifest[$key]['file'];
            }
        } catch (\Exception $e) {
            // Ignore
        }

        // If the image is not in the manifest, check if it exists in the public directory
        $imagePath = '/' . $this->assetDirectories['images'] . '/' . $path;
        
        if (file_exists($this->publicPath . $imagePath)) {
            return $imagePath;
        }

        // If the file doesn't exist, return a placeholder image
        return '/' . $this->assetDirectories['images'] . '/' . $placeholder;
    }

    /**
     * Get the path to a font
     *
     * @param string $path
     * @return string
     */
    public function font(string $path): string
    {
        // For fonts, we just return the path as is
        // You can add processing here if needed
        return '/' . $this->assetDirectories['fonts'] . '/' . $path;
    }
}
