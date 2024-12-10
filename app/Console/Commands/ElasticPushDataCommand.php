<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngine;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

class ElasticPushDataCommand extends Command
{
    protected $signature = 'elastic:push';

    protected $description = 'Push data from models to Elasticsearch';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            $engine = new ElasticEngine(app('elasticsearch'));
            $models = config('scout.models');

            /** @var Model $model */
            foreach ($models as $model) {
                if (!class_exists($model)) {
                    throw new \DomainException("Model {$model} not found");
                }

                $indexName = $model::searchableAs();

                if (!$engine->existsIndex($indexName)) {
                    $this->components->error("Index {$indexName} does not exist. Create it first!");
                    continue;
                }

                /** @var Collection $records */
                $records = $model::all();

                if ($records->isEmpty()) {
                    $this->components->warn("No data found for model {$model}.");
                    continue;
                }

                /** @var Model $record */
                $documents = $records->map(fn($record) => [
                    'index' => [
                        '_index' => $indexName,
                        '_id' => $record->id,
                    ],
                    'data' => $record->toSearchableArray(),
                ])->toArray();

                $engine->createDocuments($indexName, $documents);

                $this->components->info("Successfully pushed data for model {$model}.");
            }
        } catch (Throwable $e) {
            Log::channel('elastic')->error($e->getMessage());
        }
    }
}
