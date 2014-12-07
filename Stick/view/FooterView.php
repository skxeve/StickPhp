<?php
namespace Stick\view;

class FooterView extends AbstractView
{
    const TEMPLATE_PATH = 'Stick/template/footer.inc';

    public function setJs(array $js)
    {
        $this->setParam(array('js' => $js));
    }
}
