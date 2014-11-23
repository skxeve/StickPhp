<?php
namespace Stick\controller;

abstract class AbstractController extends \Stick\AbstractObject
{
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
