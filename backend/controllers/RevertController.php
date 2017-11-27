<?php
/**
 * Created by PhpStorm.
 * User: 郑伟
 * Date: 2017/11/18
 * Time: 17:56
 */

namespace backend\controllers;

use yii\rest\Controller;

class RevertController extends Controller
{

    /**
     * 返回数据
     */
    protected function revert($arr){

        $arrs = $this->null_filter($arr);

        return $arrs;

    }


    /** 过滤空字符
     * @param $arr
     * @return mixed
     */
    protected function null_filter($arr)
    {
        foreach($arr as $key=>&$val) {
            if(is_array($val)) {
                $val = $this->null_filter($val);
            } else {
                if($val === null){
                    $arr[$key] = '';
                }
            }
        }
        return $arr;
    }

}