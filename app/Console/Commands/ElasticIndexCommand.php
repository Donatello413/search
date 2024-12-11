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
            $engine = new ElasticEngineNew(app('elasticsearch'));   // создали подключение и клиент для работы с эластиком
            $models = config('scout.models');                       // модели у которых есть поиск

            foreach ($models as $model) {
                if ($engine->existsIndex($model)) {
                    $engine->deleteIndex($model);
                }

                if (!$engine->existsIndex($model)) {
                    $engine->createIndex($model);
                }

                $this->components->info('Successfully');
            }
        } catch (Throwable $e) {
            Log::channel('elastic')->error($e->getMessage());
        }
    }
}
