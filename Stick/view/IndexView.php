<?php
namespace Stick\view;

use Stick\dao\Template;
use Stick\dao\DaoException;

class IndexView extends AbstractView
{
    const STICK_INDEX_TEMPLATE = 'Stick/template/index.inc';

    public function initialize($path = null)
    {
        if ($path !== null && is_readable($path) && is_file($path)) {
            $this->setTemplate($path);
        } else {
            $this->setTemplate(self::STICK_INDEX_TEMPLATE);
        }
    }
}
