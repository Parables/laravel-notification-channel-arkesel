<?php

/*
 * @author Parables Boltnoel <parables95@gmail.com>
 * @package arkesel-sdk
 *  @version 1.0.0
 */

namespace Parables\ArkeselSdk;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Parables\ArkeselSdk\BulkSms\SmsClient;
use Parables\ArkeselSdk\NotificationChannel\ArkeselChannel;

class ArkeselServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/arkesel.php', 'arkesel');

        // Bind the main class to use with the facade
        $this->app->singleton('sms', function ($app) {
            return new SmsClient($app['config']['arkesel']);
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('arkesel', function ($app) {
                return new ArkeselChannel($app->make(SmsClient::class));
            });
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/arkesel.php' => $this->app->configPath('arkesel.php'),
            ], 'arkesel');
        }
    }
}
