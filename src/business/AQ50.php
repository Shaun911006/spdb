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
    public function setBody($serialNo,$originalTransDate)
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
}