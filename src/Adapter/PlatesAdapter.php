<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Adapter;

use League\Plates\Engine;
use Slim4\ViteIntegration\ViteAssetHelper;

class PlatesAdapter extends AbstractTemplateAdapter
{
    /**
     * Constructor
     *
     * @param Engine $engine
     */
    public function __construct(Engine $engine)
    {
        parent::__construct($engine);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function registerFunctions(): void
    {
        if (!$this->viteAssetHelper) {
            return;
        }
        
        $viteAssetHelper = $this->viteAssetHelper;
        
        // Add functions
        $this->engine->registerFunction('vite_js', function (string $entrypoint) use ($viteAssetHelper) {
            return $viteAssetHelper->jsTag($entrypoint);
        });
        
        $this->engine->registerFunction('vite_css', function (string $entrypoint, string $fallbackCss = null) use ($viteAssetHelper) {
            return $viteAssetHelper->cssTag($entrypoint, $fallbackCss);
        });
        
        $this->engine->registerFunction('vite_asset', function (string $path) use ($viteAssetHelper) {
            return $viteAssetHelper->asset($path);
        });
        
        $this->engine->registerFunction('vite_image', function (string $path, string $resourcePath = 'resources/assets/web/images', string $placeholder = 'placeholder.jpg') use ($viteAssetHelper) {
            return $viteAssetHelper->image($path, $resourcePath, $placeholder);
        });
        
        $this->engine->registerFunction('vite_font', function (string $path) use ($viteAssetHelper) {
            return $viteAssetHelper->font($path);
        });
        
        // Add global variable
        $this->engine->addData(['vite' => $this->viteAssetHelper]);
    }
}
