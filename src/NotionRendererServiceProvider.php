<?php

namespace RehanKanak\LaravelNotionRenderer;

use Illuminate\Support\ServiceProvider;

class NotionRendererServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/notion-renderer.php' => config_path('notion-renderer.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/notion-renderer.php', 'notion-renderer');
    }
}
