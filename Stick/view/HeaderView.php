<?php
namespace Stick\view;

class HeaderView extends AbstractView
{
    const TEMPLATE_PATH = 'Stick/template/header.inc';

    public function setCss(array $css)
    {
        $this->setParam(array('css' => $css));
    }
    
    public function setJs(array $js)
    {
        $this->setParam(array('js' => $js));
    }
}
