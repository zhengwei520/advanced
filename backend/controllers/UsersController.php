<?php
/**
 * Created by PhpStorm.
 * User: 郑伟
 * Date: 2017/11/24
 * Time: 15:08
 */

namespace backend\controllers;


use backend\models\HeartBookingFee;
use backend\models\HeartOrders;
use backend\models\HeartTeacherRatings;
use backend\models\HeartUserComments;

class UsersController extends RevertController
{

    /**
     * 获取导师主页信息
     * @param        uid         导师id
     */
    public function actionGettutors()
    {

        $post = \yii::$app->request->post();

         $tutor = new HeartBookingFee();

         $tutors = $tutor->GetTutorsInfo($post['uid']);      // 查询导师预约费用设置

         if($tutors){

             $comm = new HeartTeacherRatings();

             $comment = $comm->GetUserAverageComment($post['uid']);      // 获取用户对导师的平均评价

                 $arr = array(
                     'msg'=>'获取成功',
                     'code'=>'1',
                     'data'=>array(
                         'tutors'=>$tutors,
                         'ratings'=>$comment,
                     )
                 );

         }else{

             switch ($tutors){
                 case -1 : $msg = "导师信息不能为空";break;
                 case -2 : $msg = "导师信息错误";break;
             }

             $arr = array(
                 'msg'=> $msg,
                 'code'=> $tutors,
                 'data' => array(),
             );

         }

         return $this->revert($arr);

    }


    /**
     * 我的预约订单
     * @param      uid            用户id
     * @param      status         用户状态（1=导师  2=普通用户）
     * @param      types          订单类型（1= 心光爱  2=语音咨询  3=有偿问答）
     */
    public function actionAverageuser()
    {
        $data = \yii::$app->request->post();

        $mem = new HeartOrders();

        $mems = $mem->BookingOrder($data);
    }

}