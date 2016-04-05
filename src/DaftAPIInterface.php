<?php

namespace vgomes\daftapi;

interface DaftAPIInterface
{
    public function sale(array $params);
    public function rental(array $params);
    public function commercial(array $params);
    public function development(array $params);
    public function short_term(array $params);
    public function sharing(array $params);
    public function parking(array $params);
    public function areas();
    public function media($id, $type);
    public function ad_types();
    public function property_types($type);
    public function features();

    public function getById($id, $type, $images = true);
    public function getByUrl($url, $images = true);
    public function getDaftUniqueId($url);
    public function getDaftUniqueUrl($url);
    public function getAdIdFromUrl($url);
    public function getTypeFromUrl($url);
}