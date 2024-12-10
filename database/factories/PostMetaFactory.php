<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostMeta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostMeta>
 */
class PostMetaFactory extends Factory
{
    protected $model = PostMeta::class;

    public function definition(): array
    {
        $post = Post::all()->random()->id;

        return [
            'post_id' => $post,
            'meta_key' => $this->faker->word,
            'meta_value' => $this->faker->sentence,
        ];
    }
}
