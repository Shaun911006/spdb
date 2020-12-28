<?php
/**
 * Author:Shaun·Yang
 * Date:2020/6/15
 * Time:上午9:12
 * Description:
 */

namespace spdb;

class XmlTools
{
    /**
     * XML编码
     * @param $head
     * @param $body
     * @param string $encoding 数据编码
     * @return string
     */
    public static function encode($head, $body, $encoding = 'GB2312')
    {
        $xml = '<?xml version=\'1.0\' encoding=\'GB2312\'?><packet>';
        $xml .= self::encodeHead($head);
        $xml .= self::encodeBody($body);
        $xml .= '</packet>';
        return $xml;
    }

    public static function encodeBody($body)
    {
        return '<body>' . self::data_to_xml($body) . '</body>';
    }

    public static function encodeHead($head)
    {
        return '<head>' . self::data_to_xml($head) . '</head>';
    }

    /**
     * 数组转xml
     * @param array $data
     * @return string
     */
    private static function data_to_xml($data)
    {
        $xml = '';
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $str = self::lists_to_xml($key, $val);
            } else {
                $str = "<" . $key . ">" . $val . "</" . $key . ">";
            }
            $xml .= $str;
        }
        return $xml;
    }

    /**
     * 数组转xml
     * @param string $name
     * @param array $data
     * @return string
     */
    private static function lists_to_xml($name, $data)
    {
        $xml = '<lists name="LoopResult">';
        foreach ($data as $val) {
            $str = "<list><" . $name . ">" . $val . "</" . $name . "></list>";
            $xml .= $str;
        }
        $xml .= '</lists>';
        return $xml;
    }

    //Xml转数组
    public static function decode($xml)
    {
        libxml_disable_entity_loader(true);
        $jsonStr = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        //转码
        $jsonStr = CharsetTools::gbkToUtf8($jsonStr);
        return json_decode($jsonStr, true);
    }
}