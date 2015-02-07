<?php
namespace Stick\lib;

class Validate extends \Stick\AbstractStatic
{
    public static function queryToArray($query)
    {
        $query_sets = explode('&', $query);
        $query_array = array();
        foreach ($query_sets as $item) {
            list($key, $value) = explode('=', $item, 2);
            if (!isset($query_array[$key])) {
                $query_array[$key] = $value;
            } else {
                if (is_array($query_array[$key])) {
                    $query_array[$key][] = $value;
                } else {
                    $i = $query_array[$key];
                    $query_array[$key] = array(
                        $i,
                        $value,
                    );
                }
            }
        }
        return $query_array;
    }

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
