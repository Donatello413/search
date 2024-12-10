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
    public function deleteIndex($indexName): array
    {
        return $this->client->indices()->delete(['index' => $indexName])->asArray();
    }

    /**
     * @throws Throwable
     */
    public function bulkAddDocuments(string $indexName, array $documents): array
    {
        $params = ['body' => []];

        foreach ($documents as $document) {
            // Метаинформация.
            // Указывает, что делать (например, index, update, или delete) и дополнительные параметры (_index, _id и т. д.).
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
}
