<?php

namespace Saacsos\Randomgenerator\Util;

class RandomGenerator
{
    private $key = null;
    private $string = null;
    private $length = 8;
    private $level = 13;
    private $min = 1;
    private $max = null;
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

    public function get()
    {
        return $this->string;
    }

    public function getString()
    {
        return $this->string;
    }

    public function key($key)
    {
        $this->setKey($key);
        return $this;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function length($length)
    {
        $this->setLength($length);
        return $this;
    }

    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    public function level($level)
    {
        $this->setLevel($level);
        return $this;
    }

    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    public function min($min)
    {
        $this->setMin($min);
        return $this;
    }

    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    public function max($max)
    {
        $this->setMax($max);
        return $this;
    }

    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * Random access token from key
     * @return string Hexa String
     */
    public function accessToken()
    {
        $this->string = $this->length(48)->randomHexaDecimalString()->get();
        return $this;
    }

    /**
     * @param $length
     * @return string
     */
    private function randomHexaDecimalString()
    {
        mt_srand((double)microtime() * 10000);
        $chars = strtolower(md5(uniqid(rand(), true) . $this->key));
        if ($this->length >= 32) {
            for ($i = 0; $i < floor($this->length / 32); $i++ ) {
                $chars .= strtolower(md5(uniqid(rand(), true) . $this->key));
            }
        }
        $this->string = substr($chars, 0, $this->length);
        return $this;
    }

    /**
     * @param int $level
     * @param int $length
     * @return string
     */
    public function password()
    {
        $this->string = $this->random()->get();
        return $this;
    }

    public function random()
    {
        $long_chars = '';
        $password_length = $this->length;
        $password = '';
        if ($this->level < 1)
            throw new \InvalidArgumentException("level ({$this->level}) must be integer greater than 0");
        if ($this->length < 1)
            throw new \InvalidArgumentException("length ({$this->length}) must be integer greater than 0");

        foreach ($this->token_level_chars as $level_key => $chars) {
            if (($level_key & $this->level) == $level_key and $password_length > 0) {
                $password .= $this->randomString($chars, 1);
                $long_chars .= $chars;
                $password_length--;
            }
        }

        if ($password_length > 0) {
            $password .= $this->randomString($long_chars, $password_length);
        }
        $this->string = str_shuffle($password);
        return $this;
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

    public function patternString($strict = false)
    {
        if (!empty($this->min) || !empty($this->max)) {
            $range = "{{$this->min},{$this->max}}";
        } else {
            $range = "+";
        }

        $string = "";
        foreach ($this->validate_token_level_chars as $level_key => $chars) {
            if ($level_key === self::TOKEN_LEVEL_SPECIAL) {
                $chars = preg_quote($chars);
            }

            if (($level_key & $this->level) == $level_key) {
                $string .= $chars;
            }
        }

        if ($strict) {
            $pattern_string = '/^';
            foreach ($this->validate_token_level_chars as $level_key => $chars) {
                if ($level_key === self::TOKEN_LEVEL_SPECIAL) {
                    $chars = preg_quote($chars);
                }

                if (($level_key & $this->level) == $level_key) {
                    $pattern_string .= '(?=.*[';
                    $pattern_string .= $chars;
                    $pattern_string .= '])';
                }
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

    public function isMatch($string, $strict=false) {
        return preg_match(
            $this->patternString($strict),
            $string
        );
    }

}