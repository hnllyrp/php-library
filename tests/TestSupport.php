<?php

namespace Hnllyrp\PhpSupport\Tests;

use Hnllyrp\PhpSupport\Str;
use Hnllyrp\PhpSupport\Time;

class TestSupport
{

    public static function testStr()
    {
        $json = Str::arrToJson(['a' => 1]);
        $arr = Str::jsonToArr($json);
        return $arr;
    }

    public static function testTime()
    {
        Time::runtime('for-start');

        $arr = [];
        for ($i = 0; $i < 1000000; $i++) {
            $arr[] = $i;
        }

        Time::runtime('for-end');
        echo 'for循环运行时间' . Time::runtime('for-start', 'for-end');
    }
}
