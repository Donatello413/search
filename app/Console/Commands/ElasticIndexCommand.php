<?php

namespace App\Console\Commands;

use App\Elastic\ElasticEngine;
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
            $engine = new ElasticEngine(app('elasticsearch'));   // создали подключение и клиент для работы с эластиком
            $models = config('scout.models');                       // модели у которых есть поиск

            foreach ($models as $model) {
                if (!class_exists($model)) {
                    throw new \DomainException("Model {$model} not found");
                }

                $indexName = $model::searchableAs();
                $mapping = $model::mapping(); // todo настроить в модели

                if ($engine->existsIndex($indexName)) {
                    $engine->deleteIndex($indexName);
                }

                if (!$engine->existsIndex($indexName)) {
                    $engine->createIndex($indexName, $mapping);
                }

                $this->components->info('Successfully');
            }
        } catch (Throwable $e) {
            Log::channel('elastic')->error($e->getMessage());
        }
    }
}
