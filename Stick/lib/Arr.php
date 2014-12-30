<?php
namespace Stick\lib;

class Arr extends \Stick\AbstractStatic
{
    public static function getUniqueKeys(array $array, $num = 1)
    {
        if (count($array) < $num) {
            return array_keys($array);
        }
        $keys = array();
        while (count($keys) < $num) {
            $r = array_rand($array);
            if (!in_array($r, $keys)) {
                $keys[] = $r;
            }
        }
        return $keys;
    }
}
