<?php

namespace vgomes\daftapi;

/**
 * Class to interact with Daft.ie
 * Class DaftAPI
 * @link http://api.daft.ie/doc/v3
 * @package vgomes\daftapi
 */
class DaftAPI implements DaftAPIInterface
{
    const SALE       = 'sale';
    const RENTAL     = 'rental';
    const SHARING    = 'sharing';
    const PARKING    = 'parking';
    const COMMERCIAL = 'commercial';
    const SHORTTERM  = 'shortterm';

    protected $api_key;
    protected $daftAPI;
    protected $params;
    protected $query;

    function __construct($api_key)
    {
        $this->api_key = $api_key;
        $this->daftAPI = new \SoapClient("http://api.daft.ie/v3/wsdl.xml", ['features' => SOAP_SINGLE_ELEMENT_ARRAYS]);
        $this->query = [];
    }

    /**
     * Performs a search for properties for sale.
     * @link http://api.daft.ie/doc/v3/#search_sale
     * @param array|null $params
     * @return mixed
     */
    public function sale(array $params = null)
    {
        return $this->daftAPI->search_sale([
            'api_key' => $this->api_key,
            'query'   => $params,
        ]);
    }

    /**
     * Performs a search for properties for rental.
     * @link http://api.daft.ie/doc/v3/#search_rental
     * @param array|null $params
     * @return mixed
     */
    public function rental(array $params = null)
    {
        return $this->daftAPI->search_rental([
            'api_key' => $this->api_key,
            'query'   => $params
        ]);
    }

    /**
     * Performs a search for commercial properties.
     * @link http://api.daft.ie/doc/v3/#search_commercial
     * @param array|null $params
     * @return mixed
     */
    public function commercial(array $params = null)
    {
        return $this->daftAPI->search_commercial([
            'api_key' => $this->api_key,
            'query'   => $params
        ]);
    }

    /**
     * Performs a search for new developments for sale.
     * @link http://api.daft.ie/doc/v3/#search_new_development
     * @param array|null $params
     * @return mixed
     */
    public function development(array $params = null)
    {
        return $this->daftAPI->search_development([
            'api_key' => $this->api_key,
            'query'   => $params
        ]);
    }

    /**
     * Performs a search for short-term rental properties.
     * @link http://api.daft.ie/doc/v3/#search_shortterm
     * @param array|null $params
     * @return mixed
     */
    public function short_term(array $params = null)
    {
        return $this->daftAPI->search_shortterm([
            'api_key' => $this->api_key,
            'query'   => $params
        ]);
    }

    /**
     * Performs a search for sharing properties.
     * @link http://api.daft.ie/doc/v3/#search_sharing
     * @param array|null $params
     * @return mixed
     */
    public function sharing(array $params = null)
    {
        return $this->daftAPI->search_sharing([
            'api_key' => $this->api_key,
            'query'   => $params
        ]);
    }

    /**
     * Performs a search for parking spaces
     * @link http://api.daft.ie/doc/v3/#search_parking
     * @param array|null $params
     * @return mixed
     */
    public function parking(array $params = null)
    {
        return $this->daftAPI->search_parking([
            'api_key' => $this->api_key,
            'query'   => $params
        ]);
    }

    /**
     * Look up areas, counties, postcodes, etc.
     * @link http://api.daft.ie/doc/v3/#areas
     * @param array|null $params
     * @return mixed
     */
    public function areas(array $params = null)
    {
        $params['api_key'] = $this->api_key;

        return $this->daftAPI->areas($params);
    }

    /**
     * Finds all the media for a property ad.
     * @link http://api.daft.ie/doc/v3/#media
     * @param $id
     * @param $type
     * @return mixed
     */
    public function media($id, $type)
    {
        return $this->daftAPI->media([
            'api_key' => $this->api_key,
            'ad_id'   => $id,
            'ad_type' => $type
        ]);
    }

    /**
     * Lists all the ad types and their associated alternative texts.
     * @link http://api.daft.ie/doc/v3/#ad_types
     * @return mixed
     */
    public function ad_types()
    {
        return $this->daftAPI->ad_types(['api_key' => $this->api_key]);
    }

    /**
     * Lists the property types for a particular ad type
     * @link http://api.daft.ie/doc/v3/#property_types
     * @param $type
     * @return mixed
     */
    public function property_types($type)
    {
        return $this->daftAPI->property_types([
            'api_key' => $this->api_key,
            'ad_type' => $type
        ]);
    }


    /**
     * List the predefined features for a particular ad type.
     * @link http://api.daft.ie/doc/v3/#features
     * @param $type
     * @return mixed
     */
    public function features($type)
    {
        return $this->daftAPI->features([
            'api_key' => $this->api_key,
            'ad_type' => $type
        ]);
    }


    /**
     * Returns info from the ad of the given url. It should work from any daft.ie url.
     * $images param controls if returned ad should include property media or not.
     * @param           $url
     * @param bool|true $images
     * @return mixed
     */
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
     * Converts any daft.ie url to its shortened link format.
     * @param $url
     * @return string
     */
    private function getDaftUniqueUrl($url)
    {
        $ad_id = $this->getDaftUniqueId($url);

        return "http://daft.ie/$ad_id";
    }

    /**
     * Returns daft unique ad_id and appended type digit. Used to build shortened daft urls.
     * @param $url
     * @return int
     */
    private function getDaftUniqueId($url)
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

    /**
     * Gets the type numeric type id to use in shortened urls.
     * @param $url
     * @return null|string
     */
    public function getTypeFromUrl($url)
    {
        $id = $this->getDaftUniqueId($url);
        $ad_id = intval(substr($id, 0, 1));
        $type = null;

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

    /**
     * Get an idividual ad given it's ad_id and type. $images param controls if property media should be included or not.
     * @param           $id
     * @param           $type
     * @param bool|true $images
     * @return mixed|null
     */
    public function getById($id, $type, $images = true)
    {
        $property = null;

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





}