<?php
/**
 * Author:Shaun·Yang
 * Date:2020/12/22
 * Time:下午5:50
 * Description:
 */

namespace spdb\business;

use spdb\SpdBank;
use spdb\XmlTools;

class AQ50
{

    private SpdBank $client;
    private array $head; //报文头数组
    private string $signature; //报文体签名串

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * 设置报文体
     * @param string $serialNo 流水号
     * @param string $originalTransDate 交易发生日期（YYYYMMDD）
     * @return SpdBank
     */
    public function setBody($serialNo, $originalTransDate)
    {
        $body            = [
            'transMasterID' => $this->client->conf['transMasterID'],
            'projectNumber' => $this->client->conf['projectNumber'],
            'costItemCode' => $this->client->conf['costItemCode'],
            'originalTransDate' => $originalTransDate, //交易日期
            'elecChequeNo' => $serialNo
        ];
        $this->signature = $this->client->getSign($body);
        $this->setHead();
        $this->buildMsg();
        return $this->client;
    }

    public function setHead()
    {
        $this->head = [
            'transCode' => 'AQ50',
            'signFlag' => 1,
            'masterID' => $this->client->conf['masterID'],
            'packetID' => date('YmdHis') . rand(100000, 999999),
            'timeStamp' => date('Y-m-d H:i:s'),
        ];
        return $this;
    }

    public function buildMsg()
    {
        $signBody               = [
            'signature' => $this->signature
        ];
        $this->client->wholeMsg = XmlTools::encode($this->head, $signBody);
    }

    public function getResult($resultArray)
    {
        //返回的结果 0失败 1成功 2处理中 3异常
        if ($resultArray['code'] == 'AAAAAAA') {
            //判断handleResult
            if (
                isset($resultArray['data']['body'])
                && isset($resultArray['data']['body']['sic'])
                && isset($resultArray['data']['body']['sic']['body'])
                && isset($resultArray['data']['body']['sic']['body']['handleResult'])
            ) {
                if ($resultArray['data']['body']['sic']['body']['handleResult'] == 0) {
                    return ['code' => 1,'msg' => $resultArray['data']['body']['sic']['body']['failureMsg']];
                } elseif ($resultArray['data']['body']['sic']['body']['handleResult'] == 1) {
                    return ['code' => 0,'msg' => $resultArray['data']['body']['sic']['body']['failureMsg']];
                } else {
                    return ['code'=>2,'msg' => ''];
                }
            } else{
                return ['code'=>2,'msg' => ''];
            }
        } else {
            return ['code'=>2,'msg' => ''];
        }
    }
}