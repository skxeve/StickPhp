<?php
namespace Stick\dao;

class Http extends \Stick\AbstractObject
{
    protected static $opt_default = array(
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_TIMEOUT         => 3,
        CURLOPT_CONNECTTIMEOUT  => 3,
    );

    protected $ch;
    protected $url;
    protected $content;

    public function getCurl()
    {
        return $this->ch;
    }

    public function init($url)
    {
        $this->ch = curl_init($url);
        if ($this->ch === false) {
            throw new DaoException('Failed to open curl '.$url);
        }
        if ($this->setOptArray(self::$opt_default) === false) {
            throw new DaoException('Failed to set default options.');
        }
        $this->url = $url;
        $this->content = null;
    }

    public function setPost()
    {
        $this->getLogger()->info('Set POST method');
        return $this->setOpt(CURLOPT_POST, true);
    }

    public function setPostParameter($param)
    {
        $this->getLogger()->debug('Set POST parameter '.var_export($param, true));
        return $this->setOpt(CURLOPT_POSTFIELDS, $param);
    }

    public function execute()
    {
        $this->getLogger()->info('Execute curl '.$this->url);
        $content = curl_exec($this->getCurl());
        if ($content === false) {
            $this->getLogger()->warning('Failed curl, code ' . curl_errno($this->getCurl()));
            throw new DaoException(curl_error($this->getCurl()));
        }
        $this->content = $content;
        $status = $this->getHttpStatus();
        $this->getLogger()->info('Http Status code '.$status);
        $this->getLogger()->debug(var_export($content, true));
        return $status;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setOpt($option, $value)
    {
        $array = array(
            $option => $value,
        );
        return curl_setopt($this->getCurl(), $option, $value);
    }

    public function setOptArray(array $array)
    {
        return curl_setopt_array($this->getCurl(), $array);
    }

    public function getHttpStatus()
    {
        return $this->getInfo(CURLINFO_HTTP_CODE);
    }

    public function getInfo($option)
    {
        return curl_getinfo($this->getCurl(), $option);
    }

    public function __destruct()
    {
        curl_close($this->getCurl());
    }
}
