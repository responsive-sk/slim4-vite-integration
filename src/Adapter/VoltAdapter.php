<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Adapter;

use Phalcon\Mvc\View\Engine\Volt\Compiler;
use Slim4\ViteIntegration\ViteAssetHelper;

class VoltAdapter extends AbstractTemplateAdapter
{
    /**
     * Constructor
     *
     * @param Compiler $engine
     */
    public function __construct(Compiler $engine)
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
        $this->engine->addFunction('vite_js', function (array $arguments) use ($viteAssetHelper) {
            return $viteAssetHelper->jsTag($arguments[0]);
        });
        
        $this->engine->addFunction('vite_css', function (array $arguments) use ($viteAssetHelper) {
            $fallbackCss = isset($arguments[1]) ? $arguments[1] : null;
            return $viteAssetHelper->cssTag($arguments[0], $fallbackCss);
        });
        
        $this->engine->addFunction('vite_asset', function (array $arguments) use ($viteAssetHelper) {
            return $viteAssetHelper->asset($arguments[0]);
        });
        
        $this->engine->addFunction('vite_image', function (array $arguments) use ($viteAssetHelper) {
            $resourcePath = isset($arguments[1]) ? $arguments[1] : 'resources/assets/web/images';
            $placeholder = isset($arguments[2]) ? $arguments[2] : 'placeholder.jpg';
            return $viteAssetHelper->image($arguments[0], $resourcePath, $placeholder);
        });
        
        $this->engine->addFunction('vite_font', function (array $arguments) use ($viteAssetHelper) {
            return $viteAssetHelper->font($arguments[0]);
        });
        
        // Add global variable
        $this->engine->getCompiler()->getEventsManager()->attach('view:beforeRender', function ($event, $view) use ($viteAssetHelper) {
            $view->setVar('vite', $viteAssetHelper);
        });
    }
}
