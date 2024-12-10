<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class Test extends Command
{
    protected $signature = 'test';

    protected $description = '';

    public function handle()
    {

// COMPLETE:
// command:
// php artisan scout:index "posts_index" +
// php artisan scout:import "App\Models\Post" +
// php artisan scout:flush "App\Models\Post" +
// php artisan scout:sync-index-settings -
// Model::search('...')->get() +
// auto:
// create +
// update +
// delete +

// TODO:
// relation ?
// search settings ?



//        $a = Post::search('pa')->get();
//        dd($a);

        $post = new Post();
        $post->text = 'asasasasasasas';
        $post->title = '123';
        $post->save();

    }
}
