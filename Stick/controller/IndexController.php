<?php
namespace Stick\controller;

use Stick\manager\ViewManager;

class IndexController extends AbstractController
{
    protected $view;

    protected function preExecute()
    {
        parent::preExecute();
        $this->view = new ViewManager;
        $this->view->init();
    }

    protected function mainExecute()
    {
        parent::mainExecute();
    }

    protected function postExecute()
    {
        parent::postExecute();
        $this->view->execute();
    }
}
