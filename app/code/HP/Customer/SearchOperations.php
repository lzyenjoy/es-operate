<?php

namespace HP\Customer;

use HP\Customer\Common;

class SearchOperations extends Common
{

    public function searchIndex()
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

        $results = $this->getClient()->search($params);
        print_r($results->asArray());
        $milliseconds = $results['took'];
        $maxScore = $results['hits']['max_score'];

        $score = $results['hits']['hits'][0]['_score'];
        $doc = $results['hits']['hits'][0]['_source'];
        echo '$milliseconds ' . $milliseconds . PHP_EOL;
        echo '$maxScore ' . $maxScore . PHP_EOL;
        echo '$score ' . $score . PHP_EOL;
        echo '$doc ' . print_r($doc);
    }

    public function boolQuery()
    {
        $params = [
            'index' => 'my_index',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['testField' => 'abc']],
                            ['match' => ['new_field' => '111111111']],
                        ]
                    ]
                ]
            ]
        ];

        $results = $this->getClient()->search($params);
        print_r($results->asArray());
    }

    public function scrollingQuery()
    {
        $params = [
            'scroll' => '30s',          // how long between scroll requests. should be small!
            'size' => 50,             // how many results *per shard* you want back
            'index' => 'my_index',
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];

// Execute the search
// The response will contain the first batch of documents
// and a scroll_id
        $response = $this->getClient()->search($params);

// Now we loop until the scroll "cursors" are exhausted
        while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {

            // **
            // Do your work here, on the $response['hits']['hits'] array
            // **

            // When done, get the new scroll_id
            // You must always refresh your _scroll_id!  It can change sometimes
            $scroll_id = $response['_scroll_id'];

            // Execute a Scroll request and repeat
            $response = $this->getClient()->scroll([
                'body' => [
                    'scroll_id' => $scroll_id,  //...using our previously obtained _scroll_id
                    'scroll' => '30s'        // and the same timeout window
                ]
            ]);
            print_r($response->asArray());
        }
    }
}