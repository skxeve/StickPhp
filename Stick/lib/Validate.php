<?php
namespace Stick\lib;

class Validate extends \Stick\AbstractStatic
{
    public static function isEmpty($v)
    {
        if ($v === null || $v === '' || $v === false) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateQuery($q)
    {
        return htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
    }
}
