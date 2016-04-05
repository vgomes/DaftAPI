<?php

namespace vgomes\DaftAPI;

interface DaftAPIInterface
{
    public function sale($id = null);
    public function rental($id = null);
    public function commercial($id = null);
    public function development($id = null);
    public function short_term($id = null);
    public function sharing($id = null);
    public function parking($id = null);
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