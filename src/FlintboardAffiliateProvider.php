<?php

namespace FlintWebmedia\FlintboardAffiliate;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FlintboardAffiliateProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // -- Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'flintaffiliate');

        // -- Load migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function setupRoutes()
    {
        // -- Setup all admin controller routes
        Route::group(['namespace' => 'FlintWebmedia\FlintboardAffiliate\app\Http\Controllers\Admin'], function () {
            Route::group(['middleware' => ['web', 'admin'], 'prefix' => config('backpack.base.route_prefix', 'admin')], function () {

                // -- Product CRUD controller routes
                \CRUD::resource('product', 'ProductCrudController');
                // -- Attribute CRUD controller routes
                \CRUD::resource('attribute', 'AttributeCrudController');
                // -- Feed CRUD controller routes
                \CRUD::resource('feed', 'FeedCrudController')->with(function() {
                    Route::get('feed/{id}/import', 'FeedCrudController@import');
                    Route::post('savemappings', 'FeedCrudController@saveMappings')->name('saveMappings');
                    Route::get('importproducts', 'FeedCrudController@importProducts')->name('importProducts');
                    Route::post('importproducts', 'FeedCrudController@postImportProducts')->name('postImportProducts');
                });

            });

        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // -- Register the current package
        $this->app->bind('flintaffiliate', function ($app) {
            return new FlintboardAffiliate($app);
        });

        // -- Register other service providers
        $this->app->register(\Felixkiss\UniqueWithValidator\ServiceProvider::class);

        $this->setupRoutes();
    }
}
