<?php

namespace tgalfa\RepoService;

use Illuminate\Support\ServiceProvider;
use tgalfa\RepoService\Console\Commands\GenerateRepositoryServiceCommand;

class RepoServiceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    /**
     * Register application services.
     * Register the Command.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRepositoryServiceCommand::class,
            ]);
        }
    }

    /**
     * Register paths for config publish.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/reposervice.php' => config_path('reposervice.php'),
        ], 'reposervice-config');
    }
}
