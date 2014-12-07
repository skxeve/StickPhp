<?php
namespace Stick\view;

use Stick\dao\Template;
use Stick\dao\DaoException;
use Stick\view\ViewException;
use Stick\lib\Error;

abstract class AbstractView extends \Stick\AbstractObject
{
    protected $param;
    protected $path;

    public function initialize()
    {
        if (defined('static::TEMPLATE_PATH')) {
            $this->setTemplate(static::TEMPLATE_PATH);
        }
    }

    public function setTemplate($path)
    {
        if (is_string($path)) {
            $this->path = $path;
        }
    }

    public function setParam(array $param)
    {
        if (is_array($this->param)) {
            $this->param = array_merge($this->param, $param);
        } else {
            $this->param = $param;
        }
    }

    public function getContent()
    {
        try {
            $t = new Template;
            $t->initialize($this->path);
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
            $this->getLogger()->warning(Error::catchExceptionMessage($e));
            return '';
        }
    }
}
