<?php
namespace app\plugins\controller;
use \think\Controller;
use app\common\controller\Commoninit;

class Index extends Commoninit
{
    public function index()
    {
        $p=model('Plugins');
        $pluginsinfo=$p->getAllPlugins();
        $this->assign('pluginsinfo',$pluginsinfo);
        return $this->fetch('index');
    }

    public function addplugin()
    {
        return view();
    }

    public function pluginadd()
    {
        $input=input();


        $file_name=$input['name'].'_'.$input['version'].'.zip';

        $name=input('name/s','','htmlspecialchars');
        $range=input('range/s','','htmlspecialchars');
        $title=input('title/s','','htmlspecialchars');
        $version=input('version/s','','htmlspecialchars');
        $ability=input('ability/s','','htmlspecialchars');
        $description=input('description/s','','htmlspecialchars');
        $author=input('author/s','','htmlspecialchars');
        $url=input('url/s','','htmlspecialchars');
        $shelves=input('shelves/s','','htmlspecialchars');
        $config=input('config/s','','htmlspecialchars');
        $function=input('function/s','','htmlspecialchars');
        $parame=input('parame/s','','htmlspecialchars');
        p($input);

        $file = request()->file('modulefile');

        $res=$file->move(ROOT_PATH . 'plugin_files' . DS . 'uploads',$file_name);

        // var_dump($res);

        $filemd5= $res->md5();
        $filesha256= $res->hash('sha256');
        $filepath=$res->getSaveName();

        $p=model('Plugins');

        $insertData=[
            'name'=>$name,
            'range'=>$range,
            'title'=>$title,
            'version'=>$version,
            'ability'=>$ability,
            'description'=>$description,
            'author'=>$author,
            'url'=>$url,
            'shelves'=>$shelves,
            'config'=>$config,
            'function'=>$function,
            'parame'=>$parame,
        ];
        $pres=$p->saveData($insertData);
        if ($pres) {
            $pvData=[
            'pid'=>$p->id,
            'name'=>$name,
            'version'=>$version,
            'filepath'=>$filepath,
            'md5'=>$filemd5,
            'sha256'=>$filesha256,

            ];
            $pv=model('PluginsVersion');
            $pvres=$pv->saveData($pvData);

            p($resmsg=($pvres)?'pvres成功':$pv->getError());
            return $this->success($resmsg);
        }else{
            p($resmsg=($pres)?'成功':$p->getError());
            return $this->success($resmsg);
        }
        var_dump($filesha256);
        // var_dump($p->getLastSql());
        return;
    }

    public function updataversion()
    {
        $p=model('Plugins');
        $pid=input('pid/d','','htmlspecialchars');
        $pinfo=$p->getPluginsById($pid);
        $this->assign('pinfo',$pinfo);

        return $this->fetch();
    }

    public function pluginupdate()
    {
        $input=input();


        $pid=input('pid/d','','htmlspecialchars');
        $name=input('name/s','','htmlspecialchars');
        $version=input('version/s','','htmlspecialchars');

        $p=model('Plugins');
        $pinfo=$p->getPluginsById($pid);
        if ($pinfo['version']>$version) {
           return $this->error('版本不能小于当前版本！');
        }

        $file_name=$input['name'].'_'.$input['version'].'.zip';
        $file = request()->file('modulefile');

        $res=$file->move(ROOT_PATH . 'plugin_files' . DS . 'uploads',$file_name);

        // var_dump($res);

        $filemd5= $res->md5();
        $filesha256= $res->hash('sha256');
        $filepath=$res->getSaveName();


        $pvData=[
            'pid'=>$pid,
            'name'=>$name,
            'version'=>$version,
            'filepath'=>$filepath,
            'md5'=>$filemd5,
            'sha256'=>$filesha256,

            ];
            $pv=model('PluginsVersion');
            $pvres=$pv->updateVersion($pvData);

            p($resmsg=($pvres)?'pvres成功':$pv->getError());
            // die;
            return $this->success($resmsg,url('index'));
    }
}
