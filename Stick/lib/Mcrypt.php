<?php
namespace Stick\lib;

class Mcrypt
{
    const ALGO = 'blowfish';
    const MODE = 'ecb';

    public static function encrypt($str, $pass, $algo = null, $mode = null)
    {
        try {
            $td = static::init($pass, $algo, $mode);
            $encrypted = mcrypt_generic($td, $str);
            static::deinit($td);
            return base64_encode($encrypted);
        } catch (LibException $e) {
            return false;
        }
    }

    public static function decrypt($str, $pass, $algo = null, $mode = null)
    {
        try {
            $td = static::init($pass, $algo, $mode);
            $decrypted = rtrim(mdecrypt_generic($td, base64_decode($str)));
            static::deinit($td);
            return $decrypted;
        } catch (LibException $e) {
            return false;
        }
    }

    protected static function init($pass, $algo = null, $mode = null)
    {
        if ($algo === null) {
            $algo = static::ALGO;
        }
        if ($mode === null) {
            $mode = static::MODE;
        }

        $td = mcrypt_module_open($algo, '', $mode,'');
        if ($td === false) {
            throw new LibException('Failed to open mcrypt module.');
        }
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = substr(md5(microtime()), 0, $iv_size);
        //$iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_RANDOM);

        if ($iv_size !== strlen($iv)) {
            throw new LibException('Incorrect iv size.');
        }

        $key = substr(
            sha1($pass),
            0,
            mcrypt_enc_get_key_size($td)
        );

        $ret = mcrypt_generic_init($td, $key, $iv);
        if ($ret === false || $ret < 0) {
            throw new LibException('Error initialize mcrypt generic => ' . var_export($ret, true));
        }
        return $td;
    }

    protected static function deinit(&$td)
    {
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
    }
}
