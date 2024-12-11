<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngineNew;
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
        $post->title = 'Hello world';
//        $post->title = 'Enim voluptatem id culpa rerum nisi qui.';
        $post->save();

        $engine = new ElasticEngineNew(app('elasticsearch'));
        $response = $engine->updateDocument($post);

        dd($response);
    }
}
