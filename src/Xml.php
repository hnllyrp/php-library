<?php

namespace Hnllyrp\PhpSupport;


class Xml
{
    /**
     * 数组转xml
     * @param array $data
     * @param string $basenode 根节点
     * @param string $encoding
     * @return string
     */
    public static function array_to_xml($data = [], $basenode = 'xml', $encoding = 'UTF-8')
    {
        $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $xml .= "<$basenode>";
        $xml .= self::data_to_xml($data);
        $xml .= "</$basenode>";
        return $xml;
    }

    public static function data_to_xml($data = [])
    {
        $xml = '';
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . self::data_to_xml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }

        return $xml;
    }

    public static function xml_to_array($xml = '')
    {
        $obj = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($obj);
        return json_decode($json, true);
    }

    /**
     * 最简单的XML转数组
     * @param string $xmlstring XML字符串
     * @return array XML数组
     */
    function simple_xml_to_array($xmlstring = '')
    {
        return json_decode(json_encode((array)simplexml_load_string($xmlstring)), true);
    }
}
