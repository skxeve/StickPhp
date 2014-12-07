<?php
namespace Stick\dao;

class BaseIni extends \Stick\AbstractObject
{
    protected $path;
    protected $data;

    /**
     * Initialize
     *
     * @param string $path ini file path
     *
     * @throw DaoException
     */
    public function initialize($path = null)
    {
        if ($path !== null) {
            $this->path = $path;
            if (!is_readable($this->path)) {
                throw new DaoException('Is not readable ' . var_export($this->path, true));
            }
            $this->data = parse_ini_file($this->path, true);
            if ($this->data === false) {
                throw new DaoException('Failed to load ini ' . var_export($this->path, true));
            }
        }
        return $this;
    }

    /**
     * Get ini value
     */
    public function getValue($section, $name = null)
    {
        if (!isset($this->data[$section])) {
            return false;
        }
        if ($name === null) {
            return $this->data[$section];
        }
        if (isset($this->data[$section][$name])) {
            return $this->data[$section][$name];
        }
        return false;
    }
}
