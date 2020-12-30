<?php
/**
 * Author:Shaun·Yang
 * Date:2020/6/12
 * Time:下午5:30
 * Description:
 */

namespace spdb;

use spdb\business\AQ49;
use spdb\business\AQ50;
use spdb\business\AQ52;
use spdb\business\AQ53;
use spdb\business\AQ54;

class SpdBank
{

    public string $wholeMsg; //业务报文体

    public string $return; //业务接口返回的数据报文

    public string $code = '9999999'; //请求接口的结果
    public string $msg = '';
    public array $data = [];


    public array $conf = [
        'projectNumber' => '99000135', //项目编码
        'projectName' => '99000135', //项目名称
        'costItemCode' => '91205', //费项编码
        'masterID' => '2000040752', //企业的唯一客户号
        'transMasterID' => '2489675304', //企业的唯一客户号
        'clientPostUrl' => 'http://192.168.50.19:5777', //客户端业务请求地址
        'clientSignUrl' => 'http://192.168.50.19:4437', //客户端签名请求地址
    ];

    public function __construct($config = [])
    {
        $this->conf = array_merge($this->conf, $config);
    }

    public function singlePayment()
    {
        return new AQ49($this);
    }

    public function singleQuery()
    {
        return new AQ50($this);
    }

    public function batchPayment()
    {
        return new AQ52($this);
    }

    public function batchQueryByDate()
    {
        return new AQ53($this);
    }

    public function batchQuery()
    {
        return new AQ54($this);
    }

    public function getSign($body)
    {
        self::log(json_encode($body,JSON_UNESCAPED_UNICODE).PHP_EOL,1);
        $bodyXML = XmlTools::encodeBody($body);
        $bodyXML = CharsetTools::utf8ToGb2312($bodyXML);
        $sign    = HttpTools::post_curls($this->conf['clientSignUrl'], $bodyXML, 'INFOSEC_SIGN/1.0');
        $sign    = CharsetTools::gbkToUtf8($sign);
        $sign    = RegexTool::getXmlValueByLabel($sign, 'sign');
        return $sign;
    }

    public function send()
    {

        $msg          = CharsetTools::utf8ToGb2312($this->wholeMsg);
        $length       = str_pad(strlen($msg) + 6, 6);
        $length       = CharsetTools::utf8ToGb2312($length);
        $message      = CharsetTools::utf8ToGb2312($length . $msg);
        $res          = HttpTools::post_curls($this->conf['clientPostUrl'], $message);
        $this->return = CharsetTools::gbkToUtf8($res);
        return $this;
    }

    public function getResult()
    {
        $returnHead = RegexTool::getXmlValueByLabel($this->return, 'head');
        $returnBody = RegexTool::getXmlValueByLabel($this->return, 'body');
        $returnCode = RegexTool::getXmlValueByLabel($returnHead, 'returnCode');
        $this->code = $returnCode;

        if ($returnCode == 'AAAAAAA') {
            $signFlag   = RegexTool::getXmlValueByLabel($returnHead, 'signFlag');
            if ($signFlag == 1) {
                $signature  = RegexTool::getXmlValueByLabel($returnBody, 'signature');
                $resultXML  = HttpTools::post_curls($this->conf['clientSignUrl'], $signature, 'INFOSEC_VERIFY_SIGN/1.0');
                $resultXML  = CharsetTools::gbkToUtf8($resultXML);
                $result     = XmlTools::decode($resultXML);
                $this->data = $result;
            }
        }else{
            $this->msg = RegexTool::getXmlValueByLabel($returnBody, 'returnMsg');
        }
        self::log('code:'.$this->code.'|'.'msg:'.$this->msg.'|'.'data:'.json_encode($this->data,JSON_UNESCAPED_UNICODE).PHP_EOL,2);
        return ['code' => $this->code, 'msg' => $this->msg, 'data' => $this->data];
    }

    public static function log($content, $type = 1)
    {
        file_put_contents('./spdBand' . $type . '.log', date('Y-m-d H:i:s').'----'.$content . PHP_EOL, FILE_APPEND);
    }
}