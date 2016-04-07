<?php

namespace vgomes\daftapi;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class DaftOverseas implements DaftOverseasInterface
{
    const BULGARIA           = 6;
    const BRAZIL             = 73;
    const CAPE_VERDE         = 71;
    const CARIBBEAN_ISLANDS  = 55;
    const DOMINICAN_REPUBLIC = 173;
    const DUBAI              = 67;
    const EGYPT              = 82;
    const ENGLAND            = 190;
    const FRANCE             = 2;
    const GERMANY            = 21;
    const GREECE             = 24;
    const INDIA              = 64;
    const ITALY              = 11;
    const JORDAN             = 116;
    const MALAYSIA           = 83;
    const MEXICO             = 128;
    const MONTENEGRO         = 32;
    const MOROCCO            = 70;
    const PANAMA             = 48;
    const PORTUGAL           = 9;
    const QATAR              = 119;
    const ROMANIA            = 35;
    const SAUDI_ARABIA       = 91;
    const SCOTLAND           = 191;
    const SENEGAL            = 155;
    const SPAIN              = 1;
    const THAILAND           = 68;
    const TUNISIA            = 75;
    const TURKEY             = 22;
    const USA                = 3;
    const VENEZUELA          = 130;
    const WALES              = 192;

    protected $key;
    protected $query;
    protected $client;

    function __construct($key)
    {
        $this->key = $key;
        $this->client = new Client();
    }

    function properties(array $params = null)
    {
        $this->query = $this->prepareQuery($params);
        /** @var Crawler $crawler */
        $crawler = $this->client->request('GET', $this->query);

        $crawler->filter('div.listing')->each(function (Crawler $node) {
            $node->filter('h1')->each(function (Crawler $node) {
                var_dump($node->text());
            });
        });
    }

    function property($id)
    {
        // TODO: Implement property() method.
    }

    function media($id)
    {
        // TODO: Implement media() method.
    }

    protected function prepareQuery(array $params = null)
    {
        $default = [
            'country'   => '',
            'max_price' => '',
            'min_price' => '',
            'sort_by'   => 'date',
            'sort_type' => 'd',
            'offset'    => 0,
            'limit'     => 30
        ];

        if (is_null($params)) {
            $params = [];
        }

        $params = array_merge($default, $params);

        $query = [
            's'      => [
                'country_id'  => $params['country'], // country
                'mxp'         => $params['max_price'], // maximum price
                'mnp'         => $params['min_price'], // minimum price
                'search_type' => 'international_sale', // search type
                "sort_by"     => $params['sort_by'], // sorting method [ price, date ]
                "sort_type"   => $params['sort_type'] // 'a' ascending, 'd' descending
            ],
            'key'    => $this->key,
            'offset' => $params['offset'],
            'limit'  => $params['limit']
        ];

        $url = 'http://agent.daft.ie/search.daft?' . http_build_query($query);
        return $url;
    }
}