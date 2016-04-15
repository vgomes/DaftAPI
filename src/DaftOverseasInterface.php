<?php

namespace vgomes\daftapi;

interface DaftOverseasInterface
{
    function properties(array $params = null);

    function property($id, $withImages = false);

    function media($id);
}