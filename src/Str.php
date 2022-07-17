<?php

namespace Hnllyrp\PhpSupport;


class Str
{

    /**
     * json to array
     * @param string $str
     * @return mixed
     */
    public static function jsonToArr(string $str = '')
    {
        $arr = json_decode($str, true);
        return is_null($arr) ? $str : $arr;
    }

    /**
     * array to json
     * @param array $arr
     * @return false|string
     */
    public static function arrToJson(array $arr = [])
    {
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }
}
