<?php
namespace Stick\dao;

use Stick\config\Config;
use Stick\config\ConfigException;

class Template extends \Stick\AbstractObject
{
    protected $root;
    protected $path;

    public function init($path, $section = null)
    {
        $this->root = '';
        try {
            $config = Config::get()->getConfig($this);
            if (isset($config['root'])) {
                $this->root = $config['root'];
            }
        } catch (ConfigException $e) {
            $this->getLogger()->debug('Catch ConfigException: ' . $e->getMessage());
        }
        $fullpath = stream_resolve_include_path($this->root . $path);
        if ($fullpath === false) {
            throw new DaoException('Is not found '.var_export($this->root . $path, true));
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
