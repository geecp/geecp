<?php
namespace app\index\model;

use think\Model;

/**
 * 订单表
 */
class GeeBilling extends Model
{
    public function getProductTypeAttr($var, $data)
    {
        if($data['pro_list'] == '0'){
          $proname = '账户充值';
        } else {
          $pros = json_decode($data['pro_list'], false);
          $pros = object_toArray($pros);
          foreach ($pros as $k => $v) {
              $proname .= $v['class'] . '<br />';
          }
        }
        return $proname;
    }
    //支付渠道
    public function getTypesAttr($var, $data)
    {
        switch ($data['type']) {
            case '0':
                return '消费';
                break;
            case '1':
                return '充值';
                break;
            case '2':
                return '提现';
                break;
            case '3':
                return '退款';
                break;
            case '4':
                return '产品交易';
                break;
        }
    }
    //支付渠道
    public function getChannelAttr($var, $data)
    {
        switch ($data['channel_type']) {
            case '0':
                return '余额支付';
                break;
            case '1':
                return '第三方支付';
                break;
        }
    }
    //状态
    public function getStatussAttr($var, $data)
    {
        switch ($data['status']) {
            case '0':
                return '未支付';
                break;
            case '1':
                return '已支付';
                break;
            case '2':
                return '已作废';
                break;
        }
    }
}
