<?php
namespace Stick\lib;

class Mailer extends \Stick\AbstractObject
{
    protected $encoding = "UTF-8";

    protected $default_from;
    protected $to;
    protected $from = "";
    protected $cc = "";
    protected $bcc = "";
    protected $subject = "";
    protected $body = "";

    public static $informations = array(
        'to',
        'from',
        'cc',
        'bcc',
        'subject',
        'body',
    );

    public function initialize($default_from = null)
    {
        $this->default_from = $default_from;
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    public function set(array $info)
    {
        foreach (self::$informations as $key) {
            if (isset($info[$key])) {
                $this->$key = $info[$key];
            }
        }
    }

    public function exec()
    {
        if (empty($this->from)) {
            $this->from = $this->default_from;
        }

        $this->checkRequires();

        $additional_headers = "From:{$this->from}";
        if (!empty($this->cc)) {
            $additional_headers .= "\r\nCc:{$this->cc}";
        }
        if (!empty($this->bcc)) {
            $additional_headers .= "\r\nBcc:{$this->bcc}";
        }

        $result = $this->sendMail($this->to, $this->subject, $this->body, $additional_headers);
        $param = array(
            'to'        => $this->to,
            'subject'   => $this->subject,
            'body'      => $this->body,
            'headers'   => $additional_headers,
        );
        if ($result) {
            $this->getLogger()->notice('Success to send mail, info = ' . var_export($param, true));
        } else {
            $this->getLogger()->warning('Failed to send mail, info = ' . var_export($param, true));
        }
        return $result;
    }

    protected function checkRequires()
    {
        if (empty($this->to)) {
            throw new Exception('Cannot find mailer info "To" '.var_export($this->to, true));
        }
        if (empty($this->from)) {
            throw new Exception('Cannot find mailer info "From" '.var_export($this->to, true));
        }
    }

    protected function sendMail($to, $subject, $body, $additional_headers)
    {
        if (function_exists("mb_send_mail") && !empty($this->encoding)) {
            mb_internal_encoding($this->encoding);
            return mb_send_mail($to, $subject, $body, $additional_headers);
        } elseif (function_exists("mail")) {
            return mail($to, $subject, $body, $additional_headers);
        } else {
            return false;
        }
    }
}
