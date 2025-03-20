<?php

namespace App\Providers;

use App\Repositories\Interfaces\SnippetRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Repositories\SnippetRepository;
use App\Repositories\TagRepository;
use App\Services\SnippetService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Repositories
        $this->app->bind(SnippetRepositoryInterface::class, SnippetRepository::class);
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);

        // Services
        $this->app->bind(SnippetService::class, function ($app) {
            return new SnippetService(
                $app->make(SnippetRepositoryInterface::class),
                $app->make(TagRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
