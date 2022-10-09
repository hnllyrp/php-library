<?php

namespace Hnllyrp\PhpSupport;

/**
 * 正则表达式
 * Class RegExp
 * @package Hnllyrp\PhpSupport
 */
class RegExp
{

    /**
     * 密码必须为6-8位英文+数字
     * @param string $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function password($value = '', $min = 6, $max = 8)
    {
        $length = "\{$min, $max\}";
        return (bool)preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]' . $length . '$/i', $value);
    }

    /**
     * 匹配中文字符，数字，字母的正则表达式
     * @param string $value
     * @return bool
     */
    public static function chinese($value = '')
    {
        $reg = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u';

        return (bool)preg_match($reg, $value);
    }
}
