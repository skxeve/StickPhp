<?php
namespace Stick\view;

class HeaderView extends AbstractView
{
    const TEMPLATE_PATH = 'Stick/template/header.inc';

    public function setCss(array $css)
    {
        $this->setParam(array('css' => $css));
    }
}
