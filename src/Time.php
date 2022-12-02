<?php

namespace Hnllyrp\PhpSupport;

use DateTimeImmutable;

/**
 * Class Time
 */
class Time
{
    /**
     * 统计脚本运行时间 单位s
     *
     * runtime('start');
     * ...
     * runtime('end');
     * echo '当前脚本运行时间' . runtime('start','end');
     *
     * @param null $start
     * @param null $end
     * @return array|float
     */
    public static function runtime($start = null, $end = null)
    {
        static $cache = [];
        if (is_null($start)) {
            return $cache;
        } elseif (is_null($end)) {
            return $cache[$start] = microtime(true);
        } else {
            $end = $cache[$end] ?? microtime(true);
            return number_format($end - $cache[$start], 5, '.', ''); // s
        }
    }

    /**
     * 获取毫秒级别的时间戳
     */
    public static function milli_second()
    {
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        return $time2[0];
    }

    /**
     * from web
     */
    public static function milli_second_str()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return substr($msectime, 0, 13);
    }

    /**
     * from web
     */
    public static function milli_second2()
    {
        return number_format(microtime(true), 3, '', '');
    }

    /**
     * 同 Carbon::now()->valueOf()
     * from nesbot/carbon
     */
    public static function getTimestampMs($precision = 3)
    {
        $date = new DateTimeImmutable();
        return (int)round(((float)$date->format('Uu')) / pow(10, 6 - $precision));
    }

    /**
     * from getui
     */
    public static function milli_second_time()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ($sec . substr($usec, 2, 3));
    }

    /**
     * from ramsey/uuid
     */
    public static function milli_second_timestamp()
    {
        $time = explode(' ', microtime(false));

        return $time[1] . substr($time[0], 2, 5);
    }

    /**
     * from symfony/polyfill-php73
     */
    public static function hrtime($asNum = false)
    {
        $ns = microtime(false);
        $s = substr($ns, 11) - (int)microtime(true);
        $ns = 1E9 * (float)$ns;

        if ($asNum) {
            $ns += $s * 1E9;

            return \PHP_INT_SIZE === 4 ? $ns : (int)$ns;
        }

        return [$s, (int)$ns];
    }

    /**
     * 格式化时间
     */
    public static function short_format_date($time = NULL)
    {
        $text = '';
        $t = $time - time(); //时间差 （秒）
        if ($t <= 0) {
            return 1;
        }
        $y = date('Y', $time) - date('Y', time());//是否跨年
        switch ($t) {
            case $t == 0:
                $text = '刚刚';
                break;
            case $t < 60:
                $text = $t . '秒'; // 一分钟内
                break;
            case $t < 60 * 60:
                $text = floor($t / 60) . '分'; //一小时内
                break;
            case $t < 60 * 60 * 24:
                $text = floor($t / (60 * 60)) . '时'; // 一天内
                break;
            default:
                $text = floor($t / (60 * 60 * 24)) . '天'; //一年以前
                break;
        }

        return $text;
    }

    /**
     * 获取相对时间
     *
     * @param int $timeStamp
     * @return string
     */
    public static function formatRelative($timeStamp)
    {
        $currentTime = time();

        // 判断传入时间戳是否早于当前时间戳
        $isEarly = $timeStamp <= $currentTime;

        // 获取两个时间戳差值
        $diff = abs($currentTime - $timeStamp);

        $dirStr = $isEarly ? '前' : '后';

        if ($diff < 60) { // 一分钟之内
            $resStr = $diff . '秒' . $dirStr;
        } elseif ($diff >= 60 && $diff < 3600) { // 多于59秒，少于等于59分钟59秒
            $resStr = floor($diff / 60) . '分钟' . $dirStr;
        } elseif ($diff >= 3600 && $diff < 86400) { // 多于59分钟59秒，少于等于23小时59分钟59秒
            $resStr = floor($diff / 3600) . '小时' . $dirStr;
        } elseif ($diff >= 86400 && $diff < 2623860) { // 多于23小时59分钟59秒，少于等于29天59分钟59秒
            $resStr = floor($diff / 86400) . '天' . $dirStr;
        } elseif ($diff >= 2623860 && $diff <= 31567860 && $isEarly) { // 多于29天59分钟59秒，少于364天23小时59分钟59秒，且传入的时间戳早于当前
            $resStr = date('m-d H:i', $timeStamp);
        } else {
            $resStr = date('Y-m-d', $timeStamp);
        }

        return $resStr;
    }

    /**
     * 返回今日开始和结束的时间戳
     *
     * @return array
     */
    public static function today()
    {
        [$y, $m, $d] = explode('-', date('Y-m-d'));

        return [
            mktime(0, 0, 0, $m, $d, $y),
            mktime(23, 59, 59, $m, $d, $y),
        ];
    }

    /**
     * 获取今天，本周，本月，三个月内，半年内，今年的开始和结束时间函数
     * @param string $type
     * @return false
     */
    public static function get_period_time($type = 'day')
    {
        $rs = false;
        $now = time();
        switch ($type) {
            case 'day'://今天
                $rs['beginTime'] = date('Y-m-d 00:00:00', $now);
                $rs['endTime'] = date('Y-m-d 23:59:59', $now);
                break;
            case 'week'://本周
                $time = '1' == date('w') ? strtotime('Monday', $now) : strtotime('last Monday', $now);
                $rs['beginTime'] = date('Y-m-d 00:00:00', $time);
                $rs['endTime'] = date('Y-m-d 23:59:59', strtotime('Sunday', $now));
                break;
            case 'month'://本月
                $rs['beginTime'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', $now), '1', date('Y', $now)));
                $rs['endTime'] = date('Y-m-d 23:39:59', mktime(0, 0, 0, date('m', $now), date('t', $now), date('Y', $now)));
                break;
            case '3month'://三个月
                $time = strtotime('-2 month', $now);
                $rs['beginTime'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', $time), 1, date('Y', $time)));
                $rs['endTime'] = date('Y-m-d 23:39:59', mktime(0, 0, 0, date('m', $now), date('t', $now), date('Y', $now)));
                break;
            case 'half_year'://半年内
                $time = strtotime('-5 month', $now);
                $rs['beginTime'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m', $time), 1, date('Y', $time)));
                $rs['endTime'] = date('Y-m-d 23:39:59', mktime(0, 0, 0, date('m', $now), date('t', $now), date('Y', $now)));
                break;
            case 'year'://今年内
                $rs['beginTime'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, 1, 1, date('Y', $now)));
                $rs['endTime'] = date('Y-m-d 23:39:59', mktime(0, 0, 0, 12, 31, date('Y', $now)));
                break;
        }
        return $rs;
    }

    /**
     * 获取 某个月的最大天数（最后一天）
     * @param int $month
     * @param int $year
     * @return int
     */
    public static function get_month_lastday($month = 1, $year = 2022)
    {
        switch ($month) {
            case 4 :
            case 6 :
            case 9 :
            case 11 :
                $days = 30;
                break;
            case 2 :
                if ($year % 4 == 0) {
                    if ($year % 100 == 0) {
                        $days = $year % 400 == 0 ? 29 : 28;
                    } else {
                        $days = 29;
                    }
                } else {
                    $days = 28;
                }
                break;

            default :
                $days = 31;
                break;
        }
        return $days;
    }


    /**
     * 求出最大连续天数
     * 比如 用于签到场景
     * @param array $time_array
     * @return mixed|string
     */
    public static function continue_days(array $time_array = [])
    {
        // $time_array = ['2018-04-10', '2018-04-08', '2018-04-06', '2018-04-05', '2018-04-04'];
        if (empty($time_array)) {
            return '';
        }

        $continue_days = 1;
        $continue_days_array = [];

        $list_length = count($time_array);
        for ($i = 0; $i < $list_length; $i++) {
            $today = strtotime($time_array[$i]);
            if ($i == $list_length - 1) {
                $continue_days_array[] = $continue_days;
            } else {
                $yesterday = strtotime($time_array[$i + 1]);
                $one_day = 24 * 3600;
                if ($today - $yesterday == $one_day) {
                    $continue_days += 1;
                } else {
                    $continue_days_array[] = $continue_days;
                    $continue_days = 1;
                }
            }
        }

        return max($continue_days_array);
    }

    /**
     * 返回连续天数
     * @param array $day_list
     * @return int
     */
    public function continue_day(array $day_list = [])
    {
        // $time_array = ['2018-04-10', '2018-04-08', '2018-04-06', '2018-04-05', '2018-04-04'];
        if (empty($time_array)) {
            return '';
        }

        $continue_day = 1;//连续天数

        $count = count($day_list);
        if ($count >= 1) {
            for ($i = 1; $i <= $count; $i++) {
                if ((abs((strtotime(date('Y-m-d')) - strtotime($day_list[$i - 1])) / 86400)) == $i) {
                    $continue_day = $i + 1;
                } else {
                    break;
                }
            }
        }

        return $continue_day;    //输出连续几天
    }

    /**
     * 已知月份日期获取星座
     * @param int $month
     * @param int $day
     * @return string
     */
    function constellation(int $month = 1, int $day = 1)
    {
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
            return '';
        }
        //参数为 number 类型
        $constellation_arr = ["魔羯座", "水瓶座", "双鱼座", "白羊座", "金牛座", "双子座", "巨蟹座", "狮子座", "处女座", "天秤座", "天蝎座", "射手座", "魔羯座"];
        $constellation_edge_day = [20, 19, 21, 20, 21, 22, 23, 23, 23, 24, 23, 22];

        return $day < $constellation_edge_day[$month] ? $constellation_arr[$month - 1] : $constellation_arr[$month];
    }

    function get_constellation(int $month = 1, int $day = 1)
    {
        //判断的时候,为避免出现1和true的疑惑,或是判断语句始终为真的问题,这里统一处理成字符串形式
        $str_month = strval($month);

        $constellation_name = [
            '水瓶座', '双鱼座', '白羊座', '金牛座', '双子座', '巨蟹座',
            '狮子座', '处女座', '天秤座', '天蝎座', '射手座', '摩羯座'
        ];

        if ($day <= 22) {
            if ('1' !== $str_month) {
                $constellation = $constellation_name[$month - 2];
            } else {
                $constellation = $constellation_name[11];
            }
        } else {
            $constellation = $constellation_name[$month - 1];
        }

        return $constellation;
    }

    /**
     * 通过年份获取生肖
     *
     * @param int $year
     * @return string
     */
    function chinese_zodiac($year = 1988)
    {
        $animal = ['子鼠', '丑牛', '寅虎', '卯兔', '辰龙', '巳蛇', '午马', '未羊', '申猴', '酉鸡', '戌狗', '亥猪'];
        return $animal[($year - 1900) % 12];
    }

}
