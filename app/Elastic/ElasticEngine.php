<?php

namespace App\Elastic;

use Elastic\Elasticsearch\Client;
use Throwable;

class ElasticEngine
{
    public function __construct(
        protected Client $client,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function createIndex(string $indexName, array $mapping = []): array
    {
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
    public function existsIndex(string $indexName): bool
    {
        return $this->client->indices()->exists(['index' => $indexName])->asBool();
    }

    /**
     * @throws Throwable
     */
    public function deleteIndex(string $indexName): array
    {
        return $this->client->indices()->delete(['index' => $indexName])->asArray();
    }

    /**
     * @throws Throwable
     */
    public function createDocuments(string $indexName, array $documents): array
    {
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
    public function updateDocument(string $indexName, int $documentId, array $documentData): array
    {
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
    public function createDocument(string $indexName, int $documentId, array $documentData): array
    {
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
    public function deleteDocument(string $indexName, int $documentId,): array
    {
        $params = [
            'index' => $indexName,
            'id' => $documentId,
        ];

        return $this->client->delete($params)->asArray();
    }

    /**
     * @throws Throwable
     */
    public function clearIndex(string $indexName): array
    {
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
}
