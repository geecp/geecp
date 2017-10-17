<?php

// 设置当前页面的编码
function BM($code = 'utf-8'){
    header("Content-type:text/html;charset=".$code);
}

// 打印变量
function p($param, $type = 0) {
    if(config('app_debug')){
        BM();
        echo '<pre>';
        if(!$type){
            print_r($param);
        }else{
            var_dump($param);
        }
        echo '</pre>';
    }
}

function SHA256Hex($str){
    $re=hash('sha256', $str, true);
    return bin2hex($re);
}

/*
 * 以下代码实现PHP sha256() sha256_file() sha512() sha512_file() PHP 5.1.2+完美兼容
 * @param string $data 要计算散列值的字符串
 * @param boolean $rawOutput 为true时返回原始二进制数据，否则返回字符串
 * @param string file 要计算散列值的文件名，可以是单独的文件名，也可以包含路径，绝对路径相对路径都可以
 * @return boolean | string 参数无效或者文件不存在或者文件不可读时返回false，计算成功则返回对应的散列值
 * @notes 使用示例 sha256('www.wuxiancheng.cn') sha512('www.wuxiancheng.cn') sha256_file('index.php') sha512_file('index.php')
*/
/* PHP sha256() */
function sha256($data, $rawOutput=false){
        if(!is_scalar($data)){
                return false;
        }
        $data = (string)$data;
        $rawOutput = !!$rawOutput;
        return hash('sha256', $data, $rawOutput);
}
/* PHP sha256_file() */        
function sha256_file($file, $rawOutput=false){
        if(!is_scalar($file)){
                return false;
        }
        $file = (string)$file;
        if(!is_file($file) || !is_readable($file)){
                return false;
        }
        $rawOutput = !!$rawOutput;
        return hash_file('sha256', $file, $rawOutput);
}
/* PHP sha512() */
function sha512($data, $rawOutput=false){
        if(!is_scalar($data)){
                return false;
        }
        $data = (string)$data;
        $rawOutput = !!$rawOutput;
        return hash('sha512', $data, $rawOutput);
}
/* PHP sha512_file()*/
function sha512_file($file, $rawOutput=false){
        if(!is_scalar($file)){
                return false;
        }
        $file = (string)$file;
        if(!is_file($file) || !is_readable($file)){
                return false;
        }
        $rawOutput = !!$rawOutput;
        return hash_file('sha512', $file, $rawOutput);
}