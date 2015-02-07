<?php
namespace Stick\log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class BaseLogger extends AbstractLogger
{
    protected static $level_array = array(
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
    const DEFAULT_FORMAT = "[%05d] %s [%s] %s";
    const DEFAULT_TRACE = 3;

    protected $pid;
    protected $path;
    protected $level;
    protected $date;
    protected $format;
    protected $trace;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize($config = array())
    {
        $this->pid      = getmypid();
        $this->path     = isset($config['path'])    ? $config['path']   : self::DEFAULT_PATH;
        $this->level    = isset($config['level'])   ? $config['level']  : self::DEFAULT_LEVEL;
        if (!is_numeric($this->level)) {
            $this->level = array_search(strtolower($this->level), self::$level_array);
            if ($this->level === false) {
                $this->level = 0;
            }
        }
        $this->date     = isset($config['date'])    ? $config['date']   : self::DEFAULT_DATE;
        $this->format   = isset($config['format'])  ? $config['format'] : self::DEFAULT_FORMAT;
        $this->trace    = isset($config['trace']) && preg_match('/^[0-9]+$/', $config['trace'])
        ? $config['trace'] : self::DEFAULT_TRACE;
    }

    public function log($level_str, $message, array $context = array())
    {
        $level_int = array_search($level_str, self::$level_array);
        if ($level_int === false) {
            $level_int = count(self::$level_array);
        }
        if ($level_int < $this->level) {
            return;
        }
        $trace_array = debug_backtrace(false);
        if (isset($trace_array[1]['file']) && isset($trace_array[1]['line'])) {
            $trace_file = $trace_array[1]['file'];
            $trace_line = $trace_array[1]['line'];
            $trace_pos = $trace_file . ':' . $trace_line;
        } else {
            $trace_pos = ':';
        }
        $line_array = explode("\n", preg_replace("/\r\n/", "\n", $message));
        for ($i = 0; $i < count($line_array); $i++) {
            $line = $line_array[$i];
            $log = sprintf(
                $this->format,
                $this->pid,
                date($this->date),
                strtoupper($level_str),
                $line
            );
            if (($i + 1) !== count($line_array)) {
                file_put_contents($this->path, $log . "\n", FILE_APPEND | LOCK_EX);
            } else {
                file_put_contents($this->path, $log . "\t" . '@ ' . $trace_pos . "\n", FILE_APPEND | LOCK_EX);
            }
        }
    }
}
