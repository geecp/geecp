<?php
namespace app\index\model;
use think\Model;
use app\admin\model\GeeProductGroup; //产品组表
/**
 * 产品表-Vhost
 */
class GeeProductVHost extends Model
{

    private $productType = 1;//虚拟主机默认为1,详情观看数据库注释

    protected $name = 'GeeProductVHost';

    protected $table = 'gee_product';

    protected $autoWriteTimestamp = true;

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $readonly = ['type', 'create_time'];

    protected function base($query){
        $query->where('type', $this -> productType);
    }

    public function add($data){
        $data['type'] = $this -> productType;
        return $this::create($data);
    }

    public function getDataById($id){
        return $this::get(array(
            'id' => $id
        ), null, true);
    }

    // public function where(&$query, $field, $op = null, $condition = null){
    //     if(!empty($condition)){
    //         $query -> where($field, $op, $op == 'LIKE' ? '%$condition%' : $condition);
    //     }
    // }

    public function getGroup(){
        $group = new GeeProductGroup();
        $table = $group -> table;
        // DISTINCT(`gee_product_group`.`id`) AS 'id'
        $data = $group 
                -> field('DISTINCT(`' . $table . '`.`id`) AS \'id\'')
                -> field('`' . $table . '`.`name`')
                -> field('`' . $table . '`.`slogan`')
                -> field('`' . $table . '`.`sort`')
                -> order('sort', 'DESC')
                -> join($this -> table, '`' . $this -> table . '`.`group_id` = `' . $table . '`.`id` AND `' . $this -> table . '`.`type` = ' . $this -> productType)
                
                -> select();
        return $data;
    }

    public function getProductByGroup(&$group_id){
        return $this
               -> field(['id', 'name', 'sort', 'describe', 'month'])
               -> where([ 'group_id' => $group_id ])
               -> order('sort', 'DESC')
               -> select();
    }

    public function getProductPrice(&$id, &$len){
        $data = $this
                -> where([ 'id' => $id ])
                -> find();
        if(empty($data)){
            return;
        }
        $price = 0;

        

        $threeYear = floor($len / 36);
        
        $twoYears = floor($len % 36 / 24);

        $years = floor($len / 12) % 3 % 2;
        
        $halfYear = floor($len / 6 % 2);
        
        $quarter = floor($len / 3 % 2);

        $month = $len % 3;
        
        $price = $data['triennium'] * $threeYear + $data['biennium'] * $twoYears + $data['years'] * $years + $data['semestrale'] * $halfYear + $data['quarter'] * $quarter + $data['month'] * $month;

        $month = $len % 12;

        $year = floor($len / 12);

        $timestr = [];

        if($year != 0){
            array_unshift($timestr, $year, '年');
        }
        if($month != 0){
            array_push($timestr, $month, '月');
        }


        
        return [
            'price' => $price,
            'name' => $data['name'],
            'time' => join($timestr, '')
        ];
    }
}
