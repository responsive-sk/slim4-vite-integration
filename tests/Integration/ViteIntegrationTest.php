<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Tests\Integration;

use DI\Container;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim4\ViteIntegration\Middleware\TwigViteMiddleware;
use Slim4\ViteIntegration\ViteAssetHelper;

class ViteIntegrationTest extends TestCase
{
    private string $fixturesDir;
    
    protected function setUp(): void
    {
        $this->fixturesDir = dirname(__DIR__) . '/Fixtures';
        
        // Create fixtures directory if it doesn't exist
        if (!is_dir($this->fixturesDir)) {
            mkdir($this->fixturesDir, 0777, true);
        }
        
        // Create templates directory if it doesn't exist
        if (!is_dir($this->fixturesDir . '/templates')) {
            mkdir($this->fixturesDir . '/templates', 0777, true);
        }
        
        // Create public directory if it doesn't exist
        if (!is_dir($this->fixturesDir . '/public')) {
            mkdir($this->fixturesDir . '/public', 0777, true);
        }
        
        // Create build directory if it doesn't exist
        if (!is_dir($this->fixturesDir . '/public/build')) {
            mkdir($this->fixturesDir . '/public/build', 0777, true);
        }
        
        // Create template file
        file_put_contents(
            $this->fixturesDir . '/templates/index.twig',
            '<!DOCTYPE html>
<html>
<head>
    {{ vite.cssTag("resources/assets/js/app.js")|raw }}
    {{ vite.jsTag("resources/assets/js/app.js")|raw }}
</head>
<body>
    <h1>Hello, World!</h1>
    <img src="{{ vite.image("logo.png") }}" alt="Logo">
</body>
</html>'
        );
        
        // Create manifest file
        $manifest = [
            'resources/assets/js/app.js' => [
                'file' => 'assets/app.1234.js',
                'css' => ['assets/app.1234.css'],
            ],
            'resources/assets/web/images/logo.png' => [
                'file' => 'assets/logo.1234.png',
            ],
        ];
        
        file_put_contents(
            $this->fixturesDir . '/public/build/manifest.json',
            json_encode($manifest)
        );
    }
    
    protected function tearDown(): void
    {
        // Remove manifest file if it exists
        if (file_exists($this->fixturesDir . '/public/build/manifest.json')) {
            unlink($this->fixturesDir . '/public/build/manifest.json');
        }
        
        // Remove template file if it exists
        if (file_exists($this->fixturesDir . '/templates/index.twig')) {
            unlink($this->fixturesDir . '/templates/index.twig');
        }
    }
    
    public function testIntegration(): void
    {
        // Create container
        $container = new Container();
        
        // Create Twig instance
        $twig = Twig::create($this->fixturesDir . '/templates', [
            'cache' => false,
            'debug' => true,
            'auto_reload' => true,
        ]);
        
        // Create Vite Asset Helper
        $viteAssetHelper = new ViteAssetHelper(
            $this->fixturesDir . '/public/build/manifest.json',
            false,
            'http://localhost:5173',
            $this->fixturesDir . '/public'
        );
        
        // Create app
        AppFactory::setContainer($container);
        $app = AppFactory::create();
        
        // Add middleware
        $app->add(new TwigViteMiddleware($twig, $viteAssetHelper));
        
        // Define route
        $app->get('/', function (ServerRequestInterface $request, ResponseInterface $response) use ($twig) {
            return $twig->render($response, 'index.twig');
        });
        
        // Create request
        $request = $this->createRequest('GET', '/');
        
        // Process request
        $response = $app->handle($request);
        
        // Assert response
        $this->assertEquals(200, $response->getStatusCode());
        
        $body = (string) $response->getBody();
        
        $this->assertStringContainsString('<link rel="stylesheet" href="/build/assets/app.1234.css">', $body);
        $this->assertStringContainsString('<script type="module" src="/build/assets/app.1234.js"></script>', $body);
        $this->assertStringContainsString('<img src="/build/assets/logo.1234.png" alt="Logo">', $body);
    }
    
    private function createRequest(string $method, string $uri): ServerRequestInterface
    {
        $factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        
        $request = $factory->createServerRequest($method, $uri);
        
        return $request;
    }
}
