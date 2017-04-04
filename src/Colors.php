<?php

namespace tubalmartin\CssMin;

class Colors
{
    public static function getHexToNamedMap()
    {
        return include 'data/hex-to-named-color-map.php';
    }

    public static function getNamedToHexMap()
    {
        return include 'data/named-to-hex-color-map.php';
    }
}
