<?php

namespace Hnllyrp\PhpSupport;

/**
 * Class Str
 * @package Hnllyrp\PhpSupport
 */
class Str
{
    /**
     *UUID是指在一台机器上生成的数字，它保证对在同一时空中的所有机器都是唯一的。通常平台 会提供生成UUID的API。UUID按照开放软件基金会(OSF)制定的标准计算，用到了以太网卡地址、纳秒级时间、芯片ID码和许多可能的数字。由以 下几部分的组合：当前日期和时间(UUID的第一个部分与时间有关，如果你在生成一个UUID之后，过几秒又生成一个UUID，则第一个部分不同，其余相 同)，时钟序列，全局唯一的IEEE机器识别号（如果有网卡，从网卡获得，没有网卡以其他方式获得），UUID的唯一缺陷在于生成的结果串会比较长。关于 UUID这个标准使用最普遍的是微软的GUID(Globals Unique Identifiers)。在ColdFusion中可以用CreateUUID()函数很简单的生成UUID，其格式为：xxxxxxxx-xxxx-xxxx- xxxxxxxxxxxxxxxx(8-4-4-16)，其中每个 x 是 0-9 或 a-f 范围内的一个十六进制的数字。而标准的UUID格式为：xxxxxxxx-xxxx-xxxx-xxxxxx-xxxxxxxxxx (8-4-4-4-12)
     */
    /**
     * 生成为一的uuid
     */
    public static function get_uuid()
    {
        if (function_exists('com_create_guid')) {
            $guid = com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $guid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
        }
        return strtoupper(hash('ripemd128', $guid));
    }

    /**
     * 生成随机字符加数字的函数
     * @param int $length
     * @param string $pool
     * @return string
     */
    public static function get_unique_id($length = 32, $pool = "")
    {
        if ($pool == "") $pool .= "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        mt_srand((double)microtime() * 1000000);
        $unique_id = "";
        for ($index = 0; $index < $length; $index++) {
            $unique_id .= substr($pool, (mt_rand() % (strlen($pool))), 1);
        }
        return $unique_id;
    }

    /*
    作用：取得随机字符串
    参数：
        1、(int)$length = 32 #随机字符长度，默认为32
        2、(int)$mode = 0 #随机字符类型，0为大小写英文和数字，1为数字，2为小写子木，3为大写字母，4为大小写字母，5为大写字母和数字，6为小写字母和数字
    返回：取得的字符串
    使用：
        $str = get_random_code($length, $mode);
    */
    public static function get_random_code($length = 32, $mode = 0)
    {
        switch ($mode) {
            case '1':
                $str = '1234567890';
                break;
            case '2':
                $str = 'abcdefghijklmnopqrstuvwxyz';
                break;
            case '3':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case '4':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case '5':
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                break;
            case '6':
                $str = 'abcdefghijklmnopqrstuvwxyz1234567890';
                break;
            default:
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
                break;
        }

        $result = '';
        $l = strlen($str);

        for ($i = 0; $i < $length; $i++) {
            $result .= $str[mt_rand(1, $l) - 1];
        }
        return $result;
    }

    /**
     * @检测密码强度
     * @param string $password
     * @return int
     */
    public static function checkPassword($password = '')
    {
        /*** 调用示例 ***/
        // $password = 'php_tutorials_and_examples!123';
        // $password = 'hnnn5678';
        // echo checkPassword($password);

        if (strlen($password) == 0) {
            return 1;
        }

        $strength = 0;

        /*** get the length of the password ***/
        $length = strlen($password);

        /*** check if password is not all lower case ***/
        if (strtolower($password) != $password) {
            $strength += 1;
        }

        /*** check if password is not all upper case ***/
        if (strtoupper($password) == $password) {
            $strength += 1;
        }

        /*** check string length is 8 -15 chars ***/
        if ($length >= 8 && $length <= 15) {
            $strength += 1;
        }

        /*** check if lenth is 16 - 35 chars ***/
        if ($length >= 16 && $length <= 35) {
            $strength += 2;
        }

        /*** check if length greater than 35 chars ***/
        if ($length > 35) {
            $strength += 3;
        }

        /*** get the numbers in the password ***/
        preg_match_all('/[0-9]/', $password, $numbers);
        $strength += count($numbers[0]);

        /*** check for special chars ***/
        preg_match_all("/[|!@#$%&*\/=?,;.:\-_+~^\\\]/", $password, $specialchars);
        $strength += sizeof($specialchars[0]);

        /*** get the number of unique chars ***/
        $chars = str_split($password);
        $num_unique_chars = sizeof(array_unique($chars));
        $strength += $num_unique_chars * 2;

        /*** strength is a number 1-10; ***/
        $strength = $strength > 99 ? 99 : $strength;
        $strength = floor($strength / 10 + 1);

        return $strength;
    }

