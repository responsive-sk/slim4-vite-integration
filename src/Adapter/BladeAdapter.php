<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Adapter;

use Illuminate\View\Factory;
use Slim4\ViteIntegration\ViteAssetHelper;

class BladeAdapter extends AbstractTemplateAdapter
{
    /**
     * Constructor
     *
     * @param Factory $engine
     */
    public function __construct(Factory $engine)
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
        
        // Add directives
        $this->engine->directive('viteJs', function (string $expression) {
            return "<?php echo vite_js($expression); ?>";
        });
        
        $this->engine->directive('viteCss', function (string $expression) {
            return "<?php echo vite_css($expression); ?>";
        });
        
        $this->engine->directive('viteAsset', function (string $expression) {
            return "<?php echo vite_asset($expression); ?>";
        });
        
        $this->engine->directive('viteImage', function (string $expression) {
            return "<?php echo vite_image($expression); ?>";
        });
        
        $this->engine->directive('viteFont', function (string $expression) {
            return "<?php echo vite_font($expression); ?>";
        });
        
        // Add helper functions
        if (!function_exists('vite_js')) {
            function vite_js(string $entrypoint) use ($viteAssetHelper) {
                return $viteAssetHelper->jsTag($entrypoint);
            }
        }
        
        if (!function_exists('vite_css')) {
            function vite_css(string $entrypoint, string $fallbackCss = null) use ($viteAssetHelper) {
                return $viteAssetHelper->cssTag($entrypoint, $fallbackCss);
            }
        }
        
        if (!function_exists('vite_asset')) {
            function vite_asset(string $path) use ($viteAssetHelper) {
                return $viteAssetHelper->asset($path);
            }
        }
        
        if (!function_exists('vite_image')) {
            function vite_image(string $path, string $resourcePath = 'resources/assets/web/images', string $placeholder = 'placeholder.jpg') use ($viteAssetHelper) {
                return $viteAssetHelper->image($path, $resourcePath, $placeholder);
            }
        }
        
        if (!function_exists('vite_font')) {
            function vite_font(string $path) use ($viteAssetHelper) {
                return $viteAssetHelper->font($path);
            }
        }
        
        // Add global variable
        $this->engine->share('vite', $this->viteAssetHelper);
    }
}
