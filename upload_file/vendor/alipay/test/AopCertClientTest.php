<?php

require_once '../AopCertClient.php';
require_once '../AopCertification.php';
require_once '../request/AlipayTradeQueryRequest.php';
require_once '../request/AlipayTradeWapPayRequest.php';
require_once '../request/AlipayOpenOperationOpenbizmockBizQueryRequest.php';


/**
 * 证书类型AopCertClient功能方法使用测试，特别注意支付宝更证书预计2037年会过期，请在适当时间下载更新支付更证书
 * 1、execute 证书模式调用示例
 * 2、sdkExecute 证书模式调用示例
 * 3、pageExecute 证书模式调用示例
 */


//1、execute 使用
$aop = new AopCertClient ();
$appCertPath = "应用证书路径（要确保证书文件可读），例如：/home/admin/cert/appCertPublicKey.crt";
$alipayCertPath = "支付宝公钥证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayCertPublicKey_RSA2.crt";
$rootCertPath = "支付宝根证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayRootCert.crt";

$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = '你的appid';
$aop->rsaPrivateKey = '你的应用私钥';
$aop->alipayrsaPublicKey = $aop->getPublicKey($alipayCertPath);//调用getPublicKey从支付宝公钥证书中提取公钥
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset='utf-8';
$aop->format='json';
$aop->isCheckAlipayPublicCert = true;//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内
$aop->appCertSN = $aop->getCertSN($appCertPath);//调用getCertSN获取证书序列号
$aop->alipayRootCertSN = $aop->getRootCertSN($rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

$request = new AlipayTradeQueryRequest ();
$request->setBizContent("{" .
    "\"out_trade_no\":\"20150320010101001\"," .
    "\"trade_no\":\"2014112611001004680 073956707\"," .
    "\"org_pid\":\"2088101117952222\"," .
    "      \"query_options\":[" .
    "        \"TRADE_SETTE_INFO\"" .
    "      ]" .
    "  }");
$result = $aop->execute ( $request);
var_dump($result);





//2、sdkExecute 测试
$aop = new AopCertClient ();
$appCertPath = "应用证书路径（要确保证书文件可读），例如：/home/admin/cert/appCertPublicKey.crt";
$alipayCertPath = "支付宝公钥证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayCertPublicKey_RSA2.crt";
$rootCertPath = "支付宝根证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayRootCert.crt";

$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = '你的appid';
$aop->rsaPrivateKey = '你的应用私钥';
$aop->alipayrsaPublicKey = $aop->getPublicKey($alipayCertPath);
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset='utf-8';
$aop->format='json';
$aop->isCheckAlipayPublicCert = true;//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内
$aop->appCertSN = $aop->getCertSN($appCertPath);//调用getCertSN获取证书序列号
$aop->alipayRootCertSN = $aop->getRootCertSN($rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

$request = new AlipayOpenOperationOpenbizmockBizQueryRequest ();
request.setBizContent("{\"status\":\"1001\",\"shop_id\":\"2001\"}");
$result = $aop->sdkExecute($request);
echo $result;




//3、pageExecute 测试
$aop = new AopCertClient ();
$appCertPath = "应用证书路径（要确保证书文件可读），例如：/home/admin/cert/appCertPublicKey.crt";
$alipayCertPath = "支付宝公钥证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayCertPublicKey_RSA2.crt";
$rootCertPath = "支付宝根证书路径（要确保证书文件可读），例如：/home/admin/cert/alipayRootCert.crt";

$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
$aop->appId = '你的appid';
$aop->rsaPrivateKey = '你的应用私钥';
$aop->alipayrsaPublicKey = $aop->getPublicKey($alipayCertPath);
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset='utf-8';
$aop->format='json';
$aop->isCheckAlipayPublicCert = true;//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内
$aop->appCertSN = $aop->getCertSN($appCertPath);//调用getCertSN获取证书序列号
$aop->alipayRootCertSN = $aop->getRootCertSN($rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

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




