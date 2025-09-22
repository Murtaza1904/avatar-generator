<?php

declare(strict_types=1);

namespace murtaza1904\AvatarGenerator;

use Illuminate\Support\ServiceProvider;

/**
 * Class AvatarServiceProvider
 *
 * Laravel service provider for the Avatar Generator package.
 * Registers the Avatar singleton and publishes the configuration file.
 */
final class AvatarServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind Avatar as a singleton so the same instance is reused
        $this->app->singleton('avatar.generator', function () {
            return new Avatar();
        });
    }

    /**
     * Bootstrap package services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish the configuration file for customization
        $this->publishes([
            __DIR__ . '/../config/avatar.php' => config_path('avatar.php'),
        ], 'avatar-config');
    }
}
