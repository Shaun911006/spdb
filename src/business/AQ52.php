<?php
/**
 * Author:Shaun·Yang
 * Date:2020/12/22
 * Time:下午5:47
 * Description:用于查询AQ52发起的所有批次的处理状态，若返回的批次状态为终态，则代表该批次后台处理完成，可再使用AQ54查询该批次中的具体明细状态。
 */

namespace spdb\business;

use spdb\SpdBank;
use spdb\XmlTools;

class AQ52
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
     * @param array $data 转账明细
     * @param string $batchNo 批次号
     * @param string $note 备注
     * @return SpdBank
     */
    public function setBody($serialNo, $data, $batchNo, $note = '转账')
    {
        $note = $note === '' ? '转账' : $note;

        $totalNumber = 0;
        $totalAmount = 0;

        $detailedContent = [];

        foreach ($data as $k => $v) {
            $totalNumber++;
            $totalAmount += round($v['amount'], 2);
            //明细序号|是否浦发账户|收付款人对公对私标志|银行卡卡类型|对方账号|对方账户名|证件类型|证件号码|对手行行号|对手行行名|支付行号|币种|金额|手机号|企业流水号|备用信息|企业分支机构|摘要|备注|备用1|备用2|备用3
            $detailedContent[] = str_pad($k, 8, '0',STR_PAD_LEFT) . '|' . $v['crossLineSign'] . '|' . $v['adversaryType'] . '|' . $v['adversaryAccountType'] . '|' . $v['adversaryAccount'] . '|' . $v['adversaryAccountName'] . '|||||||' . round($v['amount'], 2) . '||||||' . $note . '|' . $note . '||';
        }

        $body = [
            'transMasterID' => $this->client->conf['transMasterID'],
            'projectNumber' => $this->client->conf['projectNumber'],
            'projectName' => $this->client->conf['projectName'],
            'costItemCode' => $this->client->conf['costItemCode'],
            'transType' => '2',  //交易类型编码 1代收2代付
            'elecChequeNo' => $serialNo,
            'onlyChannelFlag' => '0', //是否指定唯一渠道 0否1是
            'thirdChannelID' => '', //支付渠道编号 不指定不需要传
            'totalNumber' => $totalNumber,    //交易总笔数
            'totalAmount' => $totalAmount,    //交易总金额
            'note' => $note,
            'purpose' => $note,
            'note1' => '',
            'note2' => '',
            'note3' => '',
            'batchNo' => $batchNo,
            'detailedContent' => $detailedContent,
        ];

        $this->signature = $this->client->getSign($body);
        $this->setHead();
        $this->buildMsg();
        return $this->client;
    }

    public function setHead()
    {
        $this->head = [
            'transCode' => 'AQ52',
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
        if ($resultArray['code'] == 'AAAAAAA') {
            return ['res' => true, 'msg' => $resultArray['msg']];
        } else {
            return ['res' => false, 'msg' => $resultArray['msg']];
        }
    }
}