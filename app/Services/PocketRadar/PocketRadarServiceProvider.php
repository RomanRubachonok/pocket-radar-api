<?php


namespace App\Services\PocketRadar;

use App\Services\PocketRadar\Http\Clients\PocketRadarGuzzleHttpClient;
use Illuminate\Support\ServiceProvider;

class PocketRadarServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PocketRadarClient::class, function ($app) {
            return new PocketRadarClient(new PocketRadarGuzzleHttpClient());
        });

    }

    public function provides()
    {
        return [PocketRadarClient::class];
    }
}