<?php
namespace Stick;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stick\config\Config;
use Stick\log\Logger;

abstract class AbstractCommonObject
{
    protected $psr3_logger;

    /**
     * Set psr-3 logger instance
     *
     * @param obj $logger LoggerInterface
     *
     * @return void
     */
    public function setLogger($logger = null, $stick_flag = true)
    {
        if ($logger instanceof LoggerInterface) {
            $this->psr3_logger = $logger;
        } elseif (!($this->psr3_logger instanceof LoggerInterface)) {
            if ($stick_flag === false) {
                $this->psr3_logger = new NullLogger;
            } elseif ($this instanceof Config) {
                $this->psr3_logger = new Logger;
            } else {
                $this->psr3_logger = Config::get()->getLogger();
            }
        }
    }

    /**
     * Get psr-3 logger instance
     *
     * @return obj
     */
    public function getLogger()
    {
        $this->setLogger();
        return $this->psr3_logger;
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
        file_put_contents(
            'php://stderr',
            'Warning: Force read cannot property ' . get_class($this) . '->' . $name . "\n"
        );
        return $this->$name;
    }
}
