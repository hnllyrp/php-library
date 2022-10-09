<?php

namespace Hnllyrp\PhpSupport;

/**
 * Class Url
 */
class Url
{

    /**
     * Return current url.
     *
     * @return string
     */
    public static function current_url()
    {
        $protocol = 'http://';

        if ((!empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http') === 'https') {
            $protocol = 'https://';
        }

        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Get client ip.
     * 获取客户端ip
     *
     * @return string
     */
    public static function get_client_ip()
    {
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            // 命令行 for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }

    /**
     * Get current server ip.
     * 获取服务端ip
     *
     * @return string
     */
    public static function get_server_ip()
    {
        if (!empty($_SERVER['SERVER_ADDR'])) {
            $ip = $_SERVER['SERVER_ADDR'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
        } else {
            // 命令行 for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }


    /**
     * 取根域名 test.com
     * @param string $url
     * @return string
     */
    public static function get_root_main($url = '')
    {
        if (empty($url)) {
            return '';
        }

        // 以 . 分隔拆分数组
        $host = explode('.', $url);

        $host_arr = array_slice($host, -2, 2); // 取域名后两位

        return implode('.', $host_arr);
    }

    /**
     * 把IP地址转化为int
     *
     * @param string $ipAddr
     * @return int
     */
    public static function ip_to_int(string $ipAddr)
    {


        return 0;
    }

    /**
     * 把int->ip地址
     *
     * @param int $ipInt
     * @return String
     */
    public static function int_to_ip(int $ipInt)
    {
        if ($ipInt > 0) {

        }

        return "";
    }

}
