<?php

declare(strict_types=1);

namespace Slim4\Vite;

/**
 * Interface for Vite integration service
 */
interface ViteServiceInterface
{
    /**
     * Get the path to an asset from the manifest
     *
     * @param string $entry Entry point name
     * @return string Asset path
     */
    public function asset(string $entry): string;

    /**
     * Generate link tags for CSS files from an entry
     *
     * @param string $entry Entry point name
     * @return string HTML link tags
     */
    public function entryLinkTags(string $entry): string;

    /**
     * Generate script tags for JS files from an entry
     *
     * @param string $entry Entry point name
     * @return string HTML script tags
     */
    public function entryScriptTags(string $entry): string;

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
    ): string;

    /**
     * Get the path to a font
     *
     * @param string $path Font path
     * @return string Font URL
     */
    public function font(string $path): string;

    /**
     * Get the raw manifest data
     *
     * @return array Manifest data
     */
    public function getManifest(): array;

    /**
     * Get the build path
     *
     * @return string The build path
     */
    public function getBuildPath(): string;

    /**
     * Get the build assets path
     *
     * @return string The build assets path
     */
    public function getBuildAssetsPath(): string;

    /**
     * Get the Vite manifest path
     *
     * @return string The Vite manifest path
     */
    public function getViteManifestPath(): string;
}
