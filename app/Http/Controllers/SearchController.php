<?php

namespace App\Http\Controllers;

use App\Elastic\ElasticEngine;
use App\Http\Requests\SearchRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Throwable;

class SearchController extends Controller
{
    /**
     * @throws Throwable
     */
    public function searchPosts(SearchRequest $request): \Illuminate\Http\JsonResponse
    {
        $model = Post::class;
        $indexName = $model::searchableAs();

        $query = $request->input('search');

        $searchBody = [
            'query' => [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title', 'content'],
                ],
            ],
        ];

        $engine = new ElasticEngine(app('elasticsearch'));
        $results = $engine->searchDocuments($indexName, $searchBody);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function getAllDocuments(Request $request): \Illuminate\Http\JsonResponse
    {
        $model = Post::class;
        $indexName = $model::searchableAs();

        $engine = new ElasticEngine(app('elasticsearch'));
        $results = $engine->getAllDocuments($indexName);
        dd($results);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}
