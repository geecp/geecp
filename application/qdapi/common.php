<?php


    /**
     * 错误输出
     * @param integer $code 错误码，必填！
     * @param string  $msg  错误信息，选填，但是建议必须有！
     * @param array   $data
     */
    function error($code, $msg = '', $data = array()) {
        $returnData = array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        );
        header('Content-Type:application/json; charset=utf-8');
        $returnStr = json_encode($returnData);
        exit($returnStr);
    }

    /**
     * 成功返回
     * @param      $data
     * @param null $code
     */
     function success($data=[], $code = null) {
        $msg='success';
        $code = is_null($code) ? 0 : $code;
        $returnData = array(
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        );
        header('Content-Type:application/json; charset=utf-8');
        $returnStr = json_encode($returnData);
        exit($returnStr);
    }