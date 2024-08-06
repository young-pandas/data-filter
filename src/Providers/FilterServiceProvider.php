<?php

namespace YoungPandas\DataFilter\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use YoungPandas\DataFilter\Services\FilterService;

/**
 * Class FilterServiceProvider
 * @package YoungPandas\DataFilter\Providers
 */
class FilterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Publish/config/data-filter.php',
            'data-filter'
        );

        $this->registerRules();
        $this->registerHelpers();
        $this->registerServices();
        $this->registerFacades();
    }

    public function boot()
    {
        // Define the publishable resources
        $this->publishes([
            __DIR__ . '/../Publish/config/data-filter.php' => \App::configPath('data-filter.php'),
            __DIR__ . '/../Publish/app/Http/Controllers/CreateUserController.php' => \App::path('Http/Controllers/CreateUserController.php'),
            __DIR__ . '/../Publish/app/Helpers/DataFilter.php' => \App::path('Helpers/DataFilter.php'),
            __DIR__ . '/../Publish/json/Rules/Requests/createUser.json' => \App::basePath('json/Rules/Requests/createUser.json'),
            __DIR__ . '/../Publish/json/Rules/Responses/createUser.json' => \App::basePath('json/Rules/Responses/createUser.json'),
            __DIR__ . '/../Publish/app/Services/Service.php' => \App::path('Services/Service.php'),
            __DIR__ . '/../Publish/app/Services/CreateUserService.php' => \App::path('Services/CreateUserService.php'),
            __DIR__ . '/../Publish/resources/views/create-user.blade.php' => \App::resourcePath('views/create-user.blade.php'),
        ], 'data-filter');

        if ($this->app->runningInConsole() && $this->isVendorPublishCommand()) {
            $this->appendRoutes();
        }
    }

    private function isVendorPublishCommand()
    {
        $currentCommand = collect($_SERVER['argv'])->implode(' ');
        return str_contains($currentCommand, 'vendor:publish')
            && !str_contains($currentCommand, 'package:discover')
            && !str_contains($currentCommand, '--tag=laravel-assets');
    }

    private function appendRoutes()
    {
        $packageRoutes = __DIR__ . '/../Publish/routes/web.php';
        $appRoutes = App::basePath('routes/web.php');

        try {
            if (!File::exists($packageRoutes)) {
                throw new \Exception("Package routes file does not exist: $packageRoutes");
            }

            if (!File::exists($appRoutes)) {
                throw new \Exception("App routes file does not exist: $appRoutes");
            }

            $routesContent = File::get($packageRoutes);
            if ($routesContent === false) {
                throw new \Exception("Failed to get content from package routes file: $packageRoutes");
            }

            $appRoutesContent = File::get($appRoutes);
            if ($appRoutesContent === false) {
                throw new \Exception("Failed to get content from app routes file: $appRoutes");
            }

            // Check if the routes content already exists in the app routes file
            if (strpos($appRoutesContent, $routesContent) !== false) {
                Log::info("Routes content already exists in app routes file: $appRoutes");
                return;
            }

            $result = File::append($appRoutes, PHP_EOL . $routesContent);
            if ($result === false) {
                throw new \Exception("Failed to append content to app routes file: $appRoutes");
            } else {
                Log::info("Successfully appended routes to app routes file: $appRoutes");
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function registerRules()
    {
        $this->app->bind(
            'YoungPandas\DataFilter\Contracts\RulesContract',
            'YoungPandas\DataFilter\Services\RulesService',
        );
    }

    public function registerHelpers()
    {
        $this->app->bind(
            'YoungPandas\DataFilter\Contracts\DataFilterContract',
            'YoungPandas\DataFilter\Helpers\DataFilter',
        );
    }

    public function registerServices()
    {
        $this->app->bind(
            'YoungPandas\DataFilter\Contracts\ServiceContract',
            'YoungPandas\DataFilter\Services\FilterService',
        );
    }

    public function registerFacades()
    {
        $this->app->singleton('filter', function ($app) {
            return new FilterService($app->make('YoungPandas\DataFilter\Contracts\DataFilterContract'));
        });
    }
}