<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngineNew;
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
            $engine = new ElasticEngineNew(app('elasticsearch'));
            $models = config('scout.models');

            /** @var Model $model */
            foreach ($models as $model) {
                if (!$engine->existsIndex($model)) {
                    $this->components->error("Index {$model::searchableAs()} does not exist.");

                    continue;
                }

                /** @var Collection $records */
                $records = $model::all();

                if ($records->isEmpty()) {
                    $this->components->warn("No data found for model {$model}.");

                    continue;
                }

                $engine->createDocuments($model, $records);

                $this->components->info("Successfully pushed data for model {$model}.");
            }
        } catch (Throwable $e) {
            Log::channel('elastic')->error($e->getMessage());
        }
    }
}