    /**
     * 实现中英文截取 不会乱码
     * @param  [type]  $sourcestr [description]
     * @param integer $cutlength [description]
     * @param string $etc [description]
     * @return
     */
    public static function cut_str($sourcestr, $cutlength = 5, $etc = '***')
    {
        /*
        使用方法：
        $content = '刘默默';
        $str = cut_str($content, 1);
        echo $str;   //刘***
        */

        $returnstr = '';
        $i = 0;
        $n = 0.0;
        $str_length = strlen($sourcestr); //字符串的字节数
        while (($n < $cutlength) and ($i < $str_length)) {
            $temp_str = substr($sourcestr, $i, 1);
            $ascnum = ord($temp_str); //得到字符串中第$i位字符的ASCII码
            if ($ascnum >= 252) //如果ASCII位高与252
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 6); //根据UTF-8编码规范，将6个连续的字符计为单个字符
                $i = $i + 6; //实际Byte计为6
                $n++; //字串长度计1
            } elseif ($ascnum >= 248) //如果ASCII位高与248
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 5); //根据UTF-8编码规范，将5个连续的字符计为单个字符
                $i = $i + 5; //实际Byte计为5
                $n++; //字串长度计1
            } elseif ($ascnum >= 240) //如果ASCII位高与240
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 4); //根据UTF-8编码规范，将4个连续的字符计为单个字符
                $i = $i + 4; //实际Byte计为4
                $n++; //字串长度计1
            } elseif ($ascnum >= 224) //如果ASCII位高与224
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
                $i = $i + 3; //实际Byte计为3
                $n++; //字串长度计1
            } elseif ($ascnum >= 192) //如果ASCII位高与192
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
                $i = $i + 2; //实际Byte计为2
                $n++; //字串长度计1
            } elseif ($ascnum >= 65 and $ascnum <= 90 and $ascnum != 73) //如果是大写字母 I除外
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1; //实际的Byte数仍计1个
                $n++; //但考虑整体美观，大写字母计成一个高位字符
            } elseif (!(array_search($ascnum, array(37, 38, 64, 109, 119)) === FALSE)) //%,&,@,m,w 字符按１个字符宽
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1; //实际的Byte数仍计1个
                $n++; //但考虑整体美观，这些字条计成一个高位字符
            } else //其他情况下，包括小写字母和半角标点符号
            {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1; //实际的Byte数计1个
                $n = $n + 0.5; //其余的小写字母和半角标点等与半个高位字符宽...
            }
        }
        if ($i < $str_length) {
            $returnstr = $returnstr . $etc; //超过长度时在尾处加上省略号
        }
        return $returnstr;
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @access public
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断显示字符 默认 ***
     * @param string $position 截断显示字符位置 默认 1 为中间 例：刘***然，0 为后缀 刘***
     * @return string
     */
    public static function msubstr($str, $start = 0, $length = 1, $charset = "utf-8", $suffix = '***', $position = 1)
    {
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
            $slice_end = mb_substr($str, -$length, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
            $slice_end = iconv_substr($str, -$length, $length, $charset);
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
            $slice_end = join("", array_slice($match[0], -$length, $length));
        }

        return $position == 0 ? $slice . $suffix : $slice . $suffix . $slice_end;
    }

    /**
     * 将字符串以 * 号格式显示 配合 self::msubstr 函数使用
     * string_to_star($str,1)  w******f , string_to_star($str,2) we****af
     * @param string $string 至少9个字符长度
     * @return string
     */
    public static function string_to_star($string = '', $num = 3)
    {
        if (strlen($string) > 9 && strlen($string) > $num) {
            $lenth = strlen($string) - $num * 2;
            for ($x = 1; $x <= $lenth; $x++) {
                $star_length .= "*";
            }
            $result = self::msubstr($string, 0, $num, 'utf-8', $star_length);
        } else {
            $result = $string;
        }

        return $result;
    }

    /**
     * 获得URL参数
     * @param string $url URL表达式，格式：'?参数1=值1&参数2=值2...'
     * @return array
     * 例如：
     * $url = 'http://192.168.1.75/ecmoban_dsc/mobile/goods/409.html?u=1&a=2&b=3';
     * $param = get_url_query($url);
     * // print_r($param);
     * // 取得u参数值
     * echo $u = !empty($param['u']) ? $param['u'] : 0;
     */
    public static function get_url_query($url = '')
    {
        $params = [];
        // 解析URL
        $info = parse_url($url);
        // 判断参数 是否为url 或 path
        if (false == strpos($url, '?')) {
            if (isset($info['path'])) {
                // 解析地址里面path参数
                parse_str($info['path'], $params);
            }
        } elseif (isset($info['query'])) {
            // 解析地址里面query参数
            parse_str($info['query'], $params);
        }

        return $params;
    }

    /**
     * 处理URL 加上后缀参数 如 ?id=1  &id=1
     * @param string $url URL表达式，格式：'?参数1=值1&参数2=值2...'
     * @param string|array $vars 传入的参数，支持数组和字符串
     * @return string $url
     */
    public static function add_url_suffix($url = '', $vars = '')
    {
        // 解析URL
        $info = parse_url($url);

        $depr = '?';
        if (isset($info['query'])) {
            // query = ?id=100
            $info['query'] = htmlspecialchars_decode($info['query']); // 处理html字符 &amp, 导致的参数重复

            // 解析地址里面参数 合并到 query
            if (!empty($vars)) {
                parse_str($info['query'], $params);
                $vars = array_merge($params, $vars);
                $info['query'] = http_build_query($vars);
            }
        }
        if (isset($info['fragment'])) {
            $string = http_build_query($vars);
            // fragment = #/user/order?parent_id=6
            if (strpos($info['fragment'], '?') !== false && strpos($info['fragment'], $string) === false) {
                $depr = '&';
            } // fragment = #/user/order?parent_id=6&wechat_ru_id=1
            elseif (strpos($info['fragment'], '&') !== false && strpos($info['fragment'], $string) !== false) {
                $depr = '&';
            }
            // fragment = #/user/order
            $new_string = $depr . $string;
            // 处理参数重复
            if (strpos($info['fragment'], $new_string) !== false) {
                $info['fragment'] = str_replace($new_string, '', $info['fragment']);
            }

            $info['fragment'] = $info['fragment'] . $new_string;
        }

        $url = self::unparse_url($info);

        return strtolower($url);
    }

    /**
     * 处理url
     * @param $parsed_url
     * @return string
     */
    public static function unparse_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }


    /**
     * 解析XML格式的字符串
     *
     * @param string $str
     * @return 解析正确就返回解析结果,否则返回false,说明字符串不是XML格式
     */
    public static function xml_parser($str)
    {
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $str, true)) {
            xml_parser_free($xml_parser);
            return false;
        } else {
            return (json_decode(json_encode(simplexml_load_string($str)), true));
        }
    }

    /**
     * 将URL中的某参数设为某值
     * @param  $url   index.php?m=goods&id=530&u=1111
     * @param  $key   key=330 ,u=1
     * @param  $value 要替换后的值
     * @return string
     */
    public static function url_set_value($url, $key, $value)
    {
        $a = explode('?', $url);
        $url_f = $a[0];
        $query = $a[1];
        parse_str($query, $arr);
        $arr[$key] = $value;
        return $url_f . '?' . http_build_query($arr);
    }

    /**
     * 获取html 标签内容
     * @param string $attr
     * @param $value
     * @param $xml
     * @param null $tag
     * @return mixed
     */
    public static function get_html_tag($attr = 'div', $value = '', $xml = '', $tag = null)
    {
        if (is_null($tag))
            $tag = '\w+';
        else
            $tag = preg_quote($tag);

        $attr = preg_quote($attr);
        $value = preg_quote($value);

        $tag_regex = "/<(" . $tag . ")[^>]*$attr\s*=\s*" . "(['\"])$value\\2[^>]*>(.*?)<\/\\1>/";

        preg_match_all($tag_regex, $xml, $matches, PREG_PATTERN_ORDER);

        return $matches[3];
    }

    /**
     * 匹配字符串内容与关键词高亮显示
     * @param string $str 字符串
     * @param string $keywords 关键词
     * @return  string
     */
    public static function html_highlight($str = '', $keywords = '')
    {
        if (empty($str)) {
            return '';
        }

        if (!empty($keywords)) {
            if (is_array($keywords)) {
                $keyword = implode(' ', $keywords);
            } else {
                $keyword = $keywords;
            }

            $str = preg_replace("/($keyword)/i", "<b style=\"color:red;font-size:inherit;\">\\1</b>", $str);
        }

        return $str;
    }

    /**
     *
     * 替换字符串关键词长词优先函数
     *
     * $str = 'php技术 是时下最好用的 php';
     * echo replaceStrKeywords($str,['php'=>'C#','php技术'=>'java技术']);
     *
     * @param string $string
     * @param array $replaces
     * @return array|string|string[]
     */
    public static function replace_str_keywords($string = '', $replaces = [])
    {
        uksort($replaces, function ($a, $b) {
            return isset($b[strlen($a)]);
        });

        return str_replace(array_keys($replaces), array_values($replaces), $string);
    }

    /**
     * 在输出字符串的时候需要文字根据宽度换行, 在网页上文字可以根据宽度自动换行, 但是在命令行自定义宽度, 或者其它特殊场景, 就需要自己来控制换行了
     * 这里假设一个汉字占用2个英文的宽度
     * http://www.dotcoo.com/php-string-wrap
     * @param string $str 原始字符串
     * @param int $length 插入的间隔长度, 英文长度
     * @param int $hans_length 一个汉字等于多少个英文的宽度, 不支持小数
     * @param string $append 需要插入的字符串
     * @return string
     */
    public static function str_wrap1($str = '', $length = 16, $hans_length = 2, $append = "\r\n")
    {
        $new_str = "";
        for ($line = 0, $blen = 1, $len = strlen($str),
             $i = 0; $i < $len; $i += $blen) {
            $b = unpack("C", $str{$i})[1];
            if (($b & 0xF0) == 0xF0) {
                $blen = 4;
            } elseif (($b & 0xE0) == 0xE0) {
                $blen = 3;
            } elseif (($b & 0xC0) == 0xC0) {
                $blen = 2;
            } else {
                $blen = 1;
            }
            $vlen = $blen > 1 ? $hans_length : 1;
            if ($line + $vlen > $length) { // 检测如果加上当前字符是否会超出行的最大字数
                $new_str .= $append; // 超出就加上换行符
                $line = 0; // 因为加了换行符 就是新的一行 所以当前行长度设置为0
            }
            $new_str .= substr($str, $i, $blen); // 加上当前字符
            $line += $vlen; // 加上当前字符的长度
        }
        return $new_str;
    }

    /**
     * @param string $str 原始字符串
     * @param int $length 插入的间隔长度, 英文长度
     * @param int $hans_length 一个汉字等于多少个英文的宽度
     * @param string $append 需要插入的字符串
     * @return string
     */
    public static function str_wrap2($str = '', $length = 16, $hans_length = 2, $append = "\r\n")
    {
        // $line 记录当前行的长度 // $len utf-8字符串的长度
        $new_str = "";
        for ($line = 0, $len = mb_strlen($str, "utf-8"), $i = 0; $i < $len; $i++) {
            $v = mb_substr($str, $i, 1, "utf-8"); // 获取当前的汉字或字母
            $vlen = strlen($v) > 1 ? $hans_length : 1; // 根据二进制长度 判断出当前是中文还是英文
            if ($line + $vlen > $length) { // 检测如果加上当前字符是否会超出行的最大字数
                $new_str .= $append; // 超出就加上换行符
                $line = 0; // 因为加了换行符 就是新的一行 所以当前行长度设置为0
            }
            $new_str .= $v; // 加上当前字符
            $line += $vlen; // 加上当前字符的长度
        }

        return $new_str;
    }

    /**
     * 截取手机号
     * demo:13112345678
     * return:131****5678
     * @param string $phone
     * @return array|string|string[]|null
     */
    public static function hidden_mobile_phone($phone = '')
    {
        $IsWhat = preg_match('/(0[0-9]{2,3}[-]?[2-9][0-9]{6,7}[-]?[0-9]?)/i', $phone); //固定电话
        if ($IsWhat == 1) {
            return preg_replace('/(0[0-9]{2,3}[-]?[2-9])[0-9]{3,4}([0-9]{3}[-]?[0-9]?)/i', '$1****$2', $phone);
        }

        return preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
    }

    /**
     * 得到字符长度 数字或字母或中文 都按一个字符计算
     * @param string $str 要计算长度的字符串
     */
    public static function str_length(string $str = '')
    {
        if (empty($str)) {
            return 0;
        }

        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'utf-8');
        } else {
            preg_match_all("/./us", $str, $matches);
            return count(current($matches));
        }
    }

    /**
     * php 验证中英文 字符长度（混合）
     * @param $string
     * @return string
     */
    function check_name_ch_or_en($string)
    {
        $temp_len = (strlen($string) + mb_strlen($string, 'utf-8')) / 2;
        if ($temp_len < 4 || $temp_len > 15) {
            return 'error_len';
        } else {
            $reg = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u'; //匹配中文字符，数字，字母的正则表达式
            if (preg_match($reg, $string)) {
                return 'match';
            } else {
                return 'not match';
            }
        }
    }

    /**
     * 实现多种字符编码方式
     *
     * @param string $input 数据源
     * @param string $_output_charset 输出的字符编码
     * @param string $_input_charset 输入的字符编码
     * @return string
     */
    public static function charsetEncode($input, $_output_charset, $_input_charset)
    {
        if (!isset ($_output_charset)) {
            $_output_charset = $_input_charset;
        }

        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists("iconv")) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else {
            throw new \RuntimeException("不支持 $_input_charset 到 $_output_charset 编码！");
        }

        return $output;
    }

    /**
     * 实现多种字符解码方式
     *
     * @param string $input 数据源
     * @param string $_input_charset 输入的字符编码
     * @param string $_output_charset 输出的字符编码
     * @return string
     */
    public static function charsetDecode($input, $_input_charset, $_output_charset)
    {
        if ($_input_charset == $_output_charset || $input == null) {
            $output = $input;
        } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input, $_output_charset, $_input_charset);
        } elseif (function_exists("iconv")) {
            $output = iconv($_input_charset, $_output_charset, $input);
        } else {
            throw new \RuntimeException("不支持 $_input_charset 到 $_output_charset 解码！");
        }

        return $output;
    }

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

    /**
     * 安全处理-字符串或数组转数组
     *
     * @param mixed $value
     * @param string $format
     * @param string $delimiter
     * @param bool|\Closure $filter
     * @return array
     */
    public static function explode($value, $format = 'intval', $delimiter = ',', $filter = true)
    {
        if (!is_array($value)) {
            $value = is_string($value) ? explode($delimiter, $value) : [$value];
        }

        $value = array_map($format, $value);

        if ($filter !== false) {
            if ($filter === true) {
                $value = array_filter($value);
            } else {
                $value = array_filter($value, $filter);
            }
        }

        return array_values($value);
    }

    /**
     * 安全处理-数组转字符串
     *
     * @param mixed $value
     * @param string $format
     * @param string $delimiter
     * @return string
     */
    public static function implode($value, $format = 'intval', $delimiter = ',')
    {
        //先转换为数组，进行安全过滤
        $value = self::explode($value, $format, $delimiter);

        //去除重复
        $value = array_unique($value);

        //再次转换为字符串
        return implode(",", $value);
    }


    /**
     * 替换图片src
     * @param string $content
     * @return array|mixed|string|string[]
     */
    public function replace_content_img($content = '')
    {
        $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.bmp|\.jpeg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern, $content, $match);
        if (count($match[1]) > 0) {
            foreach ($match[1] as $k => $img) {
                // if (strtolower(substr($img, 0, 4)) != 'http') {
                //     $realpath = mb_substr($img, stripos($img, 'images/'));
                //     $album[] = $realpath;
                // } else {
                //     $album[] = $img;
                // }
                $replace = $img . '?v=11';  // 需要替换的字符串
                $content = str_replace($img, $replace, $content);
            }
        }

        return $content;
    }

    /**
     * 正则过滤内容样式 style='' width='' height=''
     * @param string $content
     * @return string
     */
    public function content_style_replace($content = '')
    {
        $label = [
            '/style=.+?[*|\"]/i' => '', // 所有style内容
            '/width\=\"[0-9]+?\"/i' => '', // 去除width="100"
            '/height\=\"[0-9]+?\"/i' => '',
            '/width\:(.*?)\;/i' => '', // 去除width:733px
            '/height\:(.*?)\;/i' => '',
        ];
        foreach ($label as $key => $value) {
            $content = preg_replace($key, $value, $content);
        }
        return $content;
    }

    /**
     * 正则批量替换详情内图片 相对路径为绝对路径
     * @param string $content
     * @param string $url
     * @return string
     */
    public function content_img_replace($content = '', $url = '')
    {
        $label = [
            // 图片路径 "img:"/images/5951cff07c39a.jpg"  => "img:"http://www.a.com/images/5951cff07c39a.jpg"
            '/<img.*?src=[\"|\']?\/(.*?)[\"|\'].*?>/i' => '<img src="' . $url . '$1" >',
        ];

        foreach ($label as $key => $value) {
            $content = preg_replace($key, $value, $content);
        }

        return $content;
    }

    /**
     * 过滤特殊字符 微信昵称等
     * @param string $str
     * @return mixed|string
     */
    public static function filter_special_characters($str = '')
    {
        // 微信昵称特殊字符
        $patterns = [
            '/[\xf0-\xf7].{3}/',
            '/[\x{1F600}-\x{1F64F}]/u',
            '/[\x{1F300}-\x{1F5FF}]/u',
            '/[\x{1F680}-\x{1F6FF}]/u',
            '/[\x{2600}-\x{26FF}]/u',
            '/[\x{2700}-\x{27BF}]/u',
        ];
        $str = preg_replace($patterns, '', $str);
        // 其他特殊字符
        $patterns = [
            '/\r\n/', '/\n/', '/\r/', // 回车换行符
            '/[[:punct:]]/i', // 标点符号 英文状态下  !@#$%^&* ().,<>|[]'\":;}{-_+=?/~`
            '/\（/', '/\）/', '/\？/', '/\：/', '/\；/', '/\！/', '/\“/', '/\’/', '/\，/', '/\。/', // 标点符号 中文状态下（）？ ：；！ ” ‘ ，。
        ];
        return preg_replace($patterns, ' ', $str);
    }
}
