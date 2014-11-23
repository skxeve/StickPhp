<?php
namespace Stick\view;

use Stick\dao\Template;
use Stick\dao\DaoException;

class IndexView extends AbstractView
{
    const STICK_INDEX_TEMPLATE = 'Stick/template/index.inc';

    protected $path;

    public function init($path = null)
    {
        if ($path !== null && is_readable($path) && is_file($path)) {
            $this->path = $path;
        } else {
            $this->path = self::STICK_INDEX_TEMPLATE;
        }
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
}
