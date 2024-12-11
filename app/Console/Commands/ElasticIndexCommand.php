<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngineNew;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ElasticIndexCommand extends Command
{
    protected $signature = 'elastic:index';

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

                if ($engine->existsIndex($modelInstance)) {
                    $engine->deleteIndex($modelInstance);
                }

                if (!$engine->existsIndex($modelInstance)) {
                    $engine->createIndex($modelInstance);
                }

                $this->components->info('Successfully');
            }
        } catch (Throwable $e) {
            Log::channel('elastic')->error($e->getMessage());
        }
    }
}
