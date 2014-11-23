<?php
namespace Stick;

use Stick\log\Logger;

abstract class AbstractObject
{
    /**
     * Get psr-3 logger instance
     *
     * @param string $section Target section to use logging in config.
     *
     * @return obj
     */
    protected function getLogger($section = null)
    {
        return Logger::get($section);
    }

    /**
     * Cannot set undefined property.
     */
    public function __set($name, $value)
    {
        throw new StickException('Undefined class property ' . get_class($this) . '->' . $name);
    }

    /**
     * Print warning to get cannnot read property
     */
    public function __get($name)
    {
        file_put_contents('php://stderr', 'Warning: Force read cannot property ' . get_class($this) . '->' . $name . "\n");
        return $this->$name;
    }
}
