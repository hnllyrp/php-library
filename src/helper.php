<?php

/**
 * 生成不重复唯一标识
 * @param string $mode
 * @return false|string
 */
function generate_uniqid($mode = 'small')
{
    if ($mode == 'small') {
        // 循环 1000000次 内有重复，但不多
        return uniqid('', true);
    }

    if ($mode == 'big') {
        // 循环 1000000次 内未重复
        return md5(uniqid(md5(microtime(true)), true));
    }

    if ($mode == 'session') {
        return session_create_id(); // php 7.1 新增，依赖于 session
    }

    return md5(32);
}

/**
 * PHP获取文件大小并格式化
 * 以下使用的函数可以获取文件的大小，并且转换成便于阅读的KB，MB等格式。
 * 使用方法如下：
 * $thefile = filesize('test_file.mp3');
 * echo format_size($thefile);
 *
 */
function format_size($size)
{
    $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    if ($size == 0) {
        return ('n/a');
    } else {
        return (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
    }
}

/**
 * 返回格式化文件大小
 * Formats bytes into a human readable string
 *
 * @param int $bytes
 * @return string
 */
function format_bytes(int $bytes)
{
    if ($bytes > 1024 * 1024) {
        return round($bytes / 1024 / 1024, 2) . ' MB';
    } elseif ($bytes > 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }

    return $bytes . ' B';
}

/**
 * 返回格式化数字大小
 *
 * @param int $number
 * @return int|string
 */
function format_number(int $number = 0)
{
    $unit = ['百', '千', '万'];

    if ($number < 100) {
        return $number;
    } elseif ($number >= 100 && $number < 1000) {
        return floor($number / 100) . $unit['0'] . '+';
    } elseif ($number >= 1000 && $number < 10000) {
        return floor($number / 1000) . $unit['1'] . '+';
    } elseif ($number >= 10000) {
        return floor($number / 10000) . $unit['2'] . '+';
    }
}

/**
 * 清除字符串所有空格与换行，也支持数组
 *
 * @param  $str
 * @return
 */
function trimall($str)
{
    if (is_array($str)) {
        return array_map('trimall', $str);
    }
    $search = array(" ", "　", "\t", "\n", "\r");
    $replace = array("", "", "", "", "");
    return str_replace($search, $replace, $str);
}

/**
 * php加载函数要比加载类要快一些，建议使用函数判断
 * 判断是否是通过手机访问
 *
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }

    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 判断是否SSL协议  https://
 * @return boolean
 */
function is_ssl()
{
    // Apache: HTTPS == 1 , IIS: HTTPS == on
    if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
        return true;
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true; //https使用端口443
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true; // 代理
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true; // 代理
    }
    return false;
}

/**
 * 生成文件
 * file_write("apiclient_cert.pem", $content);
 * @param string $content
 */
function file_write($filename, $content = '')
{
    $fp = fopen('storage/certs/' . $filename, "w+"); // 读写方式，每次修改会覆盖原内容
    flock($fp, LOCK_EX);
    fwrite($fp, $content);
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * 生成php日志文件
 * log_info 2007_03_07_1116_log.php
 * @param string $content
 */
function log_info($content = '', $code = 'pay')
{
    // 数组格式
    if (is_array($content)) {
        $content = var_export($content, true);
    } else {
        // xml格式
        $xml_parser = xml_parser_create();
        if (xml_parse($xml_parser, $content, true)) {
            $content = (array)(json_decode(json_encode(simplexml_load_string($content)), true));
            $content = var_export($content, true);
        } else {
            xml_parser_free($xml_parser);// 字符串
        }
    }

    $str_start = "<?php\r\n"; //得到php的起始符
    $str_end = "\r\n?>"; //php结束符
    $str_start .= $content;
    $str_start .= $str_end; //加入结束符

    $file = 'storage/logs/payment' . '/' . $code;
    if (!is_dir($file)) {
        @mkdir($file, 0777, true);
    }

    $filename = date('Y_m_d_Hi') . '_log.php';
    $fp = fopen($file . '/' . $filename, "w"); //
    flock($fp, LOCK_EX);
    fwrite($fp, $str_start);
    flock($fp, LOCK_UN);
    fclose($fp);
}



