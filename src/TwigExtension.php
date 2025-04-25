<?php

declare(strict_types=1);

namespace Slim4\Vite;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private ViteServiceInterface $viteService;

    public function __construct(ViteServiceInterface $viteService)
    {
        $this->viteService = $viteService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_asset', [$this, 'asset']),
            new TwigFunction('vite_entry_link_tags', [$this, 'entryLinkTags'], ['is_safe' => ['html']]),
            new TwigFunction('vite_entry_script_tags', [$this, 'entryScriptTags'], ['is_safe' => ['html']]),
            new TwigFunction('vite_image', [$this, 'image']),
            new TwigFunction('vite_font', [$this, 'font']),

            // Legacy functions for backward compatibility
            new TwigFunction('vite_css', [$this, 'entryLinkTags'], ['is_safe' => ['html']]),
            new TwigFunction('vite_js', [$this, 'entryScriptTags'], ['is_safe' => ['html']]),
        ];
    }

    public function asset(string $entry): string
    {
        return $this->viteService->asset($entry);
    }

    public function entryLinkTags(string $entry): string
    {
        return $this->viteService->entryLinkTags($entry);
    }

    public function entryScriptTags(string $entry): string
    {
        return $this->viteService->entryScriptTags($entry);
    }

    public function image(
        string $path,
        string $resourcePath = 'resources/images',
        string $placeholder = 'placeholder.jpg'
    ): string {
        return $this->viteService->image($path, $resourcePath, $placeholder);
    }

    public function font(string $path): string
    {
        return $this->viteService->font($path);
    }
}
