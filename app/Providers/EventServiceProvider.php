<?php

namespace App\Providers;

use App\Notifications\Channels\FcmChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Messaging;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Listener registration is handled by Laravel 11+'s automatic listener
     * auto-discovery, which scans `app/Listeners/` for classes with a typed
     * `handle(SomeEvent $event)` method and binds them automatically. We
     * intentionally do NOT redeclare them here — doing so caused each
     * listener to fire twice in earlier iterations of this provider.
     *
     * The provider's only job now is to register the custom 'fcm' channel
     * driver so notifications can use it via $notification->via().
     */
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)->extend('fcm', function ($app) {
            return new FcmChannel($app->make(Messaging::class));
        });
    }
}
