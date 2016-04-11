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
    protected $params;

    function __construct($key)
    {
        $this->key = $key;
        $this->client = new Client();
        $this->params = [
            'country'   => '',
            'max_price' => '',
            'min_price' => '',
            'sort_by'   => 'date',
            'sort_type' => 'd',
            'offset'    => 0,
            'limit'     => 30
        ];
    }

    function properties(array $params = null)
    {
        $this->query = $this->prepareQuery($params);
        $crawler = $this->client->request('GET', $this->query);

        $info = $crawler->filter('div#listings_summary');

        $answer = new \stdClass();
        $answer->results = new \stdClass();
        $answer->results->pagination = $this->getPaginationInfo($info);

        $ads = [];
        var_dump($this->query);
        // get list of ads
        $crawler->filter('div.listing')->each(function (Crawler $node) use (&$ads) {

            $ad = new \stdClass();

            // ad_id
            $link = $node->filter('a')->first();
            $url = parse_url($link->attr('href'));
            parse_str($url['query'], $params);
            $ad->ad_id = intval($params['id']);

            // daft_url; in daft short format --> http://daft.ie/7 + ad_id
            $ad->daft_url = "http://daft.ie/7$ad->ad_id";

            // property_type
            $prop_type = $node->filter('h3')->first();

            // some of this fields are not present in all the overseas ads
            if ($prop_type->count() > 0) {
                $prop_type = explode(',', trim($prop_type->text()));

                if (count($prop_type) > 3) {
                    $ad->property_type = trim($prop_type[2]);
                }

                // bedrooms
                $bedrooms = explode(' ', $prop_type[0]);
                $ad->bedrooms = intval($bedrooms[0]);

                // bathrooms
                $bathrooms = explode(' ', $prop_type[1]);
                $ad->bathrooms = intval($bathrooms[0]);
            }

            // price
            $price = trim($node->filter('h2')->first()->text());
            $ad->display_price = $price;

            // address
            $address = trim($node->filter('div.listing_address h1')->text());
            $ad->address = $address;

            // short description
            $description = trim($node->filter('div.listing_description p')->text());
            $ad->description = $description;

            // image
            $image = $node->filter('a img.listing_thumbnail')->attr('src');
            $ad->thumbnail_url = $image;

            // adding item to array of results
            array_push($ads, $ad);
        });

        $answer->results->ads = $ads;

        return $answer;
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
        if (is_null($params)) {
            $params = [];
        }

        $params = array_merge($this->params, $params);

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

    private function getPaginationInfo(Crawler $info)
    {
        $result = new \stdClass();

        switch ($info->count()) {
            case 0 : // no results
                $result->total_results = 0;
                $result->results_per_page = 10; // (last item - first) + 1 => Items 21 -> 40 => 40-21+1 = 20 items.
                $result->num_pages = 0;
                $result->first_on_page = 0;
                $result->last_on_page = 0;
                $result->current_page = 0;
                break;

            case 1 :
                $aux = explode(PHP_EOL, $info->text());
                $info = array_pop($aux);
                $info = explode(' ', $info);

                $result->total_results = intval($info[4]);
                $result->results_per_page = intval($info[2]) - intval($info[0]) + 1; // (last item - first) + 1 => Items 21 -> 40 => 40-21+1 = 20 items.
                $result->num_pages = intval(ceil($result->total_results / $result->results_per_page));
                $result->first_on_page = intval($info[0]);
                $result->last_on_page = intval($info[2]);
                $result->current_page = intval(ceil($result->first_on_page / $result->results_per_page));
                break;
        }

        return $result;
    }
}