<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngineNew;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class ElasticPushDocumentsCommand extends Command
{
    protected $signature = 'elastic:push';

    protected $description = '';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        try {
            $engine = new ElasticEngineNew(app('elasticsearch'));
            $models = config('scout.models');

            foreach ($models as $model) {
                $modelInstance = new $model;

                if (!$engine->existsIndex($modelInstance)) {
                    $this->components->error("Index {$model::searchableAs()} does not exist.");
                    continue;
                }

                /** @var Collection $records */
                $records = $model::all();

                if ($records->isEmpty()) {
                    $this->components->warn("No data found for model {$model}.");
                    continue;
                }

                $engine->createDocuments($modelInstance, $records);

                $this->components->info('Successfully');
            }
        } catch (Throwable $e) {
            Log::channel('elastic')->error($e->getMessage());
        }
    }
}
