<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngine;
use App\Models\Post;
use Illuminate\Console\Command;
use Throwable;

class ElasticUpdateDocumentCommand extends Command
{
    protected $signature = 'elastic:update';

    protected $description = '';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $post = Post::find(1);
//        $post->title = 'Hello world';
        $post->title = 'Enim voluptatem id culpa rerum nisi qui.';
        $post->save();

        dump($post->toArray());

        $engine = new ElasticEngine(app('elasticsearch'));
        $model = Post::class;

        $indexName = $model::searchableAs();
        $documentId = $post->id;
        $documentData = $post->toSearchableArray();

        $response = $engine->updateDocument($indexName, $documentId, $documentData);

        dd($response);
    }
}
