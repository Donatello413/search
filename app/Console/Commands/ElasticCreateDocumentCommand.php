<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngine;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ElasticCreateDocumentCommand extends Command
{
    protected $signature = 'elastic:create';

    protected $description = '';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $post = new Post(
            [
                'title' => 'test1',
                'content' => 'test2',
            ]
        );
        $post->save();

        dump($post->toArray());

        $engine = new ElasticEngine(app('elasticsearch'));
        $model = Post::class;

        $indexName = $model::searchableAs();
        $documentId = $post->id;
        $documentData = $post->toSearchableArray();

        $response = $engine->createDocument($indexName, $documentId, $documentData);

        dd($response);
    }
}
