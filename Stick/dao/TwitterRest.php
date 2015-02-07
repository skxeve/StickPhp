<?php
namespace Stick\dao;

class TwitterRest extends AbstractTwitter
{
    protected $api_urls = array(
        'update'                => 'https://api.twitter.com/1.1/statuses/update.json',
        'home_timeline'         => 'https://api.twitter.com/1.1/statuses/home_timeline.json',
        'user_timeline'         => 'https://api.twitter.com/1.1/statuses/user_timeline.json',
        'rate_limit_status'     => 'https://api.twitter.com/1.1/application/rate_limit_status.json',
        'friendships_create'    => 'https://api.twitter.com/1.1/friendships/create.json',
        'directmessage_new'     => 'https://api.twitter.com/1.1/direct_messages/new.json',
        'retweet'               => 'https://api.twitter.com/1.1/statuses/retweet/%s.json',
        'statuses_show'         => 'https://api.twitter.com/1.1/statuses/show/%s.json',
        'users_show'            => 'https://api.twitter.com/1.1/users/show.json',
        'oauth_request_token'   => 'https://api.twitter.com/oauth/request_token',
        'oauth_access_token'    => 'https://api.twitter.com/oauth/access_token',
        'rate_limit'            => 'https://api.twitter.com/1.1/application/rate_limit_status.json',
        'friends_list'          => 'https://api.twitter.com/1.1/friends/list.json',
        'friends_ids'           => 'https://api.twitter.com/1.1/friends/ids.json',
    );

    protected $api_methods = array(
        'update'                => 'POST',
        'friendships_create'    => 'POST',
        'directmessage_new'     => 'POST',
        'retweet'               => 'POST',
        'oauth_request_token'   => 'POST',
        'oauth_access_token'    => 'POST',
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
            $header = $this->generateAuthHeader($url, $method, $param);
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

    public function showUser($screen_name)
    {
        return $this->execute(
            $this->api_urls['users_show'],
            'GET',
            array('screen_name' => $screen_name)
        );
    }

    public function homeTimeline($count = null, $since_id = null, $max_id = null)
    {
        $param = array();
        foreach (array('count', 'since_id', 'max_id') as $key) {
            if ($$key !== null) {
                $param[$key] = $$key;
            }
        }
        return $this->execute(
            $this->api_urls['home_timeline'],
            'GET',
            $param
        );
    }

    public function userTimeline($count = null, $since_id = null, $max_id = null)
    {
        $param = array();
        foreach (array('count', 'since_id', 'max_id') as $key) {
            if ($$key !== null) {
                $param[$key] = $$key;
            }
        }
        return $this->execute(
            $this->api_urls['user_timeline'],
            'GET',
            $param
        );
    }

    public function rateLimit()
    {
        return $this->execute(
            $this->api_urls['rate_limit'],
            'GET'
        );
    }

    public function getFriendsListAll($id = null)
    {
        $users = array();
        $cursor = null;
        do {
            list($status, $json) = $this->getFriendsList($id, $cursor);
            if (($status / 100) == 2) {
                $data = json_decode($json, true);
                $users = array_merge($users, $data['users']);
                $cursor = $data['next_cursor_str'];
            }
        } while (($status / 100) == 2 && $cursor != '0');
        return array($status, $users);
    }
    public function getFriendsIds($id = null, $cursor = null)
    {
        $param = $this->generateFriendsParam($id, $cursor);
        return $this->execute(
            $this->api_urls['friends_ids'],
            'GET',
            $param
        );
    }
    public function getFriendsList($id = null, $cursor = null, $count = 200)
    {
        $param = $this->generateFriendsParam($id, $cursor);
        $param['count'] = $count;
        return $this->execute(
            $this->api_urls['friends_list'],
            'GET',
            $param
        );
    }
    protected function generateFriendsParam($id, $cursor)
    {
        $param = array();
        if ($id !== null) {
            if (preg_match('/^[0-9]+$/', $id)) {
                $param['user_id'] = $id;
            } else {
                $param['screen_name'] = $id;
            }
        }
        if ($cursor !== null) {
            $param['cursor'] = $cursor;
        }
        return $param;
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

    public function oauthRequestToken($callback)
    {
        return $this->execute(
            $this->api_urls['oauth_request_token'],
            'POST',
            array('callback' => $callback)
        );
    }

    public function oauthAccessToken($verifier)
    {
        return $this->execute(
            $this->api_urls['oauth_access_token'],
            'POST',
            array('oauth_verifier' => $verifier)
        );
    }
}
