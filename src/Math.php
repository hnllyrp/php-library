<?php

namespace Hnllyrp\PhpSupport;

/**
 * 高精度计算类
 * Class Math
 */
class Math
{
    /**
     * 验证值 在最小值与最大值之间
     * @param int $value
     * @param int $min
     * @param int $max
     * @return mixed
     */
    public static function between($value = 0, int $min = 0, int $max = 0)
    {
        $item = max($min, $value);
        return min($max, $item);
    }

    /**
     * 某些场景计算的结果 误差不能太大，比如计算扣税金额 乘以百分比，一般误差都不能大于0.01，所以需要高精度计算结果。
     * https://m.php.cn/article/390252.html
     * http://www.manongjc.com/article/25200.html
     */

    /**
     * 精确加法
     * @param $a
     * @param $b
     * @param int $scale
     * @return string
     */
    public static function bc_add($a, $b, int $scale = 2)
    {
        return bcadd($a, $b, $scale);
    }

    /**
     * 精确减法
     * @param $a
     * @param $b
     * @param int $scale
     * @return string
     */
    public static function bc_sub($a, $b, int $scale = 2)
    {
        return bcsub($a, $b, $scale);
    }

    /**
     * 精确乘法
     * @param $a
     * @param $b
     * @param int $scale
     * @return string
     */
    public static function bc_mul($a, $b, int $scale = 2)
    {
        return bcmul($a, $b, $scale);
    }

    /**
     * 精确除法
     * @param $a
     * @param $b
     * @param int $scale
     * @return string
     */
    public static function bc_div($a, $b, int $scale = 2)
    {
        return bcdiv($a, $b, $scale);
    }

    /**
     * 精确求余/取模
     * @param $a
     * @param $b
     * @return string
     */
    public static function bc_mod($a, $b)
    {
        return bcmod($a, $b);
    }

    /**
     * 比较两个任意精度的数字
     * @param $a
     * @param $b
     * @param int $scale
     * @return int 大于 返回 1 等于返回 0 小于返回 -1
     */
    public static function bc_comp($a, $b, int $scale = 5)
    {
        return bccomp($a, $b, $scale); // 比较到小数点位数
    }

    /**
     * 计算百分比
     * @param $a
     * @param $b
     * @param int $scale 精确到小数点位数
     * @return float|int
     */
    public static function bc_percent($a, $b, int $scale = 2)
    {
        $percent = bcdiv($a, $b, $scale);
        return $percent * 100;
    }

    /**
     * 高精度返回最大值
     * @param mixed $value
     * @param mixed ...$values
     * @return int|mixed
     */
    public static function bc_max($value, ...$values)
    {
        $args = func_get_args();
        if (count($args) == 0) return 0;
        $max = $args[0];
        foreach ($args as $item) {
            if (bccomp($item, $max) == 1) {
                $max = $item;
            }
        }
        return $max;
    }

    /**
     * 高精度返回最小值
     *
     * @param mixed $value
     * @param mixed ...$values
     * @return int|mixed
     */
    public static function bc_min($value, ...$values)
    {
        $args = func_get_args();
        if (count($args) == 0) return 0;
        $min = $args[0];
        foreach ($args as $item) {
            if (bccomp($min, $item) == 1) {
                $min = $item;
            }
        }
        return $min;
    }
}
