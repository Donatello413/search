<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngine;
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
        $post = Post::query()->where('title', 'test1')->first();
        $post->delete();

        dump($post->toArray());

        $engine = new ElasticEngine(app('elasticsearch'));
        $model = Post::class;

        $indexName = $model::searchableAs();
        $documentId = $post->id;

        $response = $engine->deleteDocument($indexName, $documentId);

        dd($response);
    }
}
