<?php

namespace Hnllyrp\PhpSupport\Library;

/**
 * PHP多线程类
 * Class Thread
 */
class Thread
{
    var $hooks = array();
    var $args = array();

    /**
     * http://www.jb51.net/article/58116.htm
     *
     * $thread = new thread();
     * $thread->add_thread('action_log','a');
     * $thread->add_thread('action_log','b');
     * $thread->add_thread('action_log','c');
     * $thread->run_thread();
     *
     * function action_log($info) {
     * $log = 'log/' . microtime() . '.log';
     * $txt = $info . "rnrn" . 'Set in ' . Date('h:i:s', time()) . (double)microtime() . "rn";
     * $fp = fopen($log, 'w');
     * fwrite($fp, $txt);
     * fclose($fp);
     * }
     */
    function thread()
    {
    }

    function add_thread($func)
    {
        $args = array_slice(func_get_args(), 1);
        $this->hooks[] = $func;
        $this->args[] = $args;
        return true;
    }

    function run_thread()
    {
        if (isset($_GET['flag'])) {
            $flag = intval($_GET['flag']);
        }
        if ($flag || $flag === 0) {
            call_user_func_array($this->hooks[$flag], $this->args[$flag]);
        } else {
            for ($i = 0, $size = count($this->hooks); $i < $size; $i++) {
                $fp = fsockopen($_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT']);
                if ($fp) {
                    $out = "GET {$_SERVER['PHP_SELF']}?flag=$i HTTP/1.1rn";
                    $out .= "Host: {$_SERVER['HTTP_HOST']}rn";
                    $out .= "Connection: Closernrn";
                    fputs($fp, $out);
                    fclose($fp);
                }
            }
        }
    }
}
