<?php
namespace Stick\manager;

use Stick\view\IndexView;

class ViewManager extends \Stick\AbstractObject
{
    const CHAR = 'UTF-8';

    protected $view;
    protected $header_string;
    protected $http_status;
    protected $param_flag;
    protected $execute_flag;

    public function init()
    {
        $this->view = array();
        $this->setContentType('text/html');
        $this->setHttpStatus(200);
        $this->enableParam(false);
        $this->execute_flag = false;
    }

    public function enableParam($flag)
    {
        $this->param_flag = $flag;
    }

    public function setLocation($url)
    {
        $this->setContentString("Location: $url");
    }

    public function setContentType($type)
    {
        $string = 'Content-type: ';
        switch ($type) {
            case 'json':
                $string .= 'application/' . $type . '; charset=' . self::CHAR;
                break;
            case 'html':
            case 'xml':
                $string .= 'text/' . $type . '; charset=' . self::CHAR;
                break;
            case 'text/html':
            case 'text/xml':
            case 'text/plain':
            case 'application/json':
                $string .= $type . '; charset=' . self::CHAR;
                break;
            default:
                $string .= $type;
                break;
        }
        $this->setContentString($string);
    }

    public function setContentString($string)
    {
        $this->header_string = $string;
    }

    public function setHttpStatus($code)
    {
        $this->http_status = $code;
        if ($code != 200) {
            $this->enableParam(true);
        }
    }

    public function setView($name, $content)
    {
        $this->view[$name] = $content;
    }

    public function getView($name)
    {
        if (isset($this->view[$name])) {
            return $this->view[$name];
        }
        throw new ManagerException('Cannot find view '.var_export($name, true));
    }

    public function execute()
    {
        if ($this->execute_flag) {
            $this->getLogger()->warning('Duplicate execute ' . get_class($this));
            return;
        }
        $this->execute_flag = true;
        // Header
        if ($this->param_flag) {
            header($this->header_string, true, $this->http_status);
        } else {
            header($this->header_string);
        }

        // View
        if (isset($this->view['index'])) {
            $index = $this->getView('index');
        } else {
            $index = new IndexView();
            $index->init();
        }
        $param = array();
        foreach ($this->view as $key => $view) {
            $param[$key] = (string)$view;
        }
        $index->setParam($param);
        echo (string)$index;
    }

    public function errorExecute()
    {
        $this->getLogger()->warning('Start ' . __METHOD__);
        if ($this->execute_flag) {
            $this->getLogger()->warning('Duplicate execute ' . get_class($this));
            return;
        }
        $this->execute_flag = true;
        echo <<<EOM
<html>
<body>
<h1>Error</h1>
<p>Unexpected error, please contact administrator.</p>
</body>
</html>
EOM;
    }
}
