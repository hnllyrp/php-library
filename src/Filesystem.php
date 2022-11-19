<?php

namespace Hnllyrp\PhpSupport;


class Filesystem
{
    /**
     * 返回目录文件大小  单位 字节
     * echo round(dirSize('/home/vagrant')/1024/1024).'MB';
     * @param $dir
     * @return int
     */
    public static function dirSize($dir): int
    {
        $size = 0;
        foreach (glob($dir . '/*') as $file) {
            $size += is_file($file) ? filesize($file) : self::dirSize($file);
        }
        return $size;
    }

    /**
     * 删除目录
     * @param $dir
     * @return bool
     */
    public static function delDir($dir): bool
    {
        if (!is_dir($dir)) {
            return true;
        }
        foreach (glob($dir . '/*') as $file) {
            is_file($file) ? unlink($file) : self::delDir($file);
        }
        return rmdir($dir);
    }

    /**
     * 复制目录
     * @param $dir
     * @param $to
     * @return bool
     */
    public static function copyDir($dir, $to): bool
    {
        is_dir($to) or mkdir($to, 0755, true);
        foreach (glob($dir . '/*') as $file) {
            $target = $to . '/' . basename($file);
            is_file($file) ? copy($file, $target) : self::copyDir($file, $target);
        }
        return true;
    }

    /**
     * 移动目录
     * @param $dir
     * @param $to
     * @return bool
     */
    public static function moveDir($dir, $to): bool
    {
        self::copyDir($dir, $to);
        return self::delDir($dir);
    }

    /**
     * 获取指定目录下所有的文件，包括子目录下的文件
     *
     * @param string $dir
     * @return array
     */
    public static function getFiles($dir)
    {
        $files = [];
        $each = function ($dir) use (&$each, &$files) {
            $it = new \FilesystemIterator($dir);
            /**@var $file \SplFileInfo */
            foreach ($it as $file) {
                if ($file->isDir()) {
                    $each($file->getPathname());
                } else {
                    $files[] = $file;
                }
            }
        };
        $each($dir);

        return $files;
    }

    /**
     * 递归指定目录下所有的文件，包括子目录下的文件
     *
     * @param string $dir
     * @param callable $callback
     */
    public static function each($dir, callable $callback)
    {
        $each = function ($dir) use (&$each, $callback) {
            $it = new \FilesystemIterator($dir);

            /**@var $file \SplFileInfo */
            foreach ($it as $file) {
                if ($callback($file) === false) {
                    return false;
                }

                if ($file->isDir()) {
                    if ($each($file->getPathname()) === false) {
                        return false;
                    }
                }
            }

            return true;
        };

        $each($dir);
    }

    /**
     * 删除文件或目录
     *
     * @param string $dir
     * @return bool
     */
    public static function delete($dir)
    {
        $each = function ($dir) use (&$each) {
            if (!is_dir($dir)) {
                return true;
            }

            $it = new \FilesystemIterator($dir);
            $flag = true;
            /**@var $file \SplFileInfo */
            foreach ($it as $file) {
                if ($file->isDir()) {
                    if ($each($file->getPathname()) === true) {
                        if (!@rmdir($file->getPathname()))
                            $flag = false;
                    } else {
                        $flag = false;
                    }
                } else {
                    if (!@unlink($file->getPathname()))
                        $flag = false;
                }
            }

            return $flag;
        };

        if ($each($dir) === true) {
            if (!is_dir($dir) || @rmdir($dir)) {
                return true;
            }
        }

        return false;
    }


    /**
     * 建立一个具有唯一文件名的文件
     *
     * @param string $prefix
     * @return false|string
     */
    public static function tempFilePath($prefix = '')
    {
        return tempnam(sys_get_temp_dir(), empty($prefix) ? uniqid() : $prefix);
    }

    /**
     * 写入数据到临时文件中
     *
     * @param mixed $data
     * @param string $prefix
     * @return false|string
     */
    public static function putTempFile($data, $prefix = '')
    {
        $filePath = static::tempFilePath($prefix);
        if ($filePath === false) {
            return false;
        }

        if (file_put_contents($filePath, $data) === false) {
            return false;
        }

        return $filePath;
    }

    /*
    * 返回指定目录下的文件
    * @param string $dir 目录名
    * @param boolean $rec 是否递归
    * @param string 筛选扩展名 多个扩展名使用,号分隔
    * @return array
    */
    public static function treeFile($dir = '', $rec = FALSE, $ext = '')
    {
        $list = array();
        $dir = ($dir == '') ? dirname(__FILE__) : $dir;
        if (!is_dir($dir)) return array();

        $link = opendir($dir);
        while (FALSE !== ($file = readdir($link))) {
            if ($file != '.' && $file != '..') {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if ($rec && is_dir($path)) {
                    $list = array_merge($list, self::treeFile($path, $rec, $ext));
                } else {

                    if (!empty($ext)) {
                        $extArr = explode(',', $ext);
                        if (in_array(strrchr($file, '.'), $extArr)) $list[] = $path;
                    } else {
                        $list[] = $path;
                    }
                }
            }
        }
        closedir($link);
        return $list;
    }

    /**
     * 删除文件
     * @param string $filename
     * @return bool
     */
    public static function unlink_file($filename = '')
    {
        if (file_exists($filename) and is_file($filename)) {
            return unlink($filename);
        }
        return false;
    }

    /**
     * 复制文件
     * @param string $src_file
     * @param string $target_file
     * @return bool
     */
    public static function copy_file($src_file = '', $target_file = '')
    {
        if (is_file($src_file) && !file_exists($target_file)) {
            return copy($src_file, $target_file);
        }

        return false;
    }

    /**
     * 获取文件名后缀
     * @param string $filename
     * @return false|mixed|string
     */
    public static function get_suffix($filename = '')
    {
        if (file_exists($filename) and is_file($filename)) {
            $temp = explode(".", $filename);
            return end($temp);
        }
        return '';
    }

    //将字符串写入文件
    public static function input_content($filename, $str)
    {
        if (function_exists('file_put_contents')) {
            file_put_contents($filename, $str);
        } else {
            $fp = fopen($filename, "wb");
            fwrite($fp, $str);
            fclose($fp);
        }
    }

    //将整个文件内容读出到一个字符串中
    public static function output_content($filename)
    {
        if (function_exists('file_get_contents')) {
            return file_get_contents($filename);
        } else {
            $fp = fopen($filename, "rb");
            $content = fread($fp, filesize($filename));
            fclose($fp);
            return $content;
        }
    }

    /**
     * 读取文件.
     *
     * @param $filename
     * @return false|string
     */
    public function readFileContent($filename)
    {
        $fp = fopen($filename, 'r');
        $content = fread($fp, filesize($filename));
        fclose($fp);

        return $content;
    }

    //将文件内容读出到一个数组中
    public static function output_to_array($filename = '')
    {
        $file = file($filename);
        $arr = [];
        foreach ($file as $value) {
            $arr[] = trim($value);
        }
        return $arr;
    }
}
