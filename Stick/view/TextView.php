<?php
namespace Stick\view;

use Stick\dao\DaoException;

class TextView extends AbstractView
{
    protected $text = '';

    public function setText($text = null)
    {
        if (is_string($text)) {
            $this->text = (string)$text;
        }
    }

    public function getContent()
    {
        return $this->text;
    }
}
