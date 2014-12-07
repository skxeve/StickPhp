<?php
namespace Stick\controller;

use Stick\config\Config;
use Stick\manager\ViewManager;
use Stick\lib\Request;

class IndexController extends AbstractController
{
    const CONTROLLER_CLASS_FOOTER = 'Controller';
    const NOTFOUND_CONTROLLER_CLASS = 'NotFound';

    protected $generate_controller = false;

    public function setGenerate($flag = true)
    {
        $this->generate_controller = $flag;
    }

    protected function preExecute()
    {
        parent::preExecute();
        $view = new ViewManager;
        $view->initialize();
        $this->setView($view);
    }

    protected function mainExecute()
    {
        parent::mainExecute();
        $this->getLogger()->info('Request URI = ' . Request::getUri());
        if (count(Request::getGet()) > 0) {
            $this->getLogger()->info('Request GET = ' . var_export(Request::getGet(), true));
        }
        if (count(Request::getPost()) > 0) {
            $this->getLogger()->info('Request POST keys = ' . implode(', ', array_keys(Request::getPost())));
            $this->getLogger()->debug('Request POST = ' . var_export(Request::getPost(), true));
        }

        $config = Config::get()->getConfig($this, null, false);
        $controller_name = self::findControllerClass($config);
        
        if ($controller_name !== null) {
            $this->getLogger()->info('Find controller ' . $controller_name);
            $controller_instance = new $controller_name;
            $controller_instance->setView($this->getView());
            $controller_instance->execute();
        } else {
            throw new ControllerException('Cannot find controller');
        }
    }

    protected function postExecute()
    {
        parent::postExecute();
        $this->getView()->execute();
    }

    protected function findControllerClass($config)
    {
        $controller_space = self::generateControllerSpace($config);
        $controller_name = self::generateControllerName($config);
        return self::searchControllerClass($controller_space, $controller_name);
    }

    protected function generateControllerSpace($config)
    {
        $controller_namespace = array();
        if (isset($config['root'])) {
            if (is_array($config['root'])) {
                $controller_namespace = $config['root'];
            } else {
                $controller_namespace = array($config['root']);
            }
        }
        $controller_namespace[] = '\\';
        return $controller_namespace;
    }

    protected function generateControllerName($config)
    {
        $uri = Request::getUri();
        if (is_array($config)) {
            foreach ($config as $key => $item) {
                if (!preg_match("/^\//", $key)) {
                    continue;
                }
                $preg = preg_replace('/\//', '\\/', $key);
                $preg = preg_replace('/%/', '(\\w+)', $preg);
                $preg = "/^" . $preg . "\/?$/";
                if (preg_match($preg, $uri)) {
                    return array($item);
                }
            }
        }
        $class_list = array();
        if ($this->generate_controller) {
            $uri_array = Request::getUriArray();
            for ($i = count($uri_array); $i >= 0; $i--) {
                $name = '';
                for ($j = 0; $j < $i; $j++) {
                    $name .= ucfirst($uri_array[$j]);
                    if (($j + 1) !== $i) {
                        $name .= '\\';
                    }
                }
                if (!empty($name)) {
                    $class_list[] = $name;
                }
            }
            if ($uri === '/') {
                $class_list[] = 'Home';
            }
        }
        return $class_list;
    }

    protected function searchControllerClass(array $controller_space, array $controller_name)
    {
        for ($i = 0; $i < count($controller_name); $i++) {
            foreach ($controller_space as $ns) {
                $c = $ns . $controller_name[$i] . self::CONTROLLER_CLASS_FOOTER;
                if (class_exists($c)) {
                    $this->getLogger()->debug('Found ' . $c);
                    return $c;
                } else {
                    $this->getLogger()->debug('Not found ' . $c);
                }
            }
        }
        $notfound_controller_name = array(
            self::NOTFOUND_CONTROLLER_CLASS,
        );
        if ($controller_name !== $notfound_controller_name) {
            return $this->SearchControllerClass($controller_space, $notfound_controller_name);
        }
        return null;
    }
}
