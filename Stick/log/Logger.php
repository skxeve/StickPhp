<?php
namespace Stick\log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

use Stick\config\Config;

class Logger extends AbstractLogger
{
    protected static $instance = array();

    public static $filedate = 'Y_m_d';

    protected static $level = array(
        0 => LogLevel::DEBUG,
        1 => LogLevel::INFO,
        2 => LogLevel::NOTICE,
        3 => LogLevel::WARNING,
        4 => LogLevel::ERROR,
        5 => LogLevel::CRITICAL,
        6 => LogLevel::ALERT,
        7 => LogLevel::EMERGENCY,
    );

    const DEFAULT_PATH = 'php://stderr';
    const DEFAULT_LEVEL = 1;
    const DEFAULT_DATE = 'Y/m/d H:i:s';
    const DEFAULT_FORMAT = "[%05d] %s [%s] %s\n";

    public static function get($section = null)
    {
        if ($section === null) {
            $section = '';
        }
        if ($section === false) {
            $key = 'false';
        } else {
            $key = sha1($section);
        }

        if (!isset(self::$instance[$key])) {
            $logger = new Logger;

            if ($section === false) {
                $config = array(
                    'path'  => self::DEFAULT_PATH,
                    'level' => self::DEFAULT_LEVEL,
                );
            } else {
                try {
                    $config = Config::get()->getConfig($logger, $section);
                } catch (\Stick\config\ConfigException $e) {
                    self::get(false)->warning('Failed to get logger config '.var_export($section, true).' - '.$e->getMessage());
                    $config = null;
                }
            }

            $writable = false;
            if ($config !== null && isset($config['path'])) {
                $p = $config['path'];
                if (is_dir($p)) {
                    $path .= '/' . date(self::$filedate) . '.log';
                }

                if (preg_match('/^php:\/\//', $p)) {
                    $writable = true;
                } elseif (is_writable($p) || (!file_exists($p) && is_writable(dirname($p)))) {
                    $writable = true;
                }
            }
            if (!$writable) {
                return self::get(false);
            }

            $logger->init($config);
            self::$instance[$key] = $logger;
        }

        return self::$instance[$key];
    }

    protected $pid;
    protected $format;
    protected $date;
    protected $path;

    protected function __construct()
    {
    }

    protected function init($config)
    {
        $this->pid      = getmypid();
        $this->path     = isset($config['path'])    ? $config['path']   : null;
        $this->level    = isset($config['level'])   ? $config['level']  : self::DEFAULT_LEVEL;
        if (!is_numeric($this->level)) {
            $this->level = array_search(strtolower($this->level), self::$level);
            if ($this->level === false) {
                $this->level = 0;
            }
        }
        $this->date     = isset($config['date'])    ? $config['date']   : self::DEFAULT_DATE;
        $this->format   = isset($config['format'])  ? $config['format'] : self::DEFAULT_FORMAT;
    }

    public function log($level_str, $message, array $context = array())
    {
        $level_int = array_search($level_str, self::$level);
        if ($level_int === false) {
            $level_int = count(self::$level);
        }
        if ($level_int < $this->level) {
            return;
        }
        $trace = debug_backtrace();
        foreach (explode("\n", preg_replace("/\r\n/", "\n", $message)) as $line) {
            $log = sprintf(
                $this->format,
                $this->pid,
                date($this->date),
                strtoupper($level_str),
                $line
            );
            file_put_contents($this->path, $log, FILE_APPEND | LOCK_EX);
        }
    }
}
