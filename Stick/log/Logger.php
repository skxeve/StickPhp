<?php
namespace Stick\log;

use Stick\config\Config;

class Logger extends BaseLogger
{
    public static $filedate = 'Y_m_d';

    public function __construct($section = null)
    {
        try {
            $config = Config::get()->getConfig($this, $section);
        } catch (\Stick\config\ConfigException $e) {
            $config = false;
        }
        if ($config === false) {
            $config = array();
        }

        if (isset($config['path'])) {
            if (is_dir($config['path'])) {
                $config['path'] .= '/' . date(self::$filedate) . '.log';
            }

            $writable = false;

            if (preg_match('@^php://@', $config['path'])) {
                $writable = true;
            } elseif (is_writable($config['path'])) {
                $writable = true;
            } elseif (!file_exists($config['path']) && is_writable(dirname($config['path']))) {
                $writable = true;
            }

            if (!$writable) {
                unset($config['path']);
            }
        }

        $this->initialize($config);
    }
}
