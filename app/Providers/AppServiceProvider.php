<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\User;
use App\UserRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis as RedisStorage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CollectorRegistry::class, fn () => new CollectorRegistry(
            new RedisStorage([
                'host' => config('database.redis.default.host'),
                'port' => (int)config('database.redis.default.port'),
            ])
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (?User $user, string $ability, array $arguments = []) {
            $subject = $arguments[0] ?? null;

            if ($subject instanceof User || $subject instanceof Reservation) {
                return null;
            }

           return $user?->role === UserRole::Admin ? true : null;
        });
    }
}
