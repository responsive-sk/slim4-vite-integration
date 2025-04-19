<?php

declare(strict_types=1);

namespace Slim4\ViteIntegration\Adapter;

use Slim4\ViteIntegration\TemplateEngineInterface;
use Slim4\ViteIntegration\ViteAssetHelper;

abstract class AbstractTemplateAdapter implements TemplateEngineInterface
{
    /**
     * @var mixed The template engine instance
     */
    protected $engine;
    
    /**
     * @var ViteAssetHelper|null
     */
    protected ?ViteAssetHelper $viteAssetHelper = null;
    
    /**
     * Constructor
     *
     * @param mixed $engine The template engine instance
     */
    public function __construct($engine)
    {
        $this->engine = $engine;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getEngine()
    {
        return $this->engine;
    }
    
    /**
     * {@inheritdoc}
     */
    public function registerViteAssetHelper(ViteAssetHelper $viteAssetHelper): void
    {
        $this->viteAssetHelper = $viteAssetHelper;
        $this->registerFunctions();
    }
    
    /**
     * Register template functions/helpers
     *
     * @return void
     */
    abstract protected function registerFunctions(): void;
}
