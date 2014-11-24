<?php
namespace Stick\controller;

use Stick\manager\ViewManager;
use Stick\lib\CalcTime;

abstract class AbstractController extends \Stick\AbstractObject
{
    protected $view;
    protected $start_msec;
    protected $end_msec;

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
        $this->start_msec = microtime();
        $this->getLogger()->info('Start execute ' . get_class($this));
        try {
            $this->preExecute();
            $this->mainExecute();
            $this->postExecute();
        } catch (\Exception $e) {
            $this->getLogger()->error('Catch ' . get_class($e) . ' : ' . $e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
            $this->getView()->errorExecute();
        }
        $this->end_msec = microtime();
        $diff_msec = CalcTime::diffMicrotime($this->end_msec, $this->start_msec);
        $this->getLogger()->info('End execute ' . get_class($this) . ' - ' . $diff_msec . ' sec.');
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
