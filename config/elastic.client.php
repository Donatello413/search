<?php declare(strict_types=1);

return [
    'default' => env('ELASTIC_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                env('ELASTICSEARCH_HOST', 'localhost:9200'),
            ],
            'basicAuthentication' => [
                env('ELASTICSEARCH_USER'),
                env('ELASTICSEARCH_PASSWORD'),
            ],
        ],
    ],
];
