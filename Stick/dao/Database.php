<?php
namespace Stick\dao;

use Stick\config\Config;
use PDO;

class Database extends BaseDatabase
{
    public function __construct($section = null)
    {
        $config = Config::get()->getConfig($this, $section);
        if (!isset($config['user']) || !isset($config['pass'])) {
            throw new DaoException('Failed to load config, config = ' . var_export($config, true));
        }
        $db_engine = isset($config['engine'])   ? $config['engine'] : static::DEFAULT_DB_ENGINE;
        $db_host   = isset($config['host'])     ? $config['host']   : static::DEFAULT_DB_HOST;
        $db_port   = isset($config['port'])     ? $config['port']   : null;
        $db_name   = isset($config['dbname'])   ? $config['dbname'] : null;
        $db_char   = isset($config['char'])     ? $config['char']   : static::DEFAULT_DB_CHAR;
        $dsn = static::generateDsn($db_engine, $db_host, $db_port, $db_name, $db_char);
        $this->initialize($dsn, $config['user'], $config['pass']);
        $this->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    }
}
