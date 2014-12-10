<?php
namespace Stick\config;

use Stick\dao\BaseIni;
use Stick\lib\Error;
use Stick\lib\Validate;

class Config extends \Stick\AbstractSingletonObject
{
    protected $loaded = array();
    protected $data = array();

    protected function initialize()
    {
        if (defined('STICKPHP_CONFIG_PATH')) {
            $this->load(STICKPHP_CONFIG_PATH);
        }
    }

    /**
     * Load config ini
     * 
     * @param mixed $path Path of config ini
     * 
     * @throw ConfigException
     */
    public function load($path)
    {
        if (is_array($path)) {
            foreach ($path as $item) {
                $this->load($item);
            }
        } else {
            if (isset($this->data[$path])) {
                return true;
            }
            if (!is_readable($path)) {
                throw new ConfigException('Is not readable ' . var_export($path, true));
            }
            try {
                $ini = new BaseIni;
                $ini->initialize($path);
                $this->data[$path] = $ini;
                if (($inc = $ini->getValue('include')) !== false) {
                    $this->load($inc);
                }
            } catch (\Exception $e) {
                throw new ConfigException(Error::catchException($e));
            }
        }
    }

    /**
     * Get config data
     *
     * @param obj $obj instance
     * @param string $section Section name
     * @param boolean $ex Exception flag(default true)
     *
     * @return mixed
     */
    public function getConfig($obj = null, $section = null)
    {
        $prefix = null;
        if ($obj instanceof \Stick\log\Logger) {
            $prefix = 'logger';
        } elseif ($obj instanceof \Stick\dao\Template) {
            $prefix = 'template';
        } elseif ($obj instanceof \Stick\controller\AbstractController) {
            $prefix = 'controller';
        } elseif ($obj instanceof \Stick\dao\Database) {
            $prefix = 'db';
        } elseif (is_string($obj)) {
            $prefix = (string)$obj;
        }
        if ($prefix !== null) {
            return $this->findConfig($prefix, $section);
        }
        return false;
    }

    protected function findConfig($prefix, $section)
    {
        $key = $prefix;
        if (Validate::isEmpty($section) === false) {
            $key .= " $section";
        }
        $ret = null;
        foreach ($this->data as $path => $ini) {
            if (($ret = $ini->getValue($key)) !== false) {
                return $ret;
            }
        }
        return false;
    }
}
