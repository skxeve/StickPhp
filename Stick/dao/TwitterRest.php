<?php
namespace Stick\dao;

class TwitterRest extends AbstractTwitter
{
    protected $api_urls = array(
        'update'                => 'https://api.twitter.com/1.1/statuses/update.json',
        'user_timeline'         => 'https://api.twitter.com/1.1/statuses/user_timeline.json',
        'rate_limit_status'     => 'https://api.twitter.com/1.1/application/rate_limit_status.json',
        'friendships_create'    => 'https://api.twitter.com/1.1/friendships/create.json',
        'directmessage_new'     => 'https://api.twitter.com/1.1/direct_messages/new.json',
        'retweet'               => 'https://api.twitter.com/1.1/statuses/retweet/%s.json',
        'statuses_show'         => 'https://api.twitter.com/1.1/statuses/show/%s.json',
    );

    protected $api_methods = array(
        'update'                => 'POST',
        'friendships_create'    => 'POST',
        'directmessage_new'     => 'POST',
        'retweet'               => 'POST',
    );

    public function execute($url, $method = 'GET', $param = array())
    {
        $method = strtoupper($method);
        if ($method == 'POST') {
            $ch = new BaseHttp($url);
            $header = $this->generateAuthHeader($url, $method, $param);
            $options = array(
                CURLOPT_POST            => true,
                CURLOPT_POSTFIELDS      => http_build_query($param),
                CURLOPT_HTTPHEADER      => array($header),
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_ENCODING        => 'gzip',
            );
        } else { // GET
            $ch = new BaseHttp($url . '?' . http_build_query($param));
            $header = $this->generateAuthHeader($url, $method);
            $options = array(
                CURLOPT_HTTPHEADER      => array($header),
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_ENCODING        => 'gzip',
            );
        }
        $ch->setOptArray($options);
        $status = $ch->execute();
        return array($status, $ch->getContent());
    }

    public function update($status, $reply_to = null)
    {
        $this->getLogger()->info('Try to update ' . $status);
        return $this->execute(
            $this->api_urls['update'],
            'POST',
            array(
                'status'                => $status,
                'in_reply_to_status_id' => $reply_to,
            )
        );
    }

    public function directMessage($screen_name, $text)
    {
        $this->getLogger()->info('DM ' . $screen_name . ' ' . $text);
        return $this->execute(
            $this->api_urls['directmessage_new'],
            'POST',
            array(
                'text'          => $text,
                'screen_name'   => $screen_name,
            )
        );
    }

    public function retweet($id)
    {
        $this->getLogger()->info('Retweet ' . $id);
        $url = sprintf($this->api_urls['retweet'], $id);
        return $this->execute(
            $url,
            'POST'
        );
    }

    public function showStatuses($id)
    {
        $url = sprintf($this->api_urls['statuses_show'], $id);
        return $this->execute(
            $url,
            'GET'
        );
    }

    public function __call($name, array $param)
    {
        if (isset($this->api_urls[$name])) {
            $execute_param = array(
                $this->api_urls[$name],
                isset($this->api_methods[$name]) ? $this->api_methods[$name] : 'GET',
                isset($param[0]) ? $param[0] : array(),
            );
            return call_user_func_array(array($this, 'execute'), $execute_param);
        } else {
            throw new Exception('Undefined twitter api ' . $name);
        }
    }
}
