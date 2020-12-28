<?php
/**
 * Author:Shaun·Yang
 * Date:2020/12/22
 * Time:下午6:17
 * Description:
 */
namespace spdb;

class RegexTool
{
    /**
     * 获取XML中指定标签中的内容
     * @param $xml
     * @param $label
     * @return mixed
     */
    public static function getXmlValueByLabel($xml, $label)
    {
        preg_match_all('#<(' . $label . ')>(.+)</\1#s', $xml, $context);
        if (isset($context[2]) && isset($context[2][0])) {
            return $context[2][0];
        }
        return '';
    }
}