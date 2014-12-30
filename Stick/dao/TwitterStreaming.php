<?php
namespace Stick\dao;

use Stick\config\Config;
use Stick\lib\Error;

class TwitterStreaming extends AbstractTwitter
{
    protected static $twitter_stream_url = array(
        'user'  => 'https://userstream.twitter.com/1.1/user.json',
        'site'  => 'https://sitestream.twitter.com/1.1/site.json',
    );

    protected $buffer;
    protected $last_status_log;
    protected $last_status_mes;

    const STATUSLOG_PER_SEC = 300;

    public function initialize($section = null)
    {
        parent::initialize($section);

        $this->buffer = '';
        $this->last_status_log = 0;
        $this->last_status_mes = '';
    }

    public function getCallback($ex = null)
    {
        $obj = $this;
        return function ($ch, $str) use (&$obj, &$ex) {
            return $obj->executeCallback($ch, $str, $ex);
        };
    }

    public function executeCallback($ch, $str, $ex)
    {
        $this->buffer .= $str;

        // Buffer is line
        if ($this->buffer[strlen($this->buffer) - 1] === "\n") {
            $info = curl_getinfo($ch);
            $status_mes = 'Status[' . $info['http_code'] . '] Length[' . strlen($this->buffer) . ']';
            if ($this->last_status_mes !== $status_mes
            || ($this->last_status_log < (time() - static::STATUSLOG_PER_SEC))) {
                $this->getLogger()->info($status_mes);
                $this->last_status_mes = $status_mes;
                $this->last_status_log = time();
            }
            // Cannot json_decode
            if (null === $json = json_decode($this->buffer, true)) {
                if ($this->buffer !== "\n" && $this->buffer !== "\r\n") {
                    throw new DaoException('Unexpected data ' . var_export($this->buffer, true), $info['http_code']);
                } elseif ($info['http_code'] != 200) {
                    throw new DaoException(
                        'Response not 200 status code. buffer = ' . var_export($this->buffer, true),
                        $info['http_code']
                    );
                }
            } else {
                if (is_object($ex) && method_exists($ex, 'execute')) {
                    $ex->execute($json);
                } else {
                    $this->getLogger()->info(var_export($json, true));
                }
            }
            $this->buffer = '';
        }
    
        // 追記された長さを返す
        return strlen($str);
    }

    public function execute($type = 'user', $ex = null)
    {
        if (isset(static::$twitter_stream_url[$type])) {
            $url = static::$twitter_stream_url[$type];
        } else {
            $url = $type;
        }
        $header = $this->generateAuthHeader($url);
        $callback = $this->getCallback($ex);

        $ch = curl_init();
        $options = array(
            CURLOPT_URL             => $url,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_HTTPHEADER      => array($header),
            CURLOPT_ENCODING        => 'gzip',
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_WRITEFUNCTION   => $callback,
        );
        curl_setopt_array($ch, $options);

        $this->getLogger()->info('Streaming ' . $url);
        try {
            curl_exec($ch);
        } catch (\Exception $e) {
            $this->getLogger()->info(Error::catchExceptionMessage($e));
            $this->getLogger()->info('CURL ERROR = ' . curl_error($ch));
        }
    }
}
