<?php
namespace Stick\lib;

class Request extends \Stick\AbstractObject
{
    private function __construct()
    {
    }

    public static function getUri()
    {
        return array_shift(explode('?', getenv('REQUEST_URI')));
    }

    public static function getUriArray()
    {
        $uri = self::validateQuery(self::getUri());
        $uri_array = explode('/', $uri);
        array_shift($uri_array);
        return $uri_array;
    }

    public static function validateQuery($q)
    {
        return htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
    }
}
