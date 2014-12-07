<?php
namespace Stick;

abstract class AbstractObject extends AbstractCommonObject
{
    /**
     * If initialize method exists, call it.
     */
    public function __construct()
    {
        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }
}
