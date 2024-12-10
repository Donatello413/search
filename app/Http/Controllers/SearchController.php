<?php

namespace App\Http\Controllers;

use App\Elastic\ElasticEngine;
use Illuminate\Http\Request;
use Throwable;

class SearchController extends Controller
{
    /**
     * @throws Throwable
     */
    public function search(Request $request)
    {
        $indexName = $request->input('index', 'default_index');

        $query = $request->input('query');
        $searchBody = [
            'query' => [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title', 'description', 'content'],
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
}
