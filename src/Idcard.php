<?php

namespace Hnllyrp\PhpSupport;


class Idcard
{
    /**
     * 验证身份证号是否正确的函数
     *
     * @param string $id_card
     * @return bool
     */
    public static function verify($id_card = '')
    {
        if (strlen($id_card) == 18) {
            return self::idcard_checksum18($id_card);
        } elseif ((strlen($id_card) == 15)) {
            $id_card = self::idcard_15to18($id_card);
            return self::idcard_checksum18($id_card);
        }

        return false;
    }

    /**
     * 计算身份证校验码，根据国家标准GB 11643-1999
     *
     * @param string $id_card
     * @return false|string
     */
    public static function verify_number($id_card = '')
    {
        if (strlen($id_card) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($id_card); $i++) {
            $checksum += substr($id_card, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        return $verify_number_list[$mod];
    }

    /**
     * 将15位身份证升级到18位
     * @param string $id_card
     * @return false|string
     */
    public static function idcard_15to18($id_card = '')
    {
        if (strlen($id_card) != 15) {
            return false;
        }

        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($id_card, 12, 3), array('996', '997', '998', '999')) !== false) {
            $id_card = substr($id_card, 0, 6) . '18' . substr($id_card, 6, 9);
        } else {
            $id_card = substr($id_card, 0, 6) . '19' . substr($id_card, 6, 9);
        }
        return $id_card . self::verify_number($id_card);
    }

    /**
     * 18位身份证校验码有效性检查
     * @param string $id_card
     * @return bool
     */
    public static function idcard_checksum18($id_card = '')
    {
        if (strlen($id_card) != 18) {
            return false;
        }

        $idcard_base = substr($id_card, 0, 17);
        if (self::verify_number($idcard_base) != strtoupper(substr($id_card, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }
}
