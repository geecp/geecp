<?php
namespace app\template\controller;
use app\template\controller\Base;
use app\template\model\VhostProduct;
use think\Db;
use think\View;


class Index extends Base
{
    public function Index()
    {
        $this->assign('root','./../plugins/themes/qiduo');
        return view();
    }

    public function About()
    {
        return view();
    }

    public function Agent()
    {
        //查询联系人数据
        $res1=Db::name('linkman')->count();
        //计算总数据行数（一行4个数据）
        $page=ceil($res1/4);
        //循环查询联系人信息
        for($i=1;$i<$page+1;$i++)
        {
            $res2[]=Db::name("linkman")->limit(($i-1)*4,4)->select();
        }
        //赋值
        $this->assign('res2',$res2);

        return view('../plugins/themes/nitian/index/agent.html');
    }

    public function Cloud()
    {
        return view();
    }

    public function Col()
    {
        return $this->fetch();
    }

    public function Domain()
    {
        //不需要
        return view();
    }

    public function Education()
    {
        //不需要
        return view();
    }

    public function Elasticw()
    {
        //不需要
        return view();
    }

    public function Finance()
    {
        //不需要
        return view();
    }

    public function Generalweb()
    {
        //不需要
        return view();
    }

    public function Help()
    {

        return view();
    }

    public function Historyus()
    {
        //不需要
        return view();
    }

    public function Honorus()
    {
        //不需要
        return view();
    }

    public function Host()
    {
        //查询分类组
        $res1=Db::name('productgroup')->where('erji',"gainet")->select();

        //循环查询产品名称加入子数组
        for($i=0;$i<count($res1);$i++)
        {
            $res1[$i]['vhostproduct']=Db::name('vhostproduct')->where('pro_id',$res1[$i]['id'])->select();
        }

        for($k=0;$k<count($res1);$k++)
        {
            for($z=0;$z<count($res1[$k]['vhostproduct']);$z++)
            {
                $res1[$k]['productprice']=Db::name('product_price')->where('p_id',$res1[$k]['vhostproduct'][$z]['id'])->select();
            }
        }

        $this->assign("res1",$res1);
        //dump(VhostProduct::getVhost());
        // dump($res1);
        return view();
    }

    public function Idc()
    {
        //读取该模块下的产品数据
        $res1=Db::name('productgroup')->where('erji','server')->select();

        //循环查询服务器
        for($i=0;$i<count($res1);$i++)
        {
            $res1[$i]['serverhost']=Db::name('serverhost')->where('room',$res1[$i]['id'])->select();
        }
        //循环查询价格
        for($k=0;$k<count($res1);$k++)
        {
            for($l=0;$l<count($res1[$k]['serverhost']);$l++)
            {
                $res1[$k]['serverhost'][$l]['productprice']=Db::name('product_price')->where('p_id','in',$res1[$k]['serverhost'][$l]['title'])->select();
            }

        }
        //赋值
        $this->assign('res1',$res1);


        return view();
    }

    public function Mixturecloud()
    {
        //不需要
        return view();
    }

    public function Allnews()
    {
        //获取所有新闻
        $res2=Db::name("news")->order('createtime')->paginate(10);
        $page = $res2->render();
        $this->assign('res2',$res2);
        $this->assign('page', $page);
        //计算总共几条数据
        $all=Db::name("news")->order('createtime')->count();
        $this->assign('all',$all);
        $allpage=ceil($all/10);
        $this->assign('allpage',$allpage);
        for($i=1;$i<=$allpage;$i++)
        {
            $res3[]=$i;
        }
        $this->assign('res3',$res3);

        return view();
    }

    public function News()
    {
        //获取所有新闻动态
        $res2=Db::name("news")->where('classify',1)->order('createtime')->paginate(10);
        $page = $res2->render();
        $this->assign('res2',$res2);
        $this->assign('page', $page);
        //计算总共几条数据
        $all=Db::name("news")->where('classify',1)->order('createtime')->count();
        $this->assign('all',$all);
        $allpage=ceil($all/10);
        $this->assign('allpage',$allpage);
        for($i=1;$i<=$allpage;$i++)
        {
            $res3[]=$i;
        }
        $this->assign('res3',$res3);

        return view();
    }

    public function Productnews()
    {
        //获取所有新闻动态
        $res2=Db::name("news")->where('classify',2)->order('createtime')->paginate(10);
        $page = $res2->render();
        $this->assign('res2',$res2);
        $this->assign('page', $page);
        //计算总共几条数据
        $all=Db::name("news")->where('classify',2)->order('createtime')->count();
        $this->assign('all',$all);
        $allpage=ceil($all/10);
        $this->assign('allpage',$allpage);
        for($i=1;$i<=$allpage;$i++)
        {
            $res3[]=$i;
        }
        $this->assign('res3',$res3);



        return view();
    }

    public function News2()
    {
        return view();
    }

    public function  ShowAllNews()
    {


        $searchkey='%'.input('searchkey').'%';
        // print($searchkey);
        $res2=Db::name("news")->whereLike("title",$searchkey)->order('createtime')->paginate(10);
        $page = $res2->render();
        $this->assign('res2',$res2);
        $this->assign('page', $page);
        //计算总共几条数据
        $all=Db::name("news")->whereLike("title",$searchkey)->order('createtime')->count();
        $this->assign('all',$all);
        $allpage=ceil($all/10);
        $this->assign('allpage',$allpage);

        if($allpage!=0)
        {
            for($i=1;$i<=$allpage;$i++)
            {
                $res3[]=$i;
            }

        }
        else
        {
            $res3=null;
        }
        $this->assign('res3',$res3);

        return view();

    }

    public function Pay()
    {
        //没有
        return view();
    }

    public function Ssl()
    {
        //没有表
        return view();
    }

    public function Vps()
    {
        //查询vps产品
        $arr1=Db::name('vps_product')->select();

        //循环查询产品组
        for($i=0;$i<count($arr1);$i++)
        {
            $arr1[$i]['productgroup']=Db::name('productgroup')->whereIn('id',$arr1[$i]['room'])->select();
        }
        //循环查询system字段中数据对应的system名称
        for($k=0;$k<count($arr1);$k++)
        {
            $server=$arr1[$k]['system'];
            $arr2=json_decode($server,true);
            $arr1[$k]['systemname']=Db::name('system')->where('id','in',$arr2)->select();
        }
        //循环查询价格
        for($j=0;$j<count($arr1);$j++)
        {
            $arr1[$j]['productprice']=Db::name('product_price')->where('p_id','in',$arr1[$j]['id'])->select();
        }
        //赋值
        $this->assign('res1',$arr1);
        return view();
    }

    public function Msm()
    {
        //查询短信数据
        $res1=Db::name('sms')->select();
        //循环计算单价并加入数组



        $this->assign('res1',$res1);
        return view();
    }

}
