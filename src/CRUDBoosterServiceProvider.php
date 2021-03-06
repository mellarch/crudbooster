<?php namespace crocodicstudio\crudbooster;

use crocodicstudio\crudbooster\singleton\CRUDBooster;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use crocodicstudio\crudbooster\commands\CrudboosterInstallationCommand;
use crocodicstudio\crudbooster\commands\CrudboosterUpdateCommand;
use Illuminate\Foundation\AliasLoader;
use App;

class CRUDBoosterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadTranslationsFrom(__DIR__.'/localization','crudbooster');
        $this->loadViewsFrom(__DIR__.'/views', 'crudbooster');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        if($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php')],'cb_config');
            $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');

            if(!file_exists(app_path('Http/Controllers/CBHook.php'))) {
                $this->publishes([__DIR__.'/stubs/controllers/CBHook.php.stub' => app_path('Http/Controllers/CBHook.php')],'CBHook');
            }

            if(!file_exists(app_path('Http/Controllers/AdminDashboardController.php'))) {
                $this->publishes([__DIR__.'/stubs/controllers/AdminDashboardController.php.stub' => app_path('Http/Controllers/AdminDashboardController.php')],'cb_dashboard_controller');
            }

            if(!file_exists(app_path('Http/Controllers/AdminProfileController.php'))) {
                $this->publishes([__DIR__.'/stubs/controllers/AdminProfileController.php.stub' => app_path('Http/Controllers/AdminProfileController.php')],'cb_profile_controller');
            }

            $this->publishes([__DIR__.'/assets'=>public_path('vendor/crudbooster')],'cb_asset');
        }

        require __DIR__.'/validations/validation.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {                                   
        require __DIR__.'/helpers/Helper.php';      

        $this->mergeConfigFrom(__DIR__.'/configs/crudbooster.php','crudbooster');        
        
        $this->app->singleton('crudbooster', function () { return new CRUDBooster; });

        if($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->commands('crudboosterinstall');
            $this->commands('crudboosterupdate');
        }

        $this->app->register('Barryvdh\DomPDF\ServiceProvider');
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        $this->app->register('Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider');
        $this->app->register('Intervention\Image\ImageServiceProvider');
    }
   
    private function registerCommands()
    {
        $this->app->singleton('crudboosterinstall',function() {
            return new CrudboosterInstallationCommand;
        });
        
        $this->app->singleton('crudboosterupdate',function() {
            return new CrudboosterUpdateCommand;
        });        
    }    
}
