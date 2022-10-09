<?php

namespace Hnllyrp\PhpSupport\Library;


/**
 * 版本检测类
 * 当前版本大于新版本 Version::check( '1.20.63.56' , '1.20.63.55.56' )===1;
 * 当前版本等于新版本 Version::check( '1.20.63.56' , '1.20.63.056' )===0;
 * 当前版本小于新版本 Version::check( '1.20.62.56' , '1.20.63.056' )===-1;
 * 当前版本大于新版本 Version::gt( '1.20.63.56' , '1.20.63.55.56' )===true;
 * 当前版本等于新版本 Version::eq( '1.20.63.56' , '1.20.63.056' )===true;
 * 当前版本小于新版本 Version::lt( '1.20.62.56' , '1.20.63.056' )===true;
 */
class Version
{

    /**
     * 当前版本大于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function gt(string $current, string $new)
    {
        return self::check($current, $new) === 1;
    }

    /**
     * 版本检测
     *
     * @param string $current
     * @param string $new
     * @return int
     */
    public static function check(string $current = '', string $new = '')
    {
        if ($current == $new) {
            return 0;
        }

        $current = explode(".", ltrim($current, 'v'));
        $new = explode(".", ltrim($new, 'v'));

        foreach ($current as $k => $cur) {
            if (isset($new[$k])) {
                if ($cur < $new[$k]) {
                    return -1;
                }

                if ($cur > $new[$k]) {
                    return 1;
                }
            } else {
                return 1;
            }
        }

        return count($new) == count($current) ? 0 : -1;
    }

    /**
     * 当前版本大于或等于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function egt(string $current, string $new)
    {
        $res = self::check($current, $new);

        return $res === 1 || $res === 0;
    }

    /**
     * 当前版本等于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function eq(string $current, string $new)
    {
        return self::check($current, $new) === 0;
    }

    /**
     * 当前版本小于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function lt(string $current, string $new)
    {
        return self::check($current, $new) === -1;
    }

    /**
     * 当前版本小于或等于新版本
     *
     * @param string $current
     * @param string $new
     * @return bool
     */
    public static function elt(string $current, string $new)
    {
        $res = self::check($current, $new);

        return $res === -1 || $res === 0;
    }

}

