<?php

namespace App\Elastic;

use Elastic\Elasticsearch\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class ElasticEngineNew
{
    public function __construct(
        protected Client $client,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function createIndex(Model $model): array
    {
        if (!class_exists($model)) {
            throw new \DomainException("Model {$model} not found");
        }

        if (!$model instanceof SearchInterface) {
            throw new \DomainException("Model {$model} does not implement SearchInterface");
        }

        /** @var string $indexName */
        $indexName = $model::searchableAs();

        /** @var array $mapping */
        $mapping = $model::mapping(); // todo настроить в модели


        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'index' => [
                        'number_of_shards' => 1,   // Независимый экземпляр индекса, позволяет выполнять параллельные операции. Больше данных в индексе, больше число. Дефолтное значение 5
                        'number_of_replicas' => 0,  // Количество копий шардов. Дефолтное значение 1 (т.е. одна копия для каждой шарды)
                        'analysis' => [             // Отвечает как текстовые данные индексируются и запрашиваются
                            'tokenizer' => '',      // разбивает текст на токены (слова, символы или фразы)
                            'filter' => '',         // изменяет токены после токенизации
                            'analyzer' => '',       // задаёт полный процесс анализа текста
                        ],
                    ],
                ],
//                'mappings' => $mapping            // пустой массив не проходит создание
            ]
        ];

        return $this->client->indices()->create($params)->asArray();
    }

    /**
     * @throws Throwable
     */
    public function existsIndex(Model $model): bool
    {

        if (!class_exists($model)) {
            throw new \DomainException("Model {$model} not found");
        }

        if (!$model instanceof SearchInterface) {
            throw new \DomainException("Model {$model} does not implement SearchInterface");
        }

        /** @var string $indexName */
        $indexName = $model::searchableAs();

        return $this->client->indices()->exists(['index' => $indexName])->asBool();
    }

    /**
     * @throws Throwable
     */
    public function deleteIndex(Model $model): array
    {
        if (!class_exists($model)) {
            throw new \DomainException("Model {$model} not found");
        }

        if (!$model instanceof SearchInterface) {
            throw new \DomainException("Model {$model} does not implement SearchInterface");
        }

        /** @var string $indexName */
        $indexName = $model::searchableAs();

        return $this->client->indices()->delete(['index' => $indexName])->asArray();
    }

    /**
     * @throws Throwable
     */
    public function createDocuments(Model $model, Collection $records): array
    {
        if (!class_exists($model)) {
            throw new \DomainException("Model {$model} not found");
        }

        if (!$model instanceof SearchInterface) {
            throw new \DomainException("Model {$model} does not implement SearchInterface");
        }

        /** @var string $indexName */
        $indexName = $model::searchableAs();

        /** @var Model $record */
        $documents = $records->map(fn($record) => [
            'index' => [
                '_index' => $indexName,
                '_id' => $record->id,
            ],
            'data' => $record->toSearchableArray(),
        ])->toArray();

        $params = ['body' => []];

        foreach ($documents as $document) {
            // Метаинформация.
            // Указывает, что делать (например, index(создание/обновление), create, update, или delete) и дополнительные параметры (_index, _id и т. д.).
            // index полностью перезаписывает документ
            $params['body'][] = [
                'index' => [
                    '_index' => $indexName,             // Указываем, что документ должен быть добавлен в индекс, например posts_index
                    '_id' => $document['index']['_id'],
                ],
            ];

            // Содержимое документа.
            // Представляет данные, которые необходимо проиндексировать или обновить.
            $params['body'][] = $document['data'];
        }

        return $this->client->bulk($params)->asArray();
    }

    /**
     * @throws Throwable
     */
    public function updateDocument(Model $model): array
    {
        $modelClass = get_class($model);

        if (!class_exists($modelClass)) {
            throw new \DomainException("Model {$modelClass} not found");
        }

        if (!$model instanceof SearchInterface) {
            throw new \DomainException("Model {$model} does not implement SearchInterface");
        }

        /** @var string $indexName */
        $indexName = $modelClass::searchableAs();

        /** @var positive-int $documentId */
        $documentId = $model->id;

        /** @var array $documentData */
        $documentData = $model->toSearchableArray();

        $params = [
            'index' => $indexName,
            'id' => $documentId,
            'body' => [
                'doc' => $documentData,
            ],
        ];

        return $this->client->update($params)->asArray();
    }

    /**
     * @throws Throwable
     */
    public function createDocument(Model $model): array
    {
        $modelClass = get_class($model);

        if (!class_exists($modelClass)) {
            throw new \DomainException("Model {$modelClass} not found");
        }

        if (!$model instanceof SearchInterface) {
            throw new \DomainException("Model {$model} does not implement SearchInterface");
        }

        /** @var string $indexName */
        $indexName = $modelClass::searchableAs();

        /** @var positive-int $documentId */
        $documentId = $model->id;

        /** @var array $documentData */
        $documentData = $model->toSearchableArray();

        $params = [
            'index' => $indexName,
            'id' => $documentId,
            'body' => $documentData
        ];

        return $this->client->create($params)->asArray();
    }

    /**
     * @throws Throwable
     */
    public function deleteDocument(Model $model): array
    {
        $modelClass = get_class($model);

        if (!class_exists($modelClass)) {
            throw new \DomainException("Model {$modelClass} not found");
        }

        if (!$model instanceof SearchInterface) {
            throw new \DomainException("Model {$model} does not implement SearchInterface");
        }

        /** @var string $indexName */
        $indexName = $modelClass::searchableAs();

        /** @var positive-int $documentId */
        $documentId = $model->id;

        $params = [
            'index' => $indexName,
            'id' => $documentId,
        ];

        return $this->client->delete($params)->asArray();
    }

    /**
     * @throws Throwable
     */
    public function clearIndex(Model $model): array
    {
        $modelClass = get_class($model);

        if (!class_exists($modelClass)) {
            throw new \DomainException("Model {$modelClass} not found");
        }

        if (!$model instanceof SearchInterface) {
            throw new \DomainException("Model {$model} does not implement SearchInterface");
        }

        /** @var string $indexName */
        $indexName = $model::searchableAs();

        $params = [
            'index' => $indexName,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ];

        return $this->client->deleteByQuery($params)->asArray();
    }

    /**
     * @throws Throwable
     */
    public function searchDocuments(string $indexName, array $searchBody): array
    {
        $response = $this->client->search([
            'index' => $indexName,
            'body'  => $searchBody,
        ]);

        return $response->asArray();
    }

    /**
     * @throws Throwable
     */
    public function getAllDocuments(string $indexName): array
    {
        $response = $this->client->search([
            'index' => $indexName,
            'body'  => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ]);

        return $response->asArray();
    }
}
