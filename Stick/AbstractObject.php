<?php
namespace Stick;

abstract class AbstractObject extends AbstractCommonObject
{
    const STICK_ABSTRACT_INITIALIZE = 'initialize';

    /**
     * If initialize method exists, call it.
     */
    public function __construct()
    {
        if (method_exists($this, static::STICK_ABSTRACT_INITIALIZE)) {
            call_user_func_array(array($this, static::STICK_ABSTRACT_INITIALIZE), func_get_args());
        }
    }
}
