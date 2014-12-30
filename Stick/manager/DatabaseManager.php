<?php
namespace Stick\manager;

use Stick\dao\Database;

class DatabaseManager extends \Stick\AbstractObject
{
    protected $db;

    protected $model_array;

    public function initialize($mixed = null)
    {
        $this->model_array = array();
        if ($mixed instanceof Database) {
            $this->db = $mixed;
        } else {
            $this->db = new Database($mixed);
        }
    }

    public function begin()
    {
        return $this->beginTransaction();
    }
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollback()
    {
        return $this->db->rollback();
    }

    public function __get($model_name)
    {
        return $this->$model_name();
    }

    public function __call($model_name, $args)
    {
        if (!isset($this->model_array[$model_name])) {
            if (class_exists($model_name)) {
                $this->model_array[$model_name] = new $model_name($this->db);
            } else {
                throw new ManagerException('Not Found ' . $model_name);
            }
        }
        return $this->model_array[$model_name];
    }
}
