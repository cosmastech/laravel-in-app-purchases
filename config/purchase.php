<?php

use Imdhemy\Purchases\Events\GooglePlay\SubscriptionCanceled;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionDeferred;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionExpired;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionInGracePeriod;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionOnHold;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionPaused;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionPauseScheduleChanged;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionPriceChangeConfirmed;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionPurchased;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionRecovered;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionRenewed;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionRestarted;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionRevoked;

return [
    'google_application_credentials' => env(
        'GOOGLE_APPLICATION_CREDENTIALS',
        storage_path('google-app-credentials.json')
    ),

    'routing' => [],

    'eventListeners' => [
        SubscriptionPurchased::class => [],
        SubscriptionRenewed::class => [],
        SubscriptionInGracePeriod::class => [],
        SubscriptionExpired::class => [],
        SubscriptionCanceled::class => [],
        SubscriptionPaused::class => [],
        SubscriptionRestarted::class => [],
        SubscriptionDeferred::class => [],
        SubscriptionRevoked::class => [],
        SubscriptionOnHold::class => [],
        SubscriptionRecovered::class => [],
        SubscriptionPauseScheduleChanged::class => [],
        SubscriptionPriceChangeConfirmed::class => [],
    ],
];
