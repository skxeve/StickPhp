<?php
namespace Stick\controller;

use Stick\manager\ViewManager;

class IndexController extends AbstractController
{
    protected function preExecute()
    {
        parent::preExecute();
        $view = new ViewManager;
        $view->init();
        $this->setView($view);
    }

    protected function mainExecute()
    {
        parent::mainExecute();
    }

    protected function postExecute()
    {
        parent::postExecute();
        $this->getView()->execute();
    }
}
