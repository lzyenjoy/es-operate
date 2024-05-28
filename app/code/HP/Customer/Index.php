<?php

namespace HP\Customer;

use Psr\Http\Message\ResponseInterface;

class Index extends Common
{

    public function indexADocument()
    {
        $params = [
            'index' => 'my_index',
            'id' => 'my_id',
            'body' => ['testField' => 'abc']
        ];
        // Document will be indexed to my_index/_doc/my_id
        $response = $this->getClient()->index($params);
        print_r($response->asArray());
//        $params = [
//            'index'     => 'my_index',
//            'id'        => 'my_id',
//            'routing'   => 'company_xyz',
//            'timestamp' => strtotime("-1d"),
//            'body'      => [ 'testField' => 'abc']
//        ];
//
//
//        $response = $client$this->getClient()->index($params);
    }

    public function bulkIndexing(){
        for($i = 0; $i < 100; $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'my_index',
                ]
            ];

            $params['body'][] = [
                'my_field'     => 'my_value',
                'second_field' => 'some more values==='.$i
            ];
        }

        $responses = $this->getClient()->bulk($params);
        print_r($responses->asArray());
    }

    public function getDocument()
    {
        $params = [
            'index' => 'my_index',
            'id' => 'my_id'
        ];

        // Get doc at /my_index/_doc/my_id
        $response = $this->getClient()->get($params);
        print_r($response->asArray());
    }

    public function searchDocument()
    {
        $params = [
            'index' => 'my_index',
            'body' => [
                'query' => [
                    'match' => [
                        'testField' => 'abc'
                    ]
                ]
            ]
        ];

        $response = $this->getClient()->search($params);
        print_r($response->asArray());
    }

    public function deleteDocument()
    {
        $params = [
            'index' => 'my_index',
            'id' => 'my_id'
        ];
        // Delete doc at /my_index/_doc_/my_id
        $response = $this->getClient()->delete($params);
        print_r($response->asArray());
    }

    public function updateDocument()
    {
        $params = [
            'index' => 'my_index',
            'id' => 'my_id',
            'body' => [
                'doc' => [
                    'new_field' => '111111111'
                ]
            ]
        ];

        // Update doc at /my_index/_doc/my_id
        $response = $this->getClient()->update($params);
        print_r($response->asArray());
    }

    public function deleteIndex()
    {
        $deleteParams = [
            'index' => 'my_index'
        ];
        $response = $this->getClient()->indices()->delete($deleteParams);
        print_r($response->asArray());

    }

    public function createIndex()
    {
        $params = [
            'index' => 'my_index',
            'body' => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0
                ]
            ]
        ];

        $response = $this->getClient()->indices()->create($params);
        print_r($response->asArray());

    }

    public function updateIndexConfigured(){
        $params = [
            'index' => 'my_index',
            'body' => [
                'settings' => [
                    'number_of_replicas' => 0,
                    'refresh_interval' => -1
                ]
            ]
        ];

        $response = $this->getClient()->indices()->putSettings($params);
        print_r($response->asArray());
    }

    public function getIndexConfigured(){
        // Get settings for one index
        $params = ['index' => 'my_index'];
        $response = $this->getClient()->indices()->getSettings($params);
        print_r($response->asArray());
// Get settings for several indices
//        $params = [
//            'index' => [ 'my_index', 'my_index2' ]
//        ];
//        $response = $this->getClient()->indices()->getSettings($params);
    }

    public function updateIndexMappings(){
        // Set the index and type
        $params = [
            'index' => 'my_index',
            'body' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'first_name' => [
                        'type' => 'text',
                        'analyzer' => 'standard'
                    ],
                    'age' => [
                        'type' => 'integer'
                    ]
                ]
            ]
        ];
// Update the index mapping
        $this->getClient()->indices()->putMapping($params);
    }

    public function getIndexMappings(){
        // Get mappings for all indices
        $response = $this->getClient()->indices()->getMapping();

// Get mappings in 'my_index'
        $params = ['index' => 'my_index'];
        $response = $this->getClient()->indices()->getMapping($params);

// Get mappings for two indices
        $params = [
            'index' => [ 'my_index', 'my_index2' ]
        ];
        $response = $this->getClient()->indices()->getMapping($params);
    }

    public function getInfo()
    {
        $response = $this->getClient()->info();
        echo $response->getStatusCode(); // 200
        echo (string)$response->getBody(); // Response body in JSON
    }

    public function asyncClient()
    {
        $this->getClient()->setAsync(true);
        $promise = [];
        for ($i = 0; $i < 10; $i++) {
            $promise = $this->getClient()->index([
                'index' => 'async-index',
                'body' => [
                    'foo' => base64_encode(random_bytes(24))
                ]
            ]);
            $result = $promise->wait();
            print_r($result->asArray());
        }

//        $promise->then(
//        // The success callback
//            function (ResponseInterface $response) {
//                // Success
//                // insert here the logic for managing the response
//                return $response;
//            },
//            // The failure callback
//            function (\Exception $exception) {
//                // Error
//                throw $exception;
//            }
//        );

    }

    public function getNamespace()
    {
        // Index Stats
// Corresponds to curl -XGET localhost:9200/_stats
        $response = $this->getClient()->indices()->stats();

// Node Stats
// Corresponds to curl -XGET localhost:9200/_nodes/stats
        $response = $this->getClient()->nodes()->stats();

// Cluster Stats
// Corresponds to curl -XGET localhost:9200/_cluster/stats
        $response = $this->getClient()->cluster()->stats();
        // Corresponds to curl -XGET localhost:9200/my_index/_stats
        $params['index'] = 'my_index';
        $response = $this->getClient()->indices()->stats($params);
    }

    public function updateAlias(){
        $params['body'] = [
            'actions' => [
                [
                    'add' => [
                        'index' => 'async-index',
                        'alias' => 'async-index-bk'
                    ]
                ]
            ]
        ];
        $this->getClient()->indices()->updateAliases($params);
    }
}