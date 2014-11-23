<?php
/**
 * PSR-0 autoloader
 */
spl_autoload_register(
    function ($c) {
        $ls = strrpos($c, '\\');
        if ($ls !== false) {
            $ls++;
        } else {
            $ls = 0;
        }
        $ns = substr($c, 0, $ls);
        $class = substr($c, $ls);
        $fullpath = stream_resolve_include_path(strtr($ns, '\\', '/') . strtr($class, '_', '/') . '.php');

        if ($fullpath) {
            require_once($fullpath);
        }
    }
);
