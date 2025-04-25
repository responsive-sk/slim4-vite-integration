<?php

declare(strict_types=1);

namespace Slim4\Vite\Tests\Unit;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Slim4\Root\PathsInterface;
use Slim4\Vite\VitePaths;

class VitePathsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $pathsMock;
    private $vitePaths;
    private $publicPath;

    protected function setUp(): void
    {
        $this->pathsMock = Mockery::mock(PathsInterface::class);
        $this->publicPath = __DIR__ . '/../../fixtures';

        // Create fixtures directory if it doesn't exist
        if (!is_dir($this->publicPath)) {
            mkdir($this->publicPath, 0777, true);
        }

        // Create build directory if it doesn't exist
        if (!is_dir($this->publicPath . '/build')) {
            mkdir($this->publicPath . '/build', 0777, true);
        }

        // Create .vite directory if it doesn't exist
        if (!is_dir($this->publicPath . '/build/.vite')) {
            mkdir($this->publicPath . '/build/.vite', 0777, true);
        }

        // Create a sample manifest.json file for testing
        file_put_contents(
            $this->publicPath . '/build/.vite/manifest.json',
            json_encode([])
        );

        // Mock the paths service
        $this->pathsMock->shouldReceive('getPublicPath')
            ->andReturn($this->publicPath);

        // Create VitePaths instance
        $this->vitePaths = new VitePaths($this->pathsMock, 'build');
    }

    protected function tearDown(): void
    {
        // Clean up the test manifest file
        if (file_exists($this->publicPath . '/build/.vite/manifest.json')) {
            unlink($this->publicPath . '/build/.vite/manifest.json');
        }

        // Remove the .vite directory if it exists
        if (is_dir($this->publicPath . '/build/.vite')) {
            rmdir($this->publicPath . '/build/.vite');
        }

        // Remove the build directory if it exists
        if (is_dir($this->publicPath . '/build')) {
            rmdir($this->publicPath . '/build');
        }

        // Remove the fixtures directory if it exists
        if (is_dir($this->publicPath)) {
            rmdir($this->publicPath);
        }

        Mockery::close();
    }

    public function testGetBuildPath(): void
    {
        $result = $this->vitePaths->getBuildPath();
        
        $this->assertEquals($this->publicPath . '/build', $result);
    }

    public function testGetBuildAssetsPath(): void
    {
        $result = $this->vitePaths->getBuildAssetsPath();
        
        $this->assertEquals($this->publicPath . '/build/assets', $result);
    }

    public function testGetViteManifestPath(): void
    {
        $result = $this->vitePaths->getViteManifestPath();
        
        $this->assertEquals($this->publicPath . '/build/.vite/manifest.json', $result);
    }

    public function testGetPaths(): void
    {
        // Mock the original paths
        $this->pathsMock->shouldReceive('getPaths')
            ->andReturn([
                'root' => '/var/www',
                'public' => $this->publicPath,
            ]);

        $result = $this->vitePaths->getPaths();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('build', $result);
        $this->assertArrayHasKey('build_assets', $result);
        $this->assertArrayHasKey('vite_manifest', $result);
        $this->assertEquals($this->publicPath . '/build', $result['build']);
        $this->assertEquals($this->publicPath . '/build/assets', $result['build_assets']);
        $this->assertEquals($this->publicPath . '/build/.vite/manifest.json', $result['vite_manifest']);
    }

    public function testDelegatedMethods(): void
    {
        // Test that all methods from PathsInterface are delegated to the decorated instance
        $this->pathsMock->shouldReceive('getRootPath')->once()->andReturn('/var/www');
        $this->pathsMock->shouldReceive('getConfigPath')->once()->andReturn('/var/www/config');
        $this->pathsMock->shouldReceive('getResourcesPath')->once()->andReturn('/var/www/resources');
        $this->pathsMock->shouldReceive('getViewsPath')->once()->andReturn('/var/www/resources/views');
        $this->pathsMock->shouldReceive('getAssetsPath')->once()->andReturn('/var/www/resources/assets');
        $this->pathsMock->shouldReceive('getCachePath')->once()->andReturn('/var/www/var/cache');
        $this->pathsMock->shouldReceive('getLogsPath')->once()->andReturn('/var/www/var/logs');
        $this->pathsMock->shouldReceive('getDatabasePath')->once()->andReturn('/var/www/database');
        $this->pathsMock->shouldReceive('getMigrationsPath')->once()->andReturn('/var/www/database/migrations');
        $this->pathsMock->shouldReceive('getStoragePath')->once()->andReturn('/var/www/storage');
        $this->pathsMock->shouldReceive('getTestsPath')->once()->andReturn('/var/www/tests');
        $this->pathsMock->shouldReceive('path')->once()->with('foo/bar')->andReturn('/var/www/foo/bar');

        $this->assertEquals('/var/www', $this->vitePaths->getRootPath());
        $this->assertEquals('/var/www/config', $this->vitePaths->getConfigPath());
        $this->assertEquals('/var/www/resources', $this->vitePaths->getResourcesPath());
        $this->assertEquals('/var/www/resources/views', $this->vitePaths->getViewsPath());
        $this->assertEquals('/var/www/resources/assets', $this->vitePaths->getAssetsPath());
        $this->assertEquals('/var/www/var/cache', $this->vitePaths->getCachePath());
        $this->assertEquals('/var/www/var/logs', $this->vitePaths->getLogsPath());
        $this->assertEquals('/var/www/database', $this->vitePaths->getDatabasePath());
        $this->assertEquals('/var/www/database/migrations', $this->vitePaths->getMigrationsPath());
        $this->assertEquals('/var/www/storage', $this->vitePaths->getStoragePath());
        $this->assertEquals('/var/www/tests', $this->vitePaths->getTestsPath());
        $this->assertEquals('/var/www/foo/bar', $this->vitePaths->path('foo/bar'));
    }
}
