<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngineNew;
use App\Models\Post;
use Illuminate\Console\Command;
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
                'title' => 'test11',
                'content' => 'test22',
            ]
        );
        $post->save();

        $engine = new ElasticEngineNew(app('elasticsearch'));
        $response = $engine->createDocument($post);

        dd($response);
    }
}
