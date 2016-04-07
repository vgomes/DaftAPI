<?php

namespace vgomes\daftapi;

interface DaftAPIInterface
{
    public function sale(array $params = null);

    public function rental(array $params = null);

    public function commercial(array $params = null);

    public function development(array $params = null);

    public function short_term(array $params = null);

    public function sharing(array $params = null);

    public function parking(array $params = null);

    public function areas(array $params = null);

    public function media($id, $type);

    public function ad_types();

    public function property_types($type);

    public function features($type);
}