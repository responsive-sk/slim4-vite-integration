<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Middleware;

use League\Plates\Engine;
use Slim4\ViteIntegration\Adapter\PlatesAdapter;
use Slim4\ViteIntegration\ViteAssetHelper;

class PlatesViteMiddleware extends AbstractViteMiddleware
{
    /**
     * Constructor
     *
     * @param Engine $plates
     * @param ViteAssetHelper $viteAssetHelper
     */
    public function __construct(Engine $plates, ViteAssetHelper $viteAssetHelper)
    {
        $adapter = new PlatesAdapter($plates);
        parent::__construct($adapter, $viteAssetHelper);
    }
}
