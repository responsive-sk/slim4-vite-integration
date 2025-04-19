<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Middleware;

use Phalcon\Mvc\View\Engine\Volt\Compiler;
use Slim4\ViteIntegration\Adapter\VoltAdapter;
use Slim4\ViteIntegration\ViteAssetHelper;

class VoltViteMiddleware extends AbstractViteMiddleware
{
    /**
     * Constructor
     *
     * @param Compiler $volt
     * @param ViteAssetHelper $viteAssetHelper
     */
    public function __construct(Compiler $volt, ViteAssetHelper $viteAssetHelper)
    {
        $adapter = new VoltAdapter($volt);
        parent::__construct($adapter, $viteAssetHelper);
    }
}
