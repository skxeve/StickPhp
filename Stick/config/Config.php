<?php
namespace Stick\config;

use Stick\dao\Ini;

class Config extends \Stick\AbstractObject
{
    protected static $instance = null;

    public static function get()
    {
        if (!(self::$instance instanceof Config)) {
            $config = new Config;
            $config->init();
            self::$instance = $config;
        }
        return self::$instance;
    }

    protected $loaded = array();
    protected $data = array();

    protected function __construct()
    {
    }

    protected function init()
    {
        if (defined('STICKPHP_CONFIG_INI_PATH')) {
            $this->load(STICKPHP_CONFIG_INI_PATH);
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
            if (!isset($this->data[$path])) {
                if (!is_readable($path)) {
                    throw new ConfigException('Is not readable ' . var_export($path, true));
                }
                $ini = new Ini;
                try {
                    $include = $ini->init($path)->getValue('include');
                } catch (\Stick\dao\DaoException $e) {
                    throw new ConfigException('Catch DaoException: ' . $e->getMessage());
                }
                $this->data[$path] = $ini;
                if (is_array($include)) {
                    foreach ($include as $item) {
                        $this->load($item);
                    }
                } else {
                    $this->load($include);
                }
            }
        }
        return $this;
    }

    /**
     * Get config data
     *
     * @param obj $obj instance
     * @param string $section Section name
     *
     * @throw ConfigException
     */
    public function getConfig($obj = null, $section = null)
    {
        $prefix = null;
        if ($obj instanceof \Stick\log\Logger) {
            $prefix = 'logger';
        } elseif (is_string($obj)) {
            $prefix = (string)$obj;
        }
        if ($prefix !== null) {
            return $this->findConfig($prefix, $section);
        }
        throw new ConfigException('Unexpected config parameter.');
    }

    protected function findConfig($prefix, $section)
    {
        $key = $prefix;
        if (!empty($section)) {
            $key .= " $section";
        }
        $ret = null;
        foreach($this->data as $path => $ini) {
            try {
                $ret = $ini->getValue($key);
                return $ret;
            } catch (\Stick\dao\DaoException $e) {
                $this->getLogger()->debug('Cannot find '.var_export($key, true).' in '.$path);
            }
        }
        throw new ConfigException('Cannot find config ' . var_export($key, true));
    }
}
