<?php

namespace vgomes\daftapi;

interface DaftOverseasInterface
{
    function properties(array $params = null);

    function property($id);

    function media($id);
}