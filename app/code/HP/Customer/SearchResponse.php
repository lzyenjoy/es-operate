<?php

namespace HP\Customer;

use Elastic\Elasticsearch\Helper\Iterators\SearchHitIterator;
use Elastic\Elasticsearch\Helper\Iterators\SearchResponseIterator;

class SearchResponse extends Common
{

    public function searchResponse()
    {
        $search_params = [
            'scroll' => '5m', // period to retain the search context
            'index' => 'my_index', // here the index name
            'size' => 100, // 100 results per page
            'body' => [
                'query' => [
                    'match_all' => new \StdClass() // {} in JSON
                ]
            ]
        ];
// $client is Elasticsearch\Client instance
        $pages = new SearchResponseIterator($this->getClient(), $search_params);

// Sample usage of iterating over page results
        foreach ($pages as $page) {
            print_r($page);
            // do something with hit e.g. copy its data to another index
            // e.g. prints the number of document per page (100)
            echo count($page['hits']['hits']), PHP_EOL;
        }
    }

    public function searchHit(){
        $search_params = [
            'scroll'      => '5m', // period to retain the search context
            'index'       => 'my_index', // here the index name
            'size'        => 100, // 100 results per page
            'body'        => [
                'query' => [
                    'match_all' => new \StdClass() // {} in JSON
                ]
            ]
        ];
// $client is Elasticsearch\Client instance
        $pages = new SearchResponseIterator($this->getClient(), $search_params);
        $hits = new SearchHitIterator($pages);

// Sample usage of iterating over hits
        foreach($hits as $hit) {
            // do something with hit e.g. write to CSV, update a database, etc
            // e.g. prints the document id
            echo $hit['_id'], PHP_EOL;
        }
    }
}