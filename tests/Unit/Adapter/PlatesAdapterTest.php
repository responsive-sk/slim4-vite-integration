<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Tests\Unit\Adapter;

use League\Plates\Engine;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Slim4\ViteIntegration\Adapter\PlatesAdapter;
use Slim4\ViteIntegration\ViteAssetHelper;

class PlatesAdapterTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    
    public function testGetEngine(): void
    {
        $engine = Mockery::mock(Engine::class);
        $adapter = new PlatesAdapter($engine);
        
        $this->assertSame($engine, $adapter->getEngine());
    }
    
    public function testRegisterViteAssetHelper(): void
    {
        $engine = Mockery::mock(Engine::class);
        $engine->shouldReceive('registerFunction')->times(5);
        $engine->shouldReceive('addData')->once()->with(['vite' => Mockery::type(ViteAssetHelper::class)]);
        
        $viteAssetHelper = Mockery::mock(ViteAssetHelper::class);
        
        $adapter = new PlatesAdapter($engine);
        $adapter->registerViteAssetHelper($viteAssetHelper);
    }
    
    public function testRegisterFunctionsWithoutViteAssetHelper(): void
    {
        $engine = Mockery::mock(Engine::class);
        $engine->shouldNotReceive('registerFunction');
        $engine->shouldNotReceive('addData');
        
        $adapter = new PlatesAdapter($engine);
        
        // Call registerFunctions through reflection
        $reflection = new \ReflectionClass($adapter);
        $method = $reflection->getMethod('registerFunctions');
        $method->setAccessible(true);
        $method->invoke($adapter);
    }
}
