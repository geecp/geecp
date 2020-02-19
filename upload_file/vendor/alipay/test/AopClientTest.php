<?php

require_once '../AopClient.php';
require_once '../AopCertification.php';
require_once '../request/AlipayTradeQueryRequest.php';
require_once '../request/AlipayTradeWapPayRequest.php';
require_once '../request/AlipayMerchantOrderSyncRequest.php';
require_once '../request/AlipayMerchantItemFileUploadRequest.php';


/**
 * 证书类型AopCertClient功能方法使用测试
 * 1、execute 证书模式调用示例
 * 2、sdkExecute 证书模式调用示例
 * 3、pageExecute 证书模式调用示例
 */


//1、execute 使用
$aop = new AopClient ();

$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = '你的appid';
$aop->rsaPrivateKey = '你的应用私钥';
$aop->alipayrsaPublicKey = '你的支付宝公钥';
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset='utf-8';
$aop->format='json';


$request = new AlipayTradeQueryRequest ();
$request->setBizContent("{" .
    "\"out_trade_no\":\"20150320010101001\"," .
    "\"trade_no\":\"2019101122001404060523103774\"," .
    "\"org_pid\":\"2088101117952222\"," .
    "      \"query_options\":[" .
    "        \"TRADE_SETTE_INFO\"" .
    "      ]" .
    "  }");
$result = $aop->execute ( $request);
var_dump($result);



//2、sdkExecute 测试
$aop = new AopClient ();

$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = '你的appid';
$aop->rsaPrivateKey = '你的应用私钥';
$aop->alipayrsaPublicKey = '你的支付宝公钥';
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset='utf-8';
$aop->format='json';

$request = new AlipayOpenOperationOpenbizmockBizQueryRequest ();
request.setBizContent("{\"status\":\"1001\",\"shop_id\":\"2001\"}");
$result = $aop->sdkExecute($request);
echo $result;


//3、pageExecute 测试
$aop = new AopClient ();

$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = '你的appid';
$aop->rsaPrivateKey = '你的应用私钥';
$aop->alipayrsaPublicKey = '你的支付宝公钥';
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset='utf-8';
$aop->format='json';

$request = new AlipayTradeWapPayRequest ();
$request->setBizContent("{" .
    "    \"body\":\"对一笔交易的具体描述信息。如果是多种商品，请将商品描述字符串累加传给body。\"," .
    "    \"subject\":\"测试\"," .
    "    \"out_trade_no\":\"70501111111S001111119\"," .
    "    \"timeout_express\":\"90m\"," .
    "    \"total_amount\":9.00," .
    "    \"product_code\":\"QUICK_WAP_WAY\"" .
    "  }");
$result = $aop->pageExecute ( $request);
echo $result;


