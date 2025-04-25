<?php

declare(strict_types=1);

namespace Slim4\Vite;

use Slim4\Root\PathsInterface;

/**
 * Extended Paths interface with Vite-specific paths
 */
interface VitePathsInterface extends PathsInterface
{
    /**
     * Get the build path
     *
     * @param string $buildDirectory Build directory name (default: 'build')
     * @return string The build path
     */
    public function getBuildPath(string $buildDirectory = 'build'): string;

    /**
     * Get the build assets path
     *
     * @param string $buildDirectory Build directory name (default: 'build')
     * @return string The build assets path
     */
    public function getBuildAssetsPath(string $buildDirectory = 'build'): string;

    /**
     * Get the Vite manifest path
     *
     * @param string $buildDirectory Build directory name (default: 'build')
     * @return string The Vite manifest path
     */
    public function getViteManifestPath(string $buildDirectory = 'build'): string;
}
