<?php
namespace Stick\lib;

class Validate extends \Stick\AbstractObject
{
    private function __construct()
    {
    }

    public static function query($q)
    {
        return htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
    }
}
