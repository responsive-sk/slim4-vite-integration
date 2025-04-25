<?php

declare(strict_types=1);

namespace Slim4\Vite;

use Slim4\Root\PathsInterface;
use Slim4\Vite\VitePathsInterface;

class ViteService implements ViteServiceInterface
{
    private array $manifest = [];
    private string $manifestPath;
    private string $publicPath;
    private string $buildDirectory;
    private bool $isDev;
    private string $devServerUrl;
    private array $assetDirectories;

    /**
     * Constructor
     *
     * @param PathsInterface|VitePathsInterface $paths Paths service
     * @param string $buildDirectory Build directory relative to public path (default: 'build')
     * @param bool $isDev Whether to use dev server (default: false)
     * @param string $devServerUrl Dev server URL (default: 'http://localhost:5173')
     * @param array $assetDirectories Directories where assets are stored
     */
    public function __construct(
        PathsInterface|VitePathsInterface $paths,
        string $buildDirectory = 'build',
        bool $isDev = false,
        string $devServerUrl = 'http://localhost:5173',
        array $assetDirectories = [
            'images' => 'assets/images',
            'fonts' => 'assets/fonts',
        ]
    ) {
        $this->publicPath = $paths->getPublicPath();
        $this->buildDirectory = $buildDirectory;
        $this->isDev = $isDev;
        $this->devServerUrl = $devServerUrl;
        $this->assetDirectories = $assetDirectories;

        // If we have a VitePathsInterface, use it to get the manifest path
        if ($paths instanceof VitePathsInterface) {
            $this->manifestPath = $paths->getViteManifestPath();
            if (file_exists($this->manifestPath)) {
                $manifestContent = file_get_contents($this->manifestPath);
                if ($manifestContent !== false) {
                    $decodedManifest = json_decode($manifestContent, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $this->manifest = $decodedManifest;
                    }
                }
            }
        } else {
            // Try to find manifest in different locations
            $possiblePaths = [
                $this->publicPath . '/' . $buildDirectory . '/manifest.json',
                $this->publicPath . '/' . $buildDirectory . '/.vite/manifest.json',
            ];

            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $manifestContent = file_get_contents($path);
                    if ($manifestContent !== false) {
                        $decodedManifest = json_decode($manifestContent, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $this->manifest = $decodedManifest;
                            $this->manifestPath = $path;
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the path to an asset from the manifest
     *
     * @param string $entry Entry point name
     * @return string Asset path
     */
    public function asset(string $entry): string
    {
        if ($this->isDev) {
            return $this->devServerUrl . '/' . $entry;
        }

        // Try to find the entry directly
        if (isset($this->manifest[$entry]['file'])) {
            return '/' . $this->buildDirectory . '/' . $this->manifest[$entry]['file'];
        }

        // Try to find by basename
        foreach ($this->manifest as $key => $value) {
            if (basename($key) === basename($entry) && isset($value['file'])) {
                return '/' . $this->buildDirectory . '/' . $value['file'];
            }
        }

        // Default fallback
        return '/' . $this->buildDirectory . '/assets/' . basename($entry);
    }

    /**
     * Generate link tags for CSS files from an entry
     *
     * @param string $entry Entry point name
     * @return string HTML link tags
     */
    public function entryLinkTags(string $entry): string
    {
        if ($this->isDev) {
            // In dev mode, CSS is injected by Vite via JS
            return '';
        }

        // Try to find the entry directly
        if (isset($this->manifest[$entry])) {
            $cssFiles = $this->manifest[$entry]['css'] ?? [];
            $links = '';

            foreach ($cssFiles as $file) {
                $links .= sprintf(
                    '<link rel="stylesheet" href="/%s/%s">',
                    $this->buildDirectory,
                    htmlspecialchars($file, ENT_QUOTES, 'UTF-8')
                );
            }

            return $links;
        }

        // Try to find by basename
        foreach ($this->manifest as $key => $value) {
            if (basename($key, '.js') === $entry && isset($value['css'])) {
                $cssFiles = $value['css'];
                $links = '';

                foreach ($cssFiles as $file) {
                    $links .= sprintf(
                        '<link rel="stylesheet" href="/%s/%s">',
                        $this->buildDirectory,
                        htmlspecialchars($file, ENT_QUOTES, 'UTF-8')
                    );
                }

                return $links;
            }
        }

        // Fallback: find any CSS file with the entry name in it
        foreach ($this->manifest as $key => $value) {
            if (strpos($key, $entry) !== false && isset($value['file']) && strpos($value['file'], '.css') !== false) {
                return sprintf(
                    '<link rel="stylesheet" href="/%s/%s">',
                    $this->buildDirectory,
                    htmlspecialchars($value['file'], ENT_QUOTES, 'UTF-8')
                );
            }
        }

        return '';
    }

    /**
     * Generate script tags for JS files from an entry
     *
     * @param string $entry Entry point name
     * @return string HTML script tags
     */
    public function entryScriptTags(string $entry): string
    {
        if ($this->isDev) {
            return sprintf(
                '<script type="module" src="%s/%s"></script>',
                $this->devServerUrl,
                $entry
            );
        }

        // Try to find the entry directly
        if (isset($this->manifest[$entry])) {
            $file = $this->manifest[$entry]['file'];
            $imports = $this->manifest[$entry]['imports'] ?? [];

            $scripts = '';

            // Add imported chunks first
            foreach ($imports as $import) {
                $scripts .= sprintf(
                    '<script type="module" src="/%s/%s"></script>',
                    $this->buildDirectory,
                    htmlspecialchars($this->manifest[$import]['file'], ENT_QUOTES, 'UTF-8')
                );
            }

            // Add main entry file
            $scripts .= sprintf(
                '<script type="module" src="/%s/%s"></script>',
                $this->buildDirectory,
                htmlspecialchars($file, ENT_QUOTES, 'UTF-8')
            );

            return $scripts;
        }

        // Try to find by basename
        foreach ($this->manifest as $key => $value) {
            if (basename($key, '.js') === $entry) {
                $file = $value['file'];
                $imports = $value['imports'] ?? [];

                $scripts = '';

                // Add imported chunks first
                foreach ($imports as $import) {
                    $scripts .= sprintf(
                        '<script type="module" src="/%s/%s"></script>',
                        $this->buildDirectory,
                        htmlspecialchars($this->manifest[$import]['file'], ENT_QUOTES, 'UTF-8')
                    );
                }

                // Add main entry file
                $scripts .= sprintf(
                    '<script type="module" src="/%s/%s"></script>',
                    $this->buildDirectory,
                    htmlspecialchars($file, ENT_QUOTES, 'UTF-8')
                );

                return $scripts;
            }
        }

        // Fallback: find any JS file with the entry name in it
        foreach ($this->manifest as $key => $value) {
            if (strpos($key, $entry) !== false && isset($value['file']) && strpos($value['file'], '.js') !== false) {
                return sprintf(
                    '<script type="module" src="/%s/%s"></script>',
                    $this->buildDirectory,
                    htmlspecialchars($value['file'], ENT_QUOTES, 'UTF-8')
                );
            }
        }

        // Last resort fallback - try to find the entry by name
        foreach ($this->manifest as $key => $value) {
            if (isset($value['name']) && $value['name'] === $entry && isset($value['file'])) {
                return sprintf(
                    '<script type="module" src="/%s/%s"></script>',
                    $this->buildDirectory,
                    htmlspecialchars($value['file'], ENT_QUOTES, 'UTF-8')
                );
            }
        }

        // If all else fails, use a generic path
        return sprintf(
            '<script type="module" src="/%s/%s.js"></script>',
            $this->buildDirectory,
            $entry
        );
    }

    /**
     * Get the path to an image
     *
     * @param string $path Image path
     * @param string $resourcePath Path to the resource in the source directory
     * @param string $placeholder Placeholder image to use if the image is not found
     * @return string Image URL
     */
    public function image(
        string $path,
        string $resourcePath = 'resources/images',
        string $placeholder = 'placeholder.jpg'
    ): string {
        if ($this->isDev) {
            return $this->devServerUrl . '/' . $resourcePath . '/' . $path;
        }

        // Try to find the image in the manifest
        $imageKey = $resourcePath . '/' . $path;
        if (isset($this->manifest[$imageKey])) {
            return '/' . $this->buildDirectory . '/' . $this->manifest[$imageKey]['file'];
        }

        // Check if the image exists in the public directory
        $imagePath = '/' . $this->assetDirectories['images'] . '/' . $path;
        if (file_exists($this->publicPath . $imagePath)) {
            return $imagePath;
        }

        // Fallback to placeholder
        return '/' . $this->assetDirectories['images'] . '/' . $placeholder;
    }

    /**
     * Get the path to a font
     *
     * @param string $path Font path
     * @return string Font URL
     */
    public function font(string $path): string
    {
        if ($this->isDev) {
            return $this->devServerUrl . '/resources/fonts/' . $path;
        }

        // Try to find the font in the manifest
        $fontKey = 'resources/fonts/' . $path;
        if (isset($this->manifest[$fontKey])) {
            return '/' . $this->buildDirectory . '/' . $this->manifest[$fontKey]['file'];
        }

        // Fallback to public path
        return '/' . $this->assetDirectories['fonts'] . '/' . $path;
    }

    /**
     * Get the raw manifest data
     *
     * @return array Manifest data
     */
    public function getManifest(): array
    {
        return $this->manifest;
    }

    /**
     * Get the build path
     *
     * @return string The build path
     */
    public function getBuildPath(): string
    {
        return $this->publicPath . '/' . $this->buildDirectory;
    }

    /**
     * Get the build assets path
     *
     * @return string The build assets path
     */
    public function getBuildAssetsPath(): string
    {
        return $this->getBuildPath() . '/assets';
    }

    /**
     * Get the Vite manifest path
     *
     * @return string The Vite manifest path
     */
    public function getViteManifestPath(): string
    {
        return $this->manifestPath ?? $this->getBuildPath() . '/.vite/manifest.json';
    }

    /**
     * Load the manifest file
     *
     * @return array The manifest data
     */
    private function loadManifest(): array
    {
        if (!empty($this->manifest)) {
            return $this->manifest;
        }

        if (isset($this->manifestPath) && file_exists($this->manifestPath)) {
            $manifestContent = file_get_contents($this->manifestPath);
            if ($manifestContent !== false) {
                $decodedManifest = json_decode($manifestContent, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decodedManifest;
                }
            }
        }

        return [];
    }
}
