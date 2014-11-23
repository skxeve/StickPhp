<?php
namespace Stick\controller;

use Stick\manager\ViewManager;

abstract class AbstractController extends \Stick\AbstractObject
{
    protected $view;

    public function setView($view)
    {
        if ($view instanceof ViewManager) {
            $this->view = $view;
        } else {
            if (is_object($view)) {
                throw new ControllerException('Unexpected instance '.get_class($view));
            } else {
                throw new ControllerException('Unexpected valuable '.var_export($view, true));
            }
        }
    }

    public function getView()
    {
        return $this->view;
    }

    final public function execute()
    {
        $this->getLogger()->info('Start execute '.get_class($this));
        try {
            $this->preExecute();
            $this->mainExecute();
            $this->postExecute();
        } catch (\Exception $e) {
            $this->getLogger()->warning('Catch ' . get_class($e) . ' : ' . $e->getMessage());
            $this->getView()->errorExecute();
        }
        $this->getLogger()->info('End execute '.get_class($this));
    }

    protected function preExecute()
    {
        $this->getLogger()->debug(get_class($this) . '->preExecute');
    }

    protected function mainExecute()
    {
        $this->getLogger()->debug(get_class($this) . '->mainExecute');
    }

    protected function postExecute()
    {
        $this->getLogger()->debug(get_class($this) . '->postExecute');
    }
}
