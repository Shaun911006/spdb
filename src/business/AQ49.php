<?php
/**
 * Author:Shaun·Yang
 * Date:2020/12/22
 * Time:下午5:47
 * Description:单笔得首付交易
 */

namespace spdb\business;

use spdb\SpdBank;
use spdb\XmlTools;

class AQ49
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
     * @param float|string $amount 转账金额
     * @param string $adversaryAccount 对方账户
     * @param string $adversaryAccountName 对方账户名称
     * @param string|int $crossLineSign 跨行标志 0:我行 1:他行
     * @param string|int $adversaryType 对手类型 0对公 1对私
     * @param string $note 备注
     * @return SpdBank
     */
    public function setBody($serialNo, $amount, $adversaryAccount, $adversaryAccountName, $crossLineSign = '0', $adversaryType = '0', $note = '转账')
    {
        $note            = $note === '' ? '转账' : $note;
        $body            = [
            'transMasterID' => $this->client->conf['transMasterID'],
            'projectNumber' => $this->client->conf['projectNumber'],
            'costItemCode' => $this->client->conf['costItemCode'],
            'elecChequeNo' => $serialNo,
            'transTypeCode' => '2',  //交易类型编码 1代收2代付
            'crossLineSign' => $crossLineSign, //跨行标志 0:我行 1:他行
            'onlyChannelFlag' => '0', //是否指定唯一渠道 0否1是
            'payChannelNo' => '', //支付渠道编号 不指定不需要传
            'adversaryType' => $adversaryType, //对手类型 0对公 1对私
            'adversaryDocType' => '',//对手证件类型 1:身份证2:护照3:军官证4:武警证5:港澳居民来往内地通行证6:户口簿7:其他8:警官证9:执行公务证A:士兵证B:台湾同胞来往内地通行证C:临时身份证D:外国人居留证Y:营业执照Z:组织机构代码
            'adversaryIDNumber' => '', //对手证件号码
            'adversaryAccountType' => $adversaryType, //对手账号类型 0对公账户 1卡
            'adversaryAccount' => $adversaryAccount, //对手账号
            'adversaryAccountName' => $adversaryAccountName, //对手账号户名
            'adversaryBankNo' => '', //收款行行号（浦发内部版）
            'adversaryBankName' => '', //收款行行名
            'payBankNo' => '', //收款行行号
            'amount' => round($amount, 2),
            'note' => $note,
            'purpose' => $note,
            'note1' => '',
            'note2' => '',
            'note3' => ''
        ];
        $this->signature = $this->client->getSign($body);
        $this->setHead();
        $this->buildMsg();
        return $this->client;
    }

    public function setHead()
    {
        $this->head = [
            'transCode' => 'AQ49',
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