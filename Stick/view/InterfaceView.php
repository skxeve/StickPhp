<?php
namespace Stick\view;

interface InterfaceView
{
    public function setParam(array $param);
    public function getContent();
    public function __toString();
}
