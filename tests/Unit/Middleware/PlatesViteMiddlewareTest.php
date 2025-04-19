<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Tests\Unit\Middleware;

use League\Plates\Engine;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim4\ViteIntegration\Middleware\PlatesViteMiddleware;
use Slim4\ViteIntegration\ViteAssetHelper;

class PlatesViteMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    
    public function testProcess(): void
    {
        $engine = Mockery::mock(Engine::class);
        $engine->shouldReceive('registerFunction')->times(5);
        $engine->shouldReceive('addData')->once();
        
        $viteAssetHelper = Mockery::mock(ViteAssetHelper::class);
        
        $request = Mockery::mock(ServerRequestInterface::class);
        
        $response = Mockery::mock(ResponseInterface::class);
        
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $handler->shouldReceive('handle')->once()->with($request)->andReturn($response);
        
        $middleware = new PlatesViteMiddleware($engine, $viteAssetHelper);
        
        $result = $middleware->process($request, $handler);
        
        $this->assertSame($response, $result);
    }
}
