<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngineNew;
use App\Models\Post;
use Illuminate\Console\Command;
use Throwable;

class ElasticClearIndexCommand extends Command
{
    protected $signature = 'elastic:clear-index';

    protected $description = '';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $engine = new ElasticEngineNew(app('elasticsearch'));
        $response = $engine->clearIndex(new Post);

        dd($response);
    }
}
