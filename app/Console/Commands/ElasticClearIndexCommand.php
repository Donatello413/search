<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngine;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
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
        $engine = new ElasticEngine(app('elasticsearch'));
        $model = Post::class;

        $indexName = $model::searchableAs();

        $response = $engine->clearIndex($indexName);

        dd($response);
    }
}
