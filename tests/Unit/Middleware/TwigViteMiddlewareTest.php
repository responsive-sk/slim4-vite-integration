<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Tests\Unit\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;
use Slim4\ViteIntegration\Adapter\TwigAdapter;
use Slim4\ViteIntegration\Middleware\TwigViteMiddleware;
use Slim4\ViteIntegration\ViteAssetHelper;
use Twig\Environment;

class TwigViteMiddlewareTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    
    public function testProcess(): void
    {
        $environment = Mockery::mock(Environment::class);
        $environment->shouldReceive('addGlobal')->once();
        $environment->shouldReceive('addFunction')->times(5);
        
        $twig = Mockery::mock(Twig::class);
        $twig->shouldReceive('getEnvironment')->andReturn($environment);
        
        $viteAssetHelper = Mockery::mock(ViteAssetHelper::class);
        
        $request = Mockery::mock(ServerRequestInterface::class);
        
        $response = Mockery::mock(ResponseInterface::class);
        
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $handler->shouldReceive('handle')->once()->with($request)->andReturn($response);
        
        $middleware = new TwigViteMiddleware($twig, $viteAssetHelper);
        
        $result = $middleware->process($request, $handler);
        
        $this->assertSame($response, $result);
    }
}
