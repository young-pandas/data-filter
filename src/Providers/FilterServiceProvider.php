<?php

namespace YoungPandas\DataFilter\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
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
            __DIR__ . '/../../config/data-filter.php',
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
            __DIR__ . '/../Stubs/app/Helpers/AppDataFilter.stub' => \App::path('Helpers/AppDataFilter.php'),
            __DIR__ . '/../Stubs/app/Http/Controllers/CreateUserController.stub' => \App::path('Http/Controllers/CreateUserController.php'),
            __DIR__ . '/../Stubs/app/Services/CreateUserService.stub' => \App::path('Services/CreateUserService.php'),
            __DIR__ . '/../Stubs/app/Traits/FirstName.stub' => \App::path('Traits/FirstName.php'),

            __DIR__ . '/../Stubs/config/data-filter.stub' => \App::configPath('data-filter.php'),

            __DIR__ . '/../Stubs/json/Rules/Requests/createUser.stub' => \App::basePath('json/Rules/Requests/createUser.json'),
            __DIR__ . '/../Stubs/json/Rules/Responses/createUser.stub' => \App::basePath('json/Rules/Responses/createUser.json'),

            __DIR__ . '/../Stubs/resources/views/create-user.blade.stub' => \App::resourcePath('views/create-user.blade.php'),
        ], 'data-filter');

        if ($this->app->runningInConsole() && $this->isVendorPublishCommand()) {
            $this->appendRoutes();
            $this->updateAppServiceProvider();
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
        $packageRoutes = __DIR__ . '/../Stubs/routes/web.stub';
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

    private function updateAppServiceProvider()
    {
        $appServiceProviderPath = App::path('Providers/AppServiceProvider.php');
        $stubFilePath = __DIR__ . '/../Stubs/app/Providers/AppServiceProvider.stub';

        try {
            if (!File::exists($appServiceProviderPath)) {
                throw new \Exception("AppServiceProvider file does not exist: $appServiceProviderPath");
            }

            if (!File::exists($stubFilePath)) {
                throw new \Exception("Stub file does not exist: $stubFilePath");
            }

            $originalContent = File::get($appServiceProviderPath);
            $stubContent = File::get($stubFilePath);

            if ($originalContent === false || $stubContent === false) {
                throw new \Exception("Failed to get content from one of the files.");
            }

            // Extract use statements from the stub and original file
            preg_match_all('/^use\s+.*;$/m', $stubContent, $stubUseStatements);
            preg_match_all('/^use\s+.*;$/m', $originalContent, $originalUseStatements);

            // Merge use statements with a separator line
            $mergedUseStatements = array_unique(array_merge($originalUseStatements[0], [''], $stubUseStatements[0]));

            // Extract register method content from the stub and original file
            preg_match('/public function register\(\): void\s*\{([^}]*)\}/s', $stubContent, $stubRegisterMethod);
            preg_match('/public function register\(\): void\s*\{([^}]*)\}/s', $originalContent, $originalRegisterMethod);

            // Add missing register method content without duplication
            if (!empty($stubRegisterMethod) && !empty($originalRegisterMethod)) {
                $stubRegisterContent = trim($stubRegisterMethod[1]);
                $originalRegisterContent = trim($originalRegisterMethod[1]);

                if (strpos($originalRegisterContent, $stubRegisterContent) === false) {
                    if ($originalRegisterContent === '//') {
                        $newRegisterContent = $stubRegisterContent;
                    } else {
                        $newRegisterContent = $originalRegisterContent . "\n\n        " . $stubRegisterContent;
                    }
                    $originalContent = preg_replace('/public function register\(\): void\s*\{([^}]*)\}/s', "public function register(): void {\n$newRegisterContent\n    }", $originalContent);
                }
            }

            // Reconstruct the final content
            $finalContent = "<?php\n\nnamespace App\Providers;\n\n";
            $finalContent .= implode("\n", $mergedUseStatements);
            $finalContent .= "\n\nclass AppServiceProvider extends ServiceProvider\n{\n";
            $finalContent .= "    /**\n     * Register any application services.\n     */\n";
            $finalContent .= "    public function register(): void\n    {\n";
            $finalContent .= "        " . trim($newRegisterContent) . "\n";
            $finalContent .= "    }\n\n";
            $finalContent .= "    /**\n     * Bootstrap any application services.\n     */\n";
            $finalContent .= "    public function boot(): void\n    {\n";
            $finalContent .= "        //\n";
            $finalContent .= "    }\n";
            $finalContent .= "}\n";

            if (File::put($appServiceProviderPath, $finalContent) === false) {
                throw new \Exception("Failed to update AppServiceProvider file: $appServiceProviderPath");
            } else {
                Log::info("Successfully updated AppServiceProvider file: $appServiceProviderPath");
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
        // Bind DataFilterContract to AppDataFilter and inject the base DataFilter
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