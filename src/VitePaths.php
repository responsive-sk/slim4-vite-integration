<?php

declare(strict_types=1);

namespace Slim4\Vite;

use Slim4\Root\PathsInterface;

/**
 * Decorator for Paths with Vite-specific paths
 */
class VitePaths implements VitePathsInterface
{
    /**
     * @var PathsInterface The decorated paths instance
     */
    private PathsInterface $paths;

    /**
     * @var string The build directory name
     */
    private string $buildDirectory;

    /**
     * Constructor
     *
     * @param PathsInterface $paths The paths instance to decorate
     * @param string $buildDirectory The build directory name
     */
    public function __construct(PathsInterface $paths, string $buildDirectory = 'build')
    {
        $this->paths = $paths;
        $this->buildDirectory = $buildDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootPath(): string
    {
        return $this->paths->getRootPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigPath(): string
    {
        return $this->paths->getConfigPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesPath(): string
    {
        return $this->paths->getResourcesPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewsPath(): string
    {
        return $this->paths->getViewsPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetsPath(): string
    {
        return $this->paths->getAssetsPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getCachePath(): string
    {
        return $this->paths->getCachePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogsPath(): string
    {
        return $this->paths->getLogsPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicPath(): string
    {
        return $this->paths->getPublicPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabasePath(): string
    {
        return $this->paths->getDatabasePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationsPath(): string
    {
        return $this->paths->getMigrationsPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getStoragePath(): string
    {
        return $this->paths->getStoragePath();
    }

    /**
     * {@inheritdoc}
     */
    public function getTestsPath(): string
    {
        return $this->paths->getTestsPath();
    }

    /**
     * {@inheritdoc}
     */
    public function path(string $path): string
    {
        return $this->paths->path($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths(): array
    {
        $paths = $this->paths->getPaths();

        // Add Vite-specific paths
        $paths['build'] = $this->getBuildPath($this->buildDirectory);
        $paths['build_assets'] = $this->getBuildAssetsPath($this->buildDirectory);
        $paths['vite_manifest'] = $this->getViteManifestPath($this->buildDirectory);

        return $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuildPath(string $buildDirectory = 'build'): string
    {
        // Use the stored build directory if no parameter is provided
        $directory = $buildDirectory === 'build' ? $this->buildDirectory : $buildDirectory;
        return $this->getPublicPath() . '/' . $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuildAssetsPath(string $buildDirectory = 'build'): string
    {
        // Use the stored build directory if no parameter is provided
        $directory = $buildDirectory === 'build' ? $this->buildDirectory : $buildDirectory;
        return $this->getBuildPath($directory) . '/assets';
    }

    /**
     * {@inheritdoc}
     */
    public function getViteManifestPath(string $buildDirectory = 'build'): string
    {
        // Use the stored build directory if no parameter is provided
        $directory = $buildDirectory === 'build' ? $this->buildDirectory : $buildDirectory;

        $possiblePaths = [
            $this->getBuildPath($directory) . '/manifest.json',
            $this->getBuildPath($directory) . '/.vite/manifest.json',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return $this->getBuildPath($directory) . '/.vite/manifest.json';
    }
}
