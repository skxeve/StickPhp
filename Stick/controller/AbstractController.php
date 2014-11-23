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

    public function getView($view)
    {
        return $this->view;
    }

    public function execute()
    {
        $this->preExecute();
        $this->mainExecute();
        $this->postExecute();
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
