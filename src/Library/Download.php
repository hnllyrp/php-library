<?php

namespace Hnllyrp\PhpSupport\Library;

/**
 * 使用curl实现超大文件下载，并支持断点续载
 *
 * Class Download
 */
class Download
{
    protected $url;
    protected $save_file_path;

    protected $file_handler;
    protected $downloaded = 0;
    protected $url_filesize = 0;

    /**
     *
        $d = new Download(
        "http://download.microsoft.com/download/B/4/8/B4870509-05CB-447C-878F-2F80E4CB464C/vs2015.com_chs.iso",
        "1.iso"
        );

        while (!$d->isDownloadComplete()) {
            $d->start();
        }
     *
     * @param $url
     * @param $save_file_path
     */
    public function __construct($url, $save_file_path)
    {
        $this->url = $url;
        $this->save_file_path = $save_file_path;

        $this->file_handler = fopen($this->save_file_path, "a+");
        $this->downloaded = $this->getLocalFileLenth();
        $this->url_filesize = $this->getUrlFileSize();
    }

    public function __destruct()
    {
        fclose($this->file_handler);
    }

    public function isDownloadComplete()
    {
        return $this->downloaded == $this->url_filesize;
    }


    protected function writeFile($handle, $data)
    {
        $this->downloaded += sizeof($data);
        return fwrite($this->file_handler, $data);
    }

    protected function onProcess(
        $curl,
        $total_download,
        $now_downloaded,
        $total_to_upload,
        $now_uploaded
    )
    {
        printf("\033[1A"); //先回到上一行
        printf("\033[K");  //清除该行
        printf("%s", "下载进度 => " . bcdiv($this->downloaded * 100, $this->url_filesize, 8) . "%\r\n");
    }

    protected function getLocalFileLenth()
    {
        return filesize($this->save_file_path);
    }

    /**
     * 获取远程文件大小
     *
     * @return float
     */
    public function getUrlFileSize()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);

        return $info;
    }

    public function start()
    {
        $ch = curl_init();

        $localLen = $this->getLocalFileLenth();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, [$this, "writeFile"]);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, "onProcess"]);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

        //下面的意思是 如果下载速度小于1字节 5秒就会终止进程
        curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 1);
        curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 5);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RESUME_FROM, $localLen);

        curl_exec($ch);
        curl_close($ch);

    }
}
