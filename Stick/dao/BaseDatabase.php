<?php
namespace Stick\dao;

use Stick\lib\Error;
use PDO;

class BaseDatabase extends \Stick\AbstractObject
{
    const DEFAULT_DB_ENGINE = 'mysql';
    const DEFAULT_DB_HOST = 'localhost';
    const DEFAULT_DB_CHAR = 'utf8';

    protected $pdo;

    public function initialize(
        $user = null,
        $pass = null,
        $db_engine = null,
        $db_host = null,
        $db_port = null,
        $db_name = null,
        $db_char = null
    ) {
        if ($user === null || $pass === null) {
            return;
        }
        $dsn = static::generateDsn($db_engine, $db_host, $db_port, $db_name, $db_char);
        try {
            $this->pdo = new PDO($dsn, $user, $pass);
        } catch (\Exception $e) {
            throw new DaoException(Error::catchExceptionMessage($e));
        }
    }

    public function setAttribute($attribute, $value)
    {
        return $this->pdo->setAttribute($attribute, $value);
    }

    public static function generateDsn(
        $db_engine = null,
        $db_host = null,
        $db_port = null,
        $db_name = null,
        $db_char = null
    ) {
        $db_engine = ($db_engine === null)  ? static::DEFAULT_DB_ENGINE : $db_engine;
        $db_host   = ($db_host === null)    ? static::DEFAULT_DB_HOST   : $db_host;
        $db_char   = ($db_char === null)    ? static::DEFAULT_DB_CHAR   : $db_char;

        $dsn = $db_engine . ':host=' . $db_host;
        if (!empty($db_port)) {
            $dsn .= ';port=' . $db_port;
        }
        if (!empty($db_name)) {
            $dsn .= ';dbname=' . $db_name;
        }
        if (!empty($db_char)) {
            $dsn .= ';charset=' . $db_char;
        }
        return $dsn;
    }

    public function getValue($sql, array $param = array())
    {
        $flag = null;
        $pdo_statement = $this->execute($sql, $param, $flag);
        if ($flag === true) {
            try {
                return $pdo_statement->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                Logger::warning(Error::catchExceptionMessage($e));
                return false;
            }
        } else {
            return false;
        }
    }

    public function setValue($sql, array $param = array())
    {
        $flag = null;
        $pdo_statement = $this->execute($sql, $param, $flag);
        return $flag;
    }

    protected function execute($sql, array $param, &$flag)
    {
        $flag = false;
        $pdo_statement = null;
        $this->getLogger()->debug($sql);
        $this->getLogger()->debug(var_export($param, true));
        try {
            $pdo_statement = $this->pdo->prepare($sql);
            foreach ($param as $key => $value) {
                if (strpos($key, '_') === 0) {
                    continue;
                }
                $key = ':' . trim($key, ':');
                if ($this->bindValue($pdo_statement, $key, $value) === false) {
                    $this->getLogger()->warning('Failed to bind value, ' . $key . ' => ' . var_export($value, true));
                }
            }
            $this->getLogger()->debug('End bind value, todo execute.');
            $flag = $pdo_statement->execute();
            if ($flag === false) {
                throw new DaoException('Failed to execute sql. errorinfo = ' . var_export($pdo_statement->errorInfo(), true));
            }
        } catch (\Exception $e) {
            $this->getLogger()->warning(Error::catchExceptionMessage($e));
            $flag = false;
        }
        return $pdo_statement;
    }

    protected function bindValue($pdo_statement, $key, $value)
    {
        $ret = false;

        if (is_int($value)) {
            $this->getLogger()->debug('Bind int ' . $key . ' => ' . $value);
            $ret = $pdo_statement->bindParam($key, $value, PDO::PARAM_INT);
        } elseif (is_bool($value)) {
            $this->getLogger()->debug('Bind bool ' . $key . ' => ' . var_export($value, true));
            $ret = $pdo_statement->bindParam($key, $value, PDO::PARAM_BOOL);
        } elseif ($value === null) {
            $this->getLogger()->debug('Bind null ' . $key);
            $ret = $pdo_statement->bindParam($key, $value, PDO::PARAM_NULL);
        } else {
            $this->getLogger()->debug('Bind string ' . $key . ' => ' . var_export($value, true));
            $ret = $pdo_statement->bindParam($key, $value, PDO::PARAM_STR);
        }

        return $ret;
    }

    public function begin()
    {
        return $this->beginTransaction();
    }

    public function beginTransaction()
    {
        $this->getLogger()->debug('Database beginTransaction.');
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->getLogger()->debug('Database commit.');
        return $this->pdo->commit();
    }

    public function rollback()
    {
        $this->getLogger()->debug('Database rollback.');
        return $this->pdo->rollBack();
    }
}
