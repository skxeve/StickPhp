<?php
namespace Stick\view;

abstract class AbstractView extends \Stick\AbstractObject implements InterfaceView
{
    protected $param;

    public function setParam(array $param)
    {
        $this->param = $param;
    }

    public function __toString()
    {
        try {
            return $this->getContent();
        } catch (ViewException $e) {
            $this->getLogger()->warning($e->getMessage());
            return '';
        }
    }
}
