<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Middleware;

use Illuminate\View\Factory;
use Slim4\ViteIntegration\Adapter\BladeAdapter;
use Slim4\ViteIntegration\ViteAssetHelper;

class BladeViteMiddleware extends AbstractViteMiddleware
{
    /**
     * Constructor
     *
     * @param Factory $blade
     * @param ViteAssetHelper $viteAssetHelper
     */
    public function __construct(Factory $blade, ViteAssetHelper $viteAssetHelper)
    {
        $adapter = new BladeAdapter($blade);
        parent::__construct($adapter, $viteAssetHelper);
    }
}
