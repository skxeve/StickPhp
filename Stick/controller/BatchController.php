<?php
namespace Stick\controller;

use Stick\config\Config;

class BatchController extends AbstractController
{
    protected $controller;

    public function initialize($controller = null)
    {
        if ($controller instanceof AbstractController) {
            $this->controller = $controller;
        }
    }

    protected function preExecute()
    {
        parent::preExecute();
        declare(ticks = 1);
        pcntl_signal(SIGTERM, array($this, 'catchSignal'));
        pcntl_signal(SIGINT, array($this, 'catchSignal'));
    }

    protected function mainExecute()
    {
        parent::mainExecute();
        if ($this->controller instanceof AbstractController) {
            $this->controller->execute();
        } else {
            throw new ControllerException('Cannot find controller');
        }
        unset($this->controller);
    }

    public function catchSignal($signo = 0)
    {
        switch ($signo) {
            case 0:
                $this->getLogger()->notice('Catch return signal 0');
                return;
            case SIGINT:
                $this->getLogger()->notice('Catch signal SIGINT(CTRL+C)');
                break;
            case SIGTERM:
                $this->getLogger()->notice('Catch signal SIGTERM(KILL)');
                break;
            default:
                $this->getLogger()->notice('Catch unexpected signal ' . var_export($signo, true));
        }
        if (method_exists($this->controller, 'catchSignal')) {
            $this->controller->catchSignal($signo);
        }
        // run destruct
        unset($this->controller);
        $this->getLogger()->info('Terminated execute ' . get_class($this));
        exit(2);
    }
}
