<?php
namespace Stick\dao;

use Stick\config\Config;

abstract class AbstractTwitter extends \Stick\AbstractObject
{
    protected $config;

    public function initialize($section = null)
    {
        $this->config = Config::get()->getConfig('twitter', $section);
        // Check
        foreach (array('consumer_key', 'consumer_secret', 'access_token', 'access_token_secret') as $key) {
            if (!isset($this->config[$key])) {
                throw new DaoException('Undefined config key ' . $key);
            }
        }
    }

    public function generateAuthHeader($url, $request_method = 'GET', $add_params = array())
    {
        $oauth_params = array(
            'oauth_consumer_key'     => $this->config['consumer_key'],
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp'        => time(),
            'oauth_version'          => '1.0',
            'oauth_nonce'            => md5(mt_rand()),
            'oauth_token'            => $this->config['access_token'],
        );
        $base = $oauth_params + $add_params;
        $key = array(
            $this->config['consumer_secret'],
            $this->config['access_token_secret'],
        );
        
        uksort($base, 'strnatcmp');
        $oauth_params['oauth_signature'] = base64_encode(
            hash_hmac(
                'sha1',
                implode(
                    '&',
                    array_map(
                        'rawurlencode',
                        array(
                            $request_method,
                            $url,
                            str_replace(
                                array('+', '%7E'),
                                array('%20', '~'),
                                http_build_query($base, '', '&')
                            )
                        )
                    )
                ),
                implode('&', array_map('rawurlencode', $key)),
                true
            )
        );
        foreach ($oauth_params as $name => $value) {
            $items[] = sprintf('%s="%s"', urlencode($name), urlencode($value));
        }
        $header = 'Authorization: OAuth ' . implode(', ', $items);
        return $header;
    }
}
