<?php

namespace vgomes\DaftAPI;

class DaftAPI implements DaftAPIInterface
{
    protected $api_key;
    protected $daftAPI;
    protected $params;
    protected $query;

    const SALE       = 'sale';
    const RENTAL     = 'rental';
    const SHARING    = 'sharing';
    const PARKING    = 'parking';
    const COMMERCIAL = 'commercial';
    const SHORTTERM  = 'shortterm';

    function __construct($api_key)
    {
        $this->api_key = $api_key;
        $this->daftAPI = new \SoapClient("http://api.daft.ie/v3/wsdl.xml", ['features' => SOAP_SINGLE_ELEMENT_ARRAYS]);
        $this->query = [];
    }

    public function getById($id, $type, $images = true)
    {
        switch ($type) {
            case (self::SALE) :
                $property = $this->sale($id);
                break;

            case (self::RENTAL) :
                $property = $this->rental($id);
                break;

            case (self::SHARING) :
                $property = $this->sharing($id);
                break;

            case (self::PARKING) :
                $property = $this->parking($id);
                break;

            case (self::COMMERCIAL) :
                $property = $this->commercial($id);
                break;

            case (self::SHORTTERM) :
                $property = $this->short_term($id);
                break;
        }

        if ($images) {
            $media = $this->media($id, $type);
            $property->results->ads[0]->images = $media->media->images;
        }

        return $property;
    }

    public function getByUrl($url, $images = true)
    {
        // get short URL
        $url = $this->getDaftUniqueUrl($url);
        $ad_id = $this->getAdIdFromUrl($url);
        $type = $this->getTypeFromUrl($url);

        $property = $this->getById($ad_id, $type, $images);

        return $property;
    }

    /**
     * @param $url
     * @return string
     */
    public function getDaftUniqueUrl($url)
    {
        $ad_id = $this->getDaftUniqueId($url);

        return "http://daft.ie/$ad_id";
    }

    /**
     * Returns daft unique short id for any daft.ie, property.ie or rent.ie urls
     * @param $url
     * @return int
     */
    public function getDaftUniqueId($url)
    {
        $parsed = parse_url($url);
        $host = str_replace('www.', '', $parsed['host']);
        $path = trim($parsed['path'], '/');
        $arguments = explode('/', $path);
        $ad_id = end($arguments);

        switch ($host) {
            case ('daft.ie') :
                if (!is_numeric($ad_id)) { // normal url; numeric part is 7 chars long, missing type number
                    $type = '';

                    switch ($arguments[1]) { // analize url fragment speaking about type of property
                        case ('houses-for-sale') :
                        case ('apartments-for-sale'):
                        case ('duplexes-for-sale'):
                        case ('sites-for-sale'):
                            $type = 1;
                            break;

                        case ('houses-for-rent') :
                        case ('apartments-for-rent') :
                            $type = 2;
                            break;

                        case ('house-share') :
                            $type = 3;
                            break;

                        case ('parking-spaces-for-rent') :
                        case ('parking-spaces-for-sale') :
                            $type = 4;
                            break;

                        case ('commercial-property-for-rent') :
                        case ('commercial-property-for-sale') :
                        case ('commercial-property') :
                            $type = 5;
                            break;

                        case ('holiday-homes') :
                            $type = 6;
                            break;
                    }

                    $aux = explode('-', $ad_id);
                    $ad_id = $type . end($aux);
                }
                break;

            case ('rent.ie') :
                $type = "";

                switch ($arguments[0]) {
                    case ("houses-to-let") :
                    case ("short-lets") :
                        $type = "2";
                        break;

                    case ("rooms-to-rent"):
                        $type = "3";
                        break;

                    case ("parking-spaces"):
                        $type = "4";
                        break;

                    case ("holiday-homes"):
                        $type = "6";
                        break;
                }

                $ad_id = $type . $ad_id;
                break;

            case ("property.ie") :
                $type = "";

                switch ($arguments[0]) {
                    case ("property-for-sale") :
                        $type = "1";
                        break;

                    case ("property-to-let"):
                        $type = "2";
                        break;
                }

                $ad_id = $type . $ad_id;
                break;
        }

        return intval($ad_id);
    }


    /**
     * Gets the ad_id from any daft.ie url
     * @param $url
     * @return string
     */
    public function getAdIdFromUrl($url)
    {
        $id = $this->getDaftUniqueId($url);
        $ad_id = substr($id, 1);

        return $ad_id;
    }

    public function getTypeFromUrl($url)
    {
        $id = $this->getDaftUniqueId($url);
        $ad_id = intval(substr($id, 0, 1));

        switch ($ad_id) {
            case 1 :
                $type = self::SALE;
                break;

            case 2 :
                $type = self::RENTAL;
                break;

            case 3 :
                $type = self::SHARING;
                break;

            case 4 :
                $type = self::PARKING;
                break;

            case 5 :
                $type = self::COMMERCIAL;
                break;

            case 6 :
                $type = self::SHORTTERM;
                break;
        }

        return $type;
    }


    public function sale($id = null)
    {
        $this->query['ad_ids'] = $id;

        return $this->daftAPI->search_sale([
            'api_key' => $this->api_key,
            'query'   => $this->query
        ]);
    }

    public function rental($id = null)
    {
        $this->query['ad_ids'] = $id;

        return $this->daftAPI->search_rental([
            'api_key' => $this->api_key,
            'query'   => $this->query
        ]);
    }

    public function commercial($id = null)
    {
        $this->query['ad_ids'] = $id;

        return $this->daftAPI->search_commercial([
            'api_key' => $this->api_key,
            'query'   => $this->query
        ]);
    }

    public function development($id = null)
    {
        $this->query['ad_ids'] = $id;

        return $this->daftAPI->search_development([
            'api_key' => $this->api_key,
            'query'   => $this->query
        ]);
    }

    public function short_term($id = null)
    {
        $this->query['ad_ids'] = $id;

        return $this->daftAPI->search_shortterm([
            'api_key' => $this->api_key,
            'query'   => $this->query
        ]);
    }

    public function sharing($id = null)
    {
        $this->query['ad_ids'] = $id;

        return $this->daftAPI->search_sharing([
            'api_key' => $this->api_key,
            'query'   => $this->query
        ]);// TODO: Implement sharing() method.
    }

    public function parking($id = null)
    {
        $this->query['ad_ids'] = $id;

        return $this->daftAPI->search_parking([
            'api_key' => $this->api_key,
            'query'   => $this->query
        ]);
    }

    public function areas()
    {
        // TODO: Implement areas() method.
    }

    public function media($id, $type)
    {
        return $this->daftAPI->media([
            'api_key' => $this->api_key,
            'ad_id'   => $id,
            'ad_type' => $type
        ]);
    }

    public function ad_types()
    {
        return $this->daftAPI->ad_types(['api_key' => $this->api_key]);
    }

    public function property_types($type)
    {
        return $this->daftAPI->property_types([
            'api_key' => $this->api_key,
            'ad_type' => $type
        ]);
    }

    public function features()
    {
        // TODO: Implement features() method.
    }
}