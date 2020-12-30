<?php
/**
 * Author:Shaun·Yang
 * Date:2020/12/22
 * Time:下午5:47
 * Description:批量代收付单批次明细结果查询交易
 */

namespace spdb\business;

use spdb\SpdBank;
use spdb\XmlTools;

class AQ54
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
     * @param string $transDate 交易日期(YYYYMMDD)
     * @return SpdBank
     */
    public function setBody($serialNo, $transDate)
    {
        $body = [
            'transMasterID' => $this->client->conf['transMasterID'],
            'projectNumber' => $this->client->conf['projectNumber'],
            'projectName' => $this->client->conf['projectName'],
            'costItemCode' => $this->client->conf['costItemCode'],
            'transTypeCode' => '2',  //交易类型编码 1代收2代付
            'electronNumber' => $serialNo,
            'transDate' => $transDate, //起始日期
            'handleSeqN' => '',
       ];

        $this->signature = $this->client->getSign($body);
        $this->setHead();
        $this->buildMsg();
        return $this->client;
    }

    public function setHead()
    {
        $this->head = [
            'transCode' => 'AQ54',
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
}