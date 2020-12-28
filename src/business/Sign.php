<?php
/**
 * Author:Shaun·Yang
 * Date:2020/12/22
 * Time:下午6:14
 * Description:
 */

namespace spdb\business;

use spdb\CharsetTools;
use spdb\HttpTools;
use spdb\RegexTool;
use spdb\XmlTools;

class Sign
{
    public static function getSign($body, $applyUrl)
    {
        $bodyXML = XmlTools::encodeBody($body);
        $bodyXML = CharsetTools::utf8ToGb2312($bodyXML);
        $sign    = HttpTools::post_curls($applyUrl, $bodyXML, 'INFOSEC_SIGN/1.0');
        $sign    = CharsetTools::gbkToUtf8($sign);
        $sign    = RegexTool::getXmlValueByLabel($sign, 'sign');
        return $sign;
    }
}