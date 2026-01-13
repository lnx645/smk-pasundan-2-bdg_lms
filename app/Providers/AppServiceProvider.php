<?php

namespace App\Providers;

use App\Models\Discusion;
use App\Models\Materi;
use App\Models\Tugas;
use App\Service\AuthServiceImpl;
use App\Service\Contract\AuthServiceInterface;
use App\Service\Contract\KelasServiceInterface;
use App\Service\Contract\MatpelServiceInterface;
use App\Service\KelasServiceImpl;
use App\Service\MateriService;
use App\Service\Contract\MateriServiceInterface;
use App\Service\MatpelService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthServiceImpl::class);
        $this->app->bind(KelasServiceInterface::class, KelasServiceImpl::class);
        $this->app->bind(MateriServiceInterface::class, MateriService::class);
        $this->app->bind(MatpelServiceInterface::class, MatpelService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        Relation::morphMap([
            'materi' => Materi::class,
            'tugas'  => Tugas::class,
            'forum'  => Discusion::class,
        ]);
    }
}
