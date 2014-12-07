<?php
namespace Stick\lib;

class Request extends \Stick\AbstractStatic
{
    public static function getGet()
    {
        return $_GET;
    }

    public static function getPost()
    {
        return $_POST;
    }

    public static function getUri()
    {
        return array_shift(explode('?', getenv('REQUEST_URI')));
    }

    public static function getUriArray($n = null)
    {
        $uri_array = explode('/', self::getUri());
        array_shift($uri_array);
        if ($n === null) {
            return $uri_array;
        } elseif (isset($uri_array[$n])) {
            return $uri_array[$n];
        } else {
            return null;
        }
    }
}
