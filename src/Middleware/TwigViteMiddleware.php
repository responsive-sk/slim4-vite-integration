<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Middleware;

use Slim4\ViteIntegration\Adapter\TwigAdapter;
use Slim4\ViteIntegration\ViteAssetHelper;
use Slim\Views\Twig;

class TwigViteMiddleware extends AbstractViteMiddleware
{
    /**
     * Constructor
     *
     * @param Twig $twig
     * @param ViteAssetHelper $viteAssetHelper
     */
    public function __construct(Twig $twig, ViteAssetHelper $viteAssetHelper)
    {
        $adapter = new TwigAdapter($twig->getEnvironment());
        parent::__construct($adapter, $viteAssetHelper);
    }
}
