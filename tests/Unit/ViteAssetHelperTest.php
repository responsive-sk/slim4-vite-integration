<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Slim4\ViteIntegration\ViteAssetHelper;

class ViteAssetHelperTest extends TestCase
{
    private string $fixturesDir;
    
    protected function setUp(): void
    {
        $this->fixturesDir = dirname(__DIR__) . '/Fixtures';
        
        // Create fixtures directory if it doesn't exist
        if (!is_dir($this->fixturesDir)) {
            mkdir($this->fixturesDir, 0777, true);
        }
        
        // Create public directory if it doesn't exist
        if (!is_dir($this->fixturesDir . '/public')) {
            mkdir($this->fixturesDir . '/public', 0777, true);
        }
        
        // Create build directory if it doesn't exist
        if (!is_dir($this->fixturesDir . '/public/build')) {
            mkdir($this->fixturesDir . '/public/build', 0777, true);
        }
        
        // Create assets directories if they don't exist
        if (!is_dir($this->fixturesDir . '/public/assets/images')) {
            mkdir($this->fixturesDir . '/public/assets/images', 0777, true);
        }
        
        if (!is_dir($this->fixturesDir . '/public/assets/fonts')) {
            mkdir($this->fixturesDir . '/public/assets/fonts', 0777, true);
        }
    }
    
    protected function tearDown(): void
    {
        // Remove manifest file if it exists
        if (file_exists($this->fixturesDir . '/public/build/manifest.json')) {
            unlink($this->fixturesDir . '/public/build/manifest.json');
        }
        
        // Remove .vite directory if it exists
        if (is_dir($this->fixturesDir . '/public/build/.vite')) {
            $this->removeDirectory($this->fixturesDir . '/public/build/.vite');
        }
    }
    
    private function removeDirectory(string $dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->removeDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
    
    public function testJsTagInDevMode(): void
    {
        $helper = new ViteAssetHelper(
            null,
            true,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $tag = $helper->jsTag('resources/assets/js/app.js');
        
        $this->assertEquals(
            '<script type="module" src="http://localhost:5173/resources/assets/js/app.js"></script>',
            $tag
        );
    }
    
    public function testJsTagInProdMode(): void
    {
        // Create manifest file
        $manifest = [
            'resources/assets/js/app.js' => [
                'file' => 'assets/app.1234.js',
            ],
        ];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $tag = $helper->jsTag('resources/assets/js/app.js');
        
        $this->assertEquals(
            '<script type="module" src="/build/assets/app.1234.js"></script>',
            $tag
        );
    }
    
    public function testJsTagInProdModeWithMissingEntry(): void
    {
        // Create manifest file
        $manifest = [];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $tag = $helper->jsTag('resources/assets/js/app.js');
        
        $this->assertEquals(
            '<script type="module" src="/build/assets/app.js"></script>',
            $tag
        );
    }
    
    public function testCssTagInDevMode(): void
    {
        $helper = new ViteAssetHelper(
            null,
            true,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $tag = $helper->cssTag('resources/assets/js/app.js');
        
        $this->assertEquals('', $tag);
    }
    
    public function testCssTagInProdMode(): void
    {
        // Create manifest file
        $manifest = [
            'resources/assets/js/app.js' => [
                'file' => 'assets/app.1234.js',
                'css' => ['assets/app.1234.css'],
            ],
        ];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $tag = $helper->cssTag('resources/assets/js/app.js');
        
        $this->assertEquals(
            '<link rel="stylesheet" href="/build/assets/app.1234.css">',
            $tag
        );
    }
    
    public function testCssTagInProdModeWithMultipleCssFiles(): void
    {
        // Create manifest file
        $manifest = [
            'resources/assets/js/app.js' => [
                'file' => 'assets/app.1234.js',
                'css' => ['assets/app.1234.css', 'assets/vendor.5678.css'],
            ],
        ];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $tag = $helper->cssTag('resources/assets/js/app.js');
        
        $this->assertEquals(
            '<link rel="stylesheet" href="/build/assets/app.1234.css"><link rel="stylesheet" href="/build/assets/vendor.5678.css">',
            $tag
        );
    }
    
    public function testCssTagInProdModeWithMissingEntry(): void
    {
        // Create manifest file
        $manifest = [];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $tag = $helper->cssTag('resources/assets/js/app.js');
        
        $this->assertEquals('', $tag);
    }
    
    public function testCssTagInProdModeWithFallback(): void
    {
        // Create manifest file
        $manifest = [];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $tag = $helper->cssTag('resources/assets/js/app.js', '/assets/css/app.css');
        
        $this->assertEquals(
            '<link rel="stylesheet" href="/assets/css/app.css">',
            $tag
        );
    }
    
    public function testAssetInDevMode(): void
    {
        $helper = new ViteAssetHelper(
            null,
            true,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->asset('resources/assets/images/logo.png');
        
        $this->assertEquals(
            'http://localhost:5173/resources/assets/images/logo.png',
            $url
        );
    }
    
    public function testAssetInProdMode(): void
    {
        // Create manifest file
        $manifest = [
            'resources/assets/images/logo.png' => [
                'file' => 'assets/logo.1234.png',
            ],
        ];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->asset('resources/assets/images/logo.png');
        
        $this->assertEquals(
            '/build/assets/logo.1234.png',
            $url
        );
    }
    
    public function testAssetInProdModeWithMissingEntry(): void
    {
        // Create manifest file
        $manifest = [];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->asset('resources/assets/images/logo.png');
        
        $this->assertEquals(
            '/resources/assets/images/logo.png',
            $url
        );
    }
    
    public function testImageInDevMode(): void
    {
        $helper = new ViteAssetHelper(
            null,
            true,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->image('logo.png');
        
        $this->assertEquals(
            'http://localhost:5173/resources/assets/web/images/logo.png',
            $url
        );
    }
    
    public function testImageInProdMode(): void
    {
        // Create manifest file
        $manifest = [
            'resources/assets/web/images/logo.png' => [
                'file' => 'assets/logo.1234.png',
            ],
        ];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        // Create image file
        file_put_contents(
            $this->fixturesDir . '/public/assets/images/logo.png',
            'fake image content'
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->image('logo.png');
        
        $this->assertEquals(
            '/build/assets/logo.1234.png',
            $url
        );
    }
    
    public function testImageInProdModeWithMissingEntryButExistingFile(): void
    {
        // Create manifest file
        $manifest = [];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        // Create image file
        file_put_contents(
            $this->fixturesDir . '/public/assets/images/logo.png',
            'fake image content'
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->image('logo.png');
        
        $this->assertEquals(
            '/assets/images/logo.png',
            $url
        );
    }
    
    public function testImageInProdModeWithMissingEntryAndMissingFile(): void
    {
        // Create manifest file
        $manifest = [];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
        
        // Create placeholder image file
        file_put_contents(
            $this->fixturesDir . '/public/assets/images/placeholder.jpg',
            'fake placeholder image content'
        );
        
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->image('non-existent.png');
        
        $this->assertEquals(
            '/assets/images/placeholder.jpg',
            $url
        );
    }
    
    public function testFontInDevMode(): void
    {
        $helper = new ViteAssetHelper(
            null,
            true,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->font('myfont.woff2');
        
        $this->assertEquals(
            '/assets/fonts/myfont.woff2',
            $url
        );
    }
    
    public function testFontInProdMode(): void
    {
        $helper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        $url = $helper->font('myfont.woff2');
        
        $this->assertEquals(
            '/assets/fonts/myfont.woff2',
            $url
        );
    }
}
