<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Tests\Unit\Adapter;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Slim4\ViteIntegration\Adapter\TwigAdapter;
use Slim4\ViteIntegration\ViteAssetHelper;
use Twig\Environment;
use Twig\TwigFunction;

class TwigAdapterTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    
    public function testGetEngine(): void
    {
        $engine = Mockery::mock(Environment::class);
        $adapter = new TwigAdapter($engine);
        
        $this->assertSame($engine, $adapter->getEngine());
    }
    
    public function testRegisterViteAssetHelper(): void
    {
        $engine = Mockery::mock(Environment::class);
        $engine->shouldReceive('addGlobal')->once()->with('vite', Mockery::type(ViteAssetHelper::class));
        $engine->shouldReceive('addFunction')->times(5)->with(Mockery::type(TwigFunction::class));
        
        $viteAssetHelper = Mockery::mock(ViteAssetHelper::class);
        
        $adapter = new TwigAdapter($engine);
        $adapter->registerViteAssetHelper($viteAssetHelper);
    }
    
    public function testRegisterFunctionsWithoutViteAssetHelper(): void
    {
        $engine = Mockery::mock(Environment::class);
        $engine->shouldNotReceive('addGlobal');
        $engine->shouldNotReceive('addFunction');
        
        $adapter = new TwigAdapter($engine);
        
        // Call registerFunctions through reflection
        $reflection = new \ReflectionClass($adapter);
        $method = $reflection->getMethod('registerFunctions');
        $method->setAccessible(true);
        $method->invoke($adapter);
    }
}
