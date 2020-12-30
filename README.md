浦发银行银企直联对接项目

##使用方法

#####创建对象

    $obj = new SpdBank();   //创建对象
    
#####请求过程

    $obj->singlePayment();  //根据业务选择方法如单笔转账singlePayment
        ->setBody('','','') //设置报文的body体(设置后会自动请求签名接口加密)
        ->send()            //发送业务报文（成功后 $obj->return 可查看返回的原始报文）
        ->getResult()       //解析返回的报文，如果成功会请求验签接口将返回的业务报文解密后放到data中
        
#####返回结果

getResult方法固定返回一个通用数组 

    code 默认9999999，返回AAAAAAA为业务请求成功，具体返回参数可在data中查看
    msg  返回结果的描述，业务处理失败大多数情况会将原因返回在此处 9999999为默认值，如果请求失败可能不会返回
    data 业务请求结果的详细数据
    
###1.1 单笔转账 singlePayment

    $res = $obj->singlePayment()
        ->setBody(
            '20201225172329',   //$serialNo                 流水号
            '98',               //$amount                   转账金额
            '8888888888888888', //$adversaryAccount         对方账户
            '浦发6666666666',    //$adversaryAccountName     对方账户名称
            0,                  //$crossLineSign            跨行标志 0:我行 1:他行
            1,                  //$adversaryType            对手类型 0对公 1对私
            '备注'               //$note                     备注
        )
        ->send()
        ->getResult();
    dump($res);
    //返回示例（实际为数组）
    {
        "code": "1",
        "msg": "",
        "data": {
            "head": {
                "title": "验签名结果",
                "result": "0"
            },
            "body": {
                "sic": {
                    "body": {
                        "projectNumber": "88888888",
                        "costItemCode": "88888",
                        "transTypeCode": "2",
                        "elecChequeNo": "20201225172329",
                        "adversaryAccount": "888888888888888888",
                        "adversaryAccountName": "浦发",
                        "currency": "CNY",
                        "amount": "98",
                        "handleDate": "20201225",
                        "payChannelJnl": "8888888888888888",
                        "handleSeqNo": "88888888888888888888888888888888",
                        "handleResult": "0"
                    }
                },
                "cert": "MIIDpDCCAoygAwIBAgIFEDiTJCkwD...",
                "certdn": "CN=etest2.spdb.com.cn,O=SPDB,L=shanghai,ST=shanghai,C=CN",
                "issuer": "CN=CFCA TEST OCA1,O=China Financial Certification Authority,C=CN",
                "starttime": "Feb 19 02:45:26 2020 GMT",",=shanghai,ST=shanghai,C=CN",
                "endtime": "Feb 19 02:45:26 2021 GMT",ity,C=CN",ncial Certification Authority,C=CN",
                "certsn": "666666666"
            }
        }
    }
    //获取结果最终结果
    $res = $obj->$obj->singlePayment()->getResult($res);
    dump($res);
    {"res":true,"msg":""}
    {"res":false,"msg":"原因说明"}
    
##1.2 单笔转账查询 singleQuery

    $res = $obj->singleQuery()
        ->setBody(
            '20201225172329',   //$serialNo   流水号
            '20201225'          //$transDate  转账日期
        )
        ->send()
        ->getResult();
    dump($res);
    //返回示例（实际为数组）
    {
        "code": "1",
        "msg": "",
        "data": {
            "head": {
                "title": "验签名结果",
                "result": "0"
            },
            "body": {
                "sic": {
                    "body": {
                        "projectNumber": "88888888",
                        "costItemCode": "88888",
                        "transDate": "20201227",
                        "handleDate": "20201225",
                        "adversaryAccount": "888888888888888888",
                        "adversaryAccountName": "浦发",
                        "amount": "98.00",
                        "elecChequeNo": [],
                        "originalPayChannelJnl": "8888888888888888",
                        "handleSeqNo": "8888888888888888888888888888",88888888",
                        "handleResult": "0",
                        "failureMsg": "查询结果：该笔交易已经成功",
                        "note": [],
                        "purpose": []
                    }
                },
                "cert": "MIIDpDCCAoygAwIBAgIFEDiTJCkwDQYJKo...",
                "certdn": "CN=etest2.spdb.com.cn,O=SPDB,L=shanghai,ST=shanghai,C=CN",=shanghai,ST=shanghai,C=CN",
                "issuer": "CN=CFCA TEST OCA1,O=China Financial Certification Authority,C=CN",ncial Certification Authority,C=CN",
                "starttime": "Feb 19 02:45:26 2020 GMT",
                "endtime": "Feb 19 02:45:26 2021 GMT",
                "certsn": "1038932429"
            }
        }
    }
    //获取结果最终结果
    $res = $obj->$obj->singleQuery()->getResult($res);
    dump($res);
    {"code":1,"msg":"查询结果：该笔交易已经成功"}
    {"code":0,"msg":"失败原因"}
    {"code":2,"msg":"处理中"}
    
##2.1 批量代收付交易 batchPayment

    $res = $obj->batchPayment()
        ->setBody(
            '20201225172329',       //$serialNo    流水号
            [
                [
                    'crossLineSign' => 0,                       //是否跨行 0同行 1跨行
                    'adversaryType' => 1,                       //收付款人对公对私标志0:对公 1:对私
                    'adversaryAccountType' => 1,                //银行卡卡类型0：对公帐号 1：卡
                    'adversaryAccount' => '8888888888888888',   //对方账号
                    'adversaryAccountName' => '浦发8888888888',  //对方账户名
                    'amount' => 100.00                          //金额
                ],
                [
                    'crossLineSign' => 0,                       //是否跨行 0同行 1跨行
                    'adversaryType' => 1,                       //收付款人对公对私标志0:对公 1:对私
                    'adversaryAccountType' => 1,                //银行卡卡类型0：对公帐号 1：卡
                    'adversaryAccount' => '8888888888888888',   //对方账号
                    'adversaryAccountName' => '浦发8888888888',  //对方账户名
                    'amount' => 200.00                          //金额
                ]
            ],                      //$data        交易数据
            '000001',               //$batchNo     批次编号
            '运费'                   //$note        备注
        )
        ->send()
        ->getResult();
    dump($res);
    //返回示例（实际为数组）
    {
        "head": {
            "title": "验签名结果",
            "result": "0"
        },
        "body": {
            "sic": {
                "body": {
                    "projectNumber": "99000135",
                    "costItemCode": "91205",
                    "transTypeCode": "2",
                    "elecChequeNo": "202012301032561517",
                    "handleSeqNo": "05022020123000091834",
                    "handleDate": "20201230"
                }
            },
            "cert": "MIIDpDCCAoygAwIBAgIFEDiTJCkwDQYJKoZIhv...",
            "certdn": "CN=etest2.spdb.com.cn,O=SPDB,L=shanghai,ST=shanghai,C=CN",
            "issuer": "CN=CFCA TEST OCA1,O=China Financial Certification Authority,C=CN",
            "starttime": "Feb 19 02:45:26 2020 GMT",
            "endtime": "Feb 19 02:45:26 2021 GMT",
            "certsn": "1038932429"
        }
    }
     //获取结果最终结果
    $res = $obj->$obj->batchPayment()->getResult($res);
    dump($res);
    {"res":true,"msg":""}
    {"res":false,"msg":"原因说明"}
    
##2.2 批量代收付多批次明细结果查询交易 batchQueryByDate
##2.3 批量代收付单批次明细结果查询交易 batchQuery