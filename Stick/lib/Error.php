<?php
namespace Stick\lib;

class Error extends \Stick\AbstractStatic
{
    public static function catchExceptionMessage(\Exception $e)
    {
        $m = 'Catch ' . get_class($e) . ' : ' . $e->getMessage();
        return $m;
    }
}
