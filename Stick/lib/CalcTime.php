<?php
namespace Stick\lib;

class CalcTime
{
    protected function __construct()
    {
    }

    public static function diffMicrotime($a, $b)
    {
        list($am, $at) = explode(' ', $a);
        list($bm, $bt) = explode(' ', $b);
        return ((float)$am-(float)$bm) + ((float)$at-(float)$bt);
    }
}
