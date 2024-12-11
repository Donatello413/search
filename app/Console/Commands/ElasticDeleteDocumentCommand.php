<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngine;
use App\Elastic\ElasticEngineNew;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ElasticDeleteDocumentCommand extends Command
{
    protected $signature = 'elastic:delete';

    protected $description = '';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $post = Post::query()->where('title', 'test11')->firstOrFail();
        $post->delete();

        $engine = new ElasticEngineNew(app('elasticsearch'));
        $response = $engine->deleteDocument($post);

        dd($response);
    }
}
