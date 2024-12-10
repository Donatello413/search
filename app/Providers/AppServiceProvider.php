<?php

namespace App\Providers;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $user = config('scout.elastic.user');
        $password = config('scout.elastic.password');
        $host = config('scout.elastic.host');
        $key = config('scout.elastic.key');

        $this->app->singleton('elasticsearch', static fn() => ClientBuilder::create() // создали подключение
//            ->setBasicAuthentication(username: $user, password: $password)
            ->setApiKey($key)
            ->setHosts(hosts: [$host])
            ->setRetries(3)
            ->build()
        );
    }
}
