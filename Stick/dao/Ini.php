<?php
namespace Stick\dao;

class Ini extends \Stick\AbstractObject
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
    public function init($path)
    {
        $this->path = $path;
        if (!is_readable($this->path)) {
            throw new DaoException('Is not readable ' . var_export($this->path, true));
        }
        $this->data = parse_ini_file($this->path, true);
        if ($this->data === false) {
            throw new DaoException('Failed to load ini ' . var_export($this->path, true));
        }
        return $this;
    }

    /**
     * Get ini value
     */
    public function getValue($section, $name = null)
    {
        if (!isset($this->data[$section])) {
            throw new DaoException('Not found '.$section.' in '.$this->path);
        }
        if ($name === null) {
            return $this->data[$section];
        }
        if (isset($this->data[$section][$name])) {
            return $this->data[$section][$name];
        }
        throw new DaoException('Not found '.$section.'->'.$name.' in '.$this->path);
    }
}
