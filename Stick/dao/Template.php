<?php
namespace Stick\dao;

class Template extends \Stick\AbstractObject
{
    protected $path;

    public function init($path)
    {
        $fullpath = stream_resolve_include_path($path);
        if ($fullpath === false) {
            throw new DaoException('Is not found '.var_export($path, true));
        }
        if (!is_readable($fullpath)) {
            throw new DaoException('Is not readable '.var_export($fullpath, true));
        }
        if (!is_file($fullpath)) {
            throw new DaoException('Is not file '.var_export($fullpath, true));
        }
        $this->path = $fullpath;
    }

    public function getValue($param)
    {
        if (is_file($this->path)) {
            $this->getLogger()->debug('getTemplate '.$this->path);
            extract($param);
            ob_start();
            ob_implicit_flush(0);
            include($this->path);
            return ob_get_clean();
        } else {
            throw new DaoException('Unexpected error '.var_export($this->path, true));
        }
    }
}
