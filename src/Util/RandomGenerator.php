<?php

namespace Saacsos\Randomgenerator\Util;

class RandomGenerator
{
    private $key = null;
    const TOKEN_LEVEL_NUMERIC       = 1;
    const TOKEN_LEVEL_HEXADECIMAL   = 2;
    const TOKEN_LEVEL_LOWERCASE     = 4;
    const TOKEN_LEVEL_UPPERCASE     = 8;
    const TOKEN_LEVEL_SPECIAL       = 16;
    private $token_level_chars = array(
        1 => '0123456789',
        2 => '0123456789abcdef',
        4 => 'abcdefghjkmnpqrstuvwxyz',
        8 => 'ABCDEFGHJKMNPQRSTUVWXYZ',
        16 => '!@#$%^&*()_=[]{}?,'  # do not change special characters
    );

    private $validate_token_level_chars = array(
        1 => '0123456789',
        2 => '0123456789abcdef',
        4 => 'abcdefghijklmnopqrstuvwxyz',
        8 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        16 => '!@#$%^&*()_=[]{}?,'  # do not change special characters
    );

    /**
     * RandomGenerator constructor.
     * @param null $key
     */
    public function __construct($key = null)
    {
        $this->key = $key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Random access token from key
     * @return string Hexa String
     */
    public function accessToken()
    {
        return $this->randomHexaDecimalString(48);
    }

    /**
     * @param $length
     * @return string
     */
    private function randomHexaDecimalString($length=16)
    {
        mt_srand((double)microtime() * 10000);
        $chars = strtolower(md5(uniqid(rand(), true) . $this->key));
        if ($length >= 32) {
            for ($i = 0; $i < floor($length / 32); $i++ ) {
                $chars .= strtolower(md5(uniqid(rand(), true) . $this->key));
            }
        }
        return substr($chars, 0, $length);
    }

    /**
     * @param int $level
     * @param int $length
     * @return string
     */
    public function password($level=13, $length=8)
    {
        return $this->random($level, $length);
    }

    public function random($level, $length)
    {
        $long_chars = '';
        $password_length = $length;
        $password = '';
        if ($level < 1)
            throw new \InvalidArgumentException("level ({$level}) must be integer greater than 0");
        if ($length < 1)
            throw new \InvalidArgumentException("length ({$length}) must be integer greater than 0");

        foreach ($this->token_level_chars as $level_key => $chars) {
            if (($level_key & $level) == $level_key and $password_length > 0) {
                $password .= $this->randomString($chars, 1);
                $long_chars .= $chars;
                $password_length--;
            }
        }

        if ($password_length > 0) {
            $password .= $this->randomString($long_chars, $password_length);
        }
        return str_shuffle($password);
    }

    /**
     * @param $chars
     * @param $length
     * @return string
     */
    private function randomString($chars, $length) {
        $chars_rand_max = strlen($chars) - 1;
        $string = array();
        for ($i = 0; $i < $length; $i++) {
            array_push($string, $chars[rand(0, $chars_rand_max)]);
        }
        return implode('', $string);
    }

    public function patternString($level, $min = 8, $max = null, $strict = false)
    {
        if (!empty($min) || !empty($max)) {
            $range = "{{$min},{$max}}";
        } else {
            $range = "+";
        }
//        $pattern_string = array(
//            13 => "/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]+){$range}$/"
//        );

        $string = "";
        foreach ($this->validate_token_level_chars as $level_key => $chars) {
            if ($level_key === self::TOKEN_LEVEL_SPECIAL) {
                $chars = preg_quote($chars);
            }

            if (($level_key & $level) == $level_key) {
                $string .= $chars;
            }
        }

        if ($strict) {
            $pattern_string = '/^';
            foreach ($this->validate_token_level_chars as $level_key => $chars) {
                if ($level_key === self::TOKEN_LEVEL_SPECIAL) {
                    $chars = preg_quote($chars);
                }
                $pattern_string .= '(?=.*[';
                $pattern_string .= $chars;
                $pattern_string .= '])';
            }
            $pattern_string .= '([' . $string . ']+)';
            $pattern_string .= $range;
            $pattern_string .= '$/';
        } else {
            $pattern_string = '/^'
                . '['
                . $string
                . ']'
                . $range
                . '$/';
        }

        return $pattern_string;
    }

    public function isMatch($string, $level, $min=8, $max=null, $strict=false) {
        return preg_match(
            $this->patternString($level, $min, $max, $strict),
            $string
        );
    }

}