<?php
namespace Stick;

abstract class AbstractSingletonObject extends AbstractCommonObject
{
    protected static $instance = null;

    /**
     * If initialize method exists, call it.
     */
    final protected function __construct()
    {
        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }

    /**
     * Get own instance
     */
    final public static function get()
    {
        $class_name = get_called_class();
        if (!(self::$instance instanceof AbstractSingletonObject)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    final protected function __clone()
    {
        throw new StickException('Cannot clone, this is Singleton design pattern.');
    }
}
