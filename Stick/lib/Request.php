<?php
namespace Stick\lib;

class Request extends \Stick\AbstractObject
{
    private function __construct()
    {
    }

    public static function getUri()
    {
        return getenv('REQUEST_URI');
    }

    public static function getUriArray()
    {
        $uri = Validate::query(self::getUri());
        $uri_array = explode('/', array_shift(explode('?', $uri)));
        array_shift($uri_array);
        return $uri_array;
    }
}
