<?php

namespace Hnllyrp\PhpSupport\Library;


/**
 * 验证码实现
 *
 * // 后台code.php
 * $code = new Captcha(100, 30, 20);
 * $code->make();
 *
 * // 前台
 * // <img src="code.php" alt="">
 */
class Captcha
{
    protected $width;
    protected $height;
    protected $size;
    protected $font;

    protected $res;
    protected $len = 4;

    public function __construct(int $width = 100, int $height = 30, $size = 20)
    {
        $this->width = $width;
        $this->height = $height;
        $this->size = $size;

        $this->font = realpath('data\fonts\msyh.ttf');
    }

    public function make()
    {
        $res = imagecreatetruecolor($this->width, $this->height);
        imagefill($this->res = $res, 0, 0, imagecolorallocate($res, 200, 200, 200));
        $this->text();
        $this->line();
        $this->pix();
        $this->render();
    }

    protected function text()
    {
        $text = 'abcdefghijklmnopqrstuvwxyz123456789';

        for ($i = 0; $i < $this->len; $i++) {
            $x = $this->width / $this->len * $i;
            $box = imagettfbbox($this->size, 0, $this->font, $text[mt_rand(0, strlen($text) - 1)]);
            imagettftext(
                $this->res,
                $this->size,
                mt_rand(-20, 20),
                $x,
                $this->height / 2 + ($box[0] - $box[7]) / 2,
                $this->textColor(),
                $this->font,
                strtoupper($text[mt_rand(0, strlen($text) - 1)])
            );
        }
    }

    protected function pix()
    {
        for ($i = 0; $i < 300; $i++) {
            imagesetpixel(
                $this->res,
                mt_rand(0, $this->width),
                mt_rand(0, $this->height),
                $this->color()
            );
        }
    }

    protected function line()
    {
        for ($i = 0; $i < 6; $i++) {
            imagesetthickness($this->res, mt_rand(1, 3));
            imageline(
                $this->res,
                mt_rand(0, $this->width),
                mt_rand(0, $this->height),
                mt_rand(0, $this->width),
                mt_rand(0, $this->height),
                $this->color()
            );
        }
    }

    protected function color()
    {
        return imagecolorallocate($this->res, mt_rand(100, 200), mt_rand(100, 200), mt_rand(100, 200));
    }

    protected function textColor()
    {
        return imagecolorallocate($this->res, mt_rand(50, 150), mt_rand(50, 150), mt_rand(50, 150));
    }

    protected function render()
    {
        header('Content-type:image/png');
        imagepng($this->res);
    }


}
