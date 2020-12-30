<?php
/**
 * Author:Shaun·Yang
 * Date:2020/12/22
 * Time:下午5:47
 * Description:批量代收付多批次明细结果查询交易
 */

namespace spdb\business;

use spdb\SpdBank;
use spdb\XmlTools;

class AQ53
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
     * @param $beginDate
     * @param $endDate
     * @param $beginNumber
     * @param $queryNumber
     * @return SpdBank
     */
    public function setBody($serialNo, $beginDate, $endDate, $beginNumber, $queryNumber)
    {
        $body = [
            'transMasterID' => $this->client->conf['transMasterID'],
            'projectNumber' => $this->client->conf['projectNumber'],
            'projectName' => $this->client->conf['projectName'],
            'costItemCode' => $this->client->conf['costItemCode'],
            'transTypeCode' => '2',  //交易类型编码 1代收2代付
            'elecChequeNo' => $serialNo,
            'handleSeqNo' => '',
            'beginDate' => $beginDate, //起始日期
            'endDate' => $endDate, //截止日期
            'beginNumber' => $beginNumber, //起始笔数
            'queryNumber' => $queryNumber, //查询笔
        ];

        $this->signature = $this->client->getSign($body);
        $this->setHead();
        $this->buildMsg();
        return $this->client;
    }

    public function setHead()
    {
        $this->head = [
            'transCode' => 'AQ53',
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
                && isset($resultArray['data']['body']['sic']['body']['lists'])
                && isset($resultArray['data']['body']['sic']['body']['lists']['list'])
                && isset($resultArray['data']['body']['sic']['body']['lists']['list']['batchHandleStatus'])
            ) {
                if ($resultArray['data']['body']['sic']['body']['lists']['list']['batchHandleStatus'] == 0) {
                    return ['code' => 1,'msg' => '成功'];
                } elseif ($resultArray['data']['body']['sic']['body']['lists']['list']['batchHandleStatus'] == 1) {
                    return ['code' => 0,'msg' => '失败'];
                } else {
                    return ['code'=>2,'msg' => '处理中'];
                }
            } else{
                return ['code'=>2,'msg' => '处理中'];
            }
        } else {
            return ['code'=>2,'msg' => '处理中'];
        }
    }
}