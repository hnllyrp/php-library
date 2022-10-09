<?php

namespace Hnllyrp\PhpSupport\Tool;

/**
 * 此文件用于快速测试UTF8编码的文件是不是加了BOM，并可自动移除
 * 增加了检测子文件夹和排除不要检测的文件夹
 *
 * @author yangzy
 */
$bom = new bom();
$bom->run();

class Bom
{
    public $basedir = ".";//修改此行为需要检测的目录，点表示当前目录
    public $auto = '1';//是否自动移除发现的BOM信息。1为是，0为否。
    public $file_arr = array('.svn', 'data', 'images', 'shop', 'temp');//不需要检测文件夹

    public function __construct()
    {
    }

    /**
     * 运行程序
     */
    public function run($basedir = '')
    {
        $basedir || $basedir = $this->basedir;
        if ($dh = opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..' && !is_dir($basedir . "/" . $file)) {
                    echo $basedir . "/" . $file . ':' . $this->check_bom($basedir . "/$file") . " <br>";
                } elseif ($file != '.' && !in_array($file, $this->file_arr) && $file != '..' && is_dir($basedir . "/" . $file)) {
                    $this->run($basedir . "/" . $file);
                }
            }
            closedir($dh);
        }
    }

    /**
     * 检测bom文件
     */
    public function check_bom($filename)
    {
        $contents = file_get_contents($filename);
        $charset[1] = substr($contents, 0, 1);
        $charset[2] = substr($contents, 1, 1);
        $charset[3] = substr($contents, 2, 1);
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
            if ($this->auto == 1) {
                $rest = substr($contents, 3);
                $this->rewrite($filename, $rest);
                return ("<font color=red>BOM found, automatically removed.</font>");
            } else {
                return ("<font color=red>BOM found.</font>");
            }
        } else return ("BOM Not Found.");
    }

    /**
     * 重新生成文件
     */
    public function rewrite($filename, $data)
    {
        $filenum = fopen($filename, "w");
        flock($filenum, LOCK_EX);
        fwrite($filenum, $data);
        fclose($filenum);
    }
}
