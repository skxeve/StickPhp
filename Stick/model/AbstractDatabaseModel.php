<?php
namespace Stick\model;

use Stick\dao\Database;

abstract class AbstractDatabaseModel extends \Stick\AbstractObject
{
    protected $db;

    public function initialize($mixed = null)
    {
        if ($mixed instanceof Database) {
            $this->getLogger()->debug('Set database object.');
            $this->db = $mixed;
        } else {
            $this->getLogger()->debug('Create database object, section ' . var_export($mixed, true));
            $this->db = new Database($mixed);
        }
    }

    public function getDatabase()
    {
        return $this->db;
    }
}
