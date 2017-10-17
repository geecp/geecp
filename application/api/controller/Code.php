<?php
namespace app\api\controller;
class Code
{
    public function code(){
        error_reporting(E_ERROR);
        require_once VENDOR_PATH. 'phpqrcode/phpqrcode.php';

        //$url = urldecode($_GET["data"]);
        $level = 'L';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 10;
        $qrcode=new \QRcode();
        $qrcode->png('123',false, $level, $size);
        exit;
    }
}