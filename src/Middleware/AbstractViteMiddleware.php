<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim4\ViteIntegration\TemplateEngineInterface;
use Slim4\ViteIntegration\ViteAssetHelper;

abstract class AbstractViteMiddleware implements ViteMiddlewareInterface
{
    /**
     * @var TemplateEngineInterface
     */
    protected TemplateEngineInterface $templateEngine;
    
    /**
     * @var ViteAssetHelper
     */
    protected ViteAssetHelper $viteAssetHelper;
    
    /**
     * Constructor
     *
     * @param TemplateEngineInterface $templateEngine
     * @param ViteAssetHelper $viteAssetHelper
     */
    public function __construct(TemplateEngineInterface $templateEngine, ViteAssetHelper $viteAssetHelper)
    {
        $this->templateEngine = $templateEngine;
        $this->viteAssetHelper = $viteAssetHelper;
    }
    
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->templateEngine->registerViteAssetHelper($this->viteAssetHelper);
        
        return $handler->handle($request);
    }
}
