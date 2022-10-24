<?php

namespace Hnllyrp\PhpSupport;

/**
 * 正则表达式
 * Class Rules
 */
class Rules
{
    // 密码长度 必须为6-8位英文+数字
    const password = '/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,8}$/i';

    // 匹配中文字符，数字，字母的正则表达式
    const chinese = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u';



    

}
