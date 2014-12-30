<?php
namespace Stick\dao;

use Stick\config\Config;

class Template extends \Stick\AbstractObject
{
    protected $root;
    protected $path;

    public function initialize($path = null, $section = null)
    {
        if ($path === null) {
            return;
        }
        $this->root = array('');
        $config = Config::get()->getConfig($this, $section);
        if (isset($config['root'])) {
            if (is_array($config['root'])) {
                $this->root = $config['root'];
            } else {
                $this->root = array($config['root']);
            }
            $this->root[] = '';
        }
        $this->path = null;
        foreach ($this->root as $root) {
            $fullpath = stream_resolve_include_path($root . $path);
            if ($fullpath === false) {
                $this->getLogger()->debug('Is not found '.var_export($root . $path, true));
                continue;
            }
            if (!is_readable($fullpath)) {
                $this->getLogger()->debug('Is not readable '.var_export($fullpath, true));
                continue;
            }
            if (!is_file($fullpath)) {
                $this->getLogger()->debug('Is not file '.var_export($fullpath, true));
                continue;
            }
            $this->path = $fullpath;
            break;
        }
        if ($this->path === null) {
            throw new DaoException('Failed to resolve path '.$path);
        }
    }

    public function getValue($param = null)
    {
        if (is_file($this->path)) {
            $this->getLogger()->debug('getTemplate '.$this->path);
            if (is_array($param)) {
                extract($param);
            }
            ob_start();
            ob_implicit_flush(0);
            include($this->path);
            return ob_get_clean();
        } else {
            throw new DaoException('Unexpected error '.var_export($this->path, true));
        }
    }
}
