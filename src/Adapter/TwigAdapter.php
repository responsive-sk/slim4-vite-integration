<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Adapter;

use Slim4\ViteIntegration\ViteAssetHelper;
use Twig\Environment;
use Twig\TwigFunction;

class TwigAdapter extends AbstractTemplateAdapter
{
    /**
     * Constructor
     *
     * @param Environment $engine
     */
    public function __construct(Environment $engine)
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
        
        // Add global variable
        $this->engine->addGlobal('vite', $this->viteAssetHelper);
        
        // Add functions
        $this->engine->addFunction(new TwigFunction('vite_js', function (string $entrypoint) use ($viteAssetHelper) {
            return $viteAssetHelper->jsTag($entrypoint);
        }, ['is_safe' => ['html']]));
        
        $this->engine->addFunction(new TwigFunction('vite_css', function (string $entrypoint, string $fallbackCss = null) use ($viteAssetHelper) {
            return $viteAssetHelper->cssTag($entrypoint, $fallbackCss);
        }, ['is_safe' => ['html']]));
        
        $this->engine->addFunction(new TwigFunction('vite_asset', function (string $path) use ($viteAssetHelper) {
            return $viteAssetHelper->asset($path);
        }));
        
        $this->engine->addFunction(new TwigFunction('vite_image', function (string $path, string $resourcePath = 'resources/assets/web/images', string $placeholder = 'placeholder.jpg') use ($viteAssetHelper) {
            return $viteAssetHelper->image($path, $resourcePath, $placeholder);
        }));
        
        $this->engine->addFunction(new TwigFunction('vite_font', function (string $path) use ($viteAssetHelper) {
            return $viteAssetHelper->font($path);
        }));
    }
}
