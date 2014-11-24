<?php
namespace Stick\view;

use Stick\dao\Template;
use Stick\dao\DaoException;
use Stick\view\ViewException;

abstract class AbstractView extends \Stick\AbstractObject
{
    protected $param;
    protected $path;

    public function setTemplate($path)
    {
        $this->path = $path;
    }

    public function setParam(array $param)
    {
        $this->param = $param;
    }

    public function getContent()
    {
        try {
            $t = new Template;
            $t->init($this->path);
            return $t->getValue($this->param);
        } catch (DaoException $e) {
            throw new ViewException('Catch DaoException: ' . $e->getMessage());
        }
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
