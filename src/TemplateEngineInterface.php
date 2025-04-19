<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration;

interface TemplateEngineInterface
{
    /**
     * Register the Vite asset helper with the template engine
     *
     * @param ViteAssetHelper $viteAssetHelper
     * @return void
     */
    public function registerViteAssetHelper(ViteAssetHelper $viteAssetHelper): void;
    
    /**
     * Get the underlying template engine instance
     *
     * @return mixed
     */
    public function getEngine();
}
