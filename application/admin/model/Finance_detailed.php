<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/27
 * Time: 16:27
 */

namespace app\admin\model;
use think\Model;

class Finance_detailed extends Model
{
    //获取最近六个月的消费概览
    public static function getSixFinance($data)
    {
        $where['status']=1;
        $where2=['充值','其他'];
        $res['name']=Finance_detailed::where($where)->whereNotIn('product',$where2)->group('product')->field('product')->select();
        foreach ($res['name'] as $key=>$val){
            $rest=[];
            foreach ($data as $k =>$v)
            {
                $where['product']=$val['product'];
                $result=Finance_detailed::where($where)->whereBetween('creat_time',$v)->sum('money');
                $rest[$k]=$result;
                $res['name'][$key]['name']=$val['product'];
                $res['name'][$key]['type']='bar';
                $res['name'][$key]['stack']='消费概览';
                $res['name'][$key]['barWidth']='20%';
            }

            unset($res['name'][$key]['product']);
            $res['name'][$key]['data']=$rest;
        }

        return json_encode($res['name'],JSON_UNESCAPED_UNICODE);

    }
}