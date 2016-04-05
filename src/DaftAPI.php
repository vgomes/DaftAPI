<?php

namespace vgomes\DaftAPI;

class DaftAPI implements DaftAPIInterface
{
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
        // TODO: Implement ad_types() method.
    }

    public function property_types()
    {
        // TODO: Implement property_types() method.
    }

    public function features()
    {
        // TODO: Implement features() method.
    }
}