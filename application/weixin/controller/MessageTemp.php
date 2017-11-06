<?php
namespace app\weixin\controller;
use think\Controller;
use app\weixin\controller\Base;
class MessageTemp extends Controller
{
    public function template($data)
    {
        $base=new Base();
        $access_token=$base->base();
        //接收所要发送的信息类型
        $array=[];
        switch ($data['type']){
            case 'remind':
                $post_data='OPENTM406727153';
                $array=[
                    'first'=>['value'=>$data['first'],'color'=>'#173177'],
                    'keyword1'=>['value'=>$data['keyword1'],'color'=>'#173177'],
                    'keyword2'=>['value'=>$data['keyword2'],'color'=>'#173177'],
                    'keyword3'=>['value'=>$data['keyword3'],'color'=>'#173177'],
                    'keyword4'=>['value'=>$data['keyword4'],'color'=>'#173177'],
                    'keyword5'=>['value'=>$data['keyword5'],'color'=>'#173177'],
                    'remark'=>['value'=>$data['remark'],'color'=>'#173177']
                ];
                break;
            case 'shop':
                $post_data='OPENTM402058454';
                $array=[
                    'first'=>['value'=>$data['first'],'color'=>'#173177'],
                    'keyword1'=>['value'=>$data['keyword1'],'color'=>'#173177'],
                    'keyword2'=>['value'=>$data['keyword2'],'color'=>'#173177'],
                    'keyword3'=>['value'=>$data['keyword3'],'color'=>'#173177'],
                    'keyword4'=>['value'=>$data['keyword4'],'color'=>'#173177'],
                    'keyword5'=>['value'=>$data['keyword5'],'color'=>'#173177'],
                    'remark'=>['value'=>$data['remark'],'color'=>'#173177']
                ];
                break;
            case 'buy':
                $post_data='TM00018';
                $array=[
                    'productType'=>['value'=>$data['productType'],'color'=>'#173177'],
                    'name'=>['value'=>$data['name'],'color'=>'#173177'],
                    'number'=>['value'=>$data['number'],'color'=>'#173177'],
                    'expDate'=>['value'=>$data['expDate'],'color'=>'#173177'],
                    'remark'=>['value'=>$data['remark'],'color'=>'#173177']
                ];
                break;
            case 'doaminremind':
                $post_data='OPENTM206956452';
                $array=[
                    'first'=>['value'=>$data['first'],'color'=>'#173177'],
                    'keyword1'=>['value'=>$data['keyword1'],'color'=>'#173177'],
                    'keyword2'=>['value'=>$data['keyword2'],'color'=>'#173177'],
                    'keyword3'=>['value'=>$data['keyword3'],'color'=>'#173177'],
                    'remark'=>['value'=>$data['remark'],'color'=>'#173177']
                ];
                break;
        }
        //获取模板ID
        $url="https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=".$access_token['access_token'];
        $data['template_id_short']=$post_data;
        $res=postCurl($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        if(!$res['errcode']){
            $message['template_id']=$res['template_id'];
            $message['array']=$array;
            return $message;
        }else{
            return ['template_id'=>0,'msg'=>'获取模板ID错误'];
        }

    }
}