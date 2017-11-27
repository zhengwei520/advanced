<?php

namespace backend\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "heart_sms".
 *
 * @property integer $id
 * @property integer $status
 * @property string $phone
 * @property string $code
 * @property string $create_time
 * @property string $overdue_time
 */
class HeartSms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'heart_sms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['create_time', 'overdue_time'], 'safe'],
            [['phone'], 'string', 'max' => 11],
            [['code'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => '短信验证码状态（1=注册  2=导师登录   3=找回密码）',
            'phone' => '验证手机号',
            'code' => '验证码',
            'create_time' => '创建时间',
            'overdue_time' => '过期时间',
        ];
    }


    /**  发送验证码
     * @param string $phone          待发送手机号
     * @param string $cmsType        短信验证码状态（1=注册  2=导师登录   3=找回密码）
     * @return        -1             手机号不能为空
     * @return        -2             手机号已被注册
     * @return        -3             数据异常
     */
    public function sendout($phone='',$cmsType=1)
    {

        if(!$phone) return -1;

        if($cmsType == 1){

            $if = HeartUsers::find()->where(['phone'=>$phone])->one();

            if($if) return -2;

        }

        $sms = $this->createSMSCode('6');    // 获取验证码

        $crea = date('Y-m-d H:i:s',time());

        $over = date('Y-m-d H:i:s',time()+300); //过期时间设置为5分钟内有效

        $find = HeartSms::find()->where(['phone'=>$phone,'status'=>$cmsType])->one();

        if($find){

            $mems = HeartSms::updateAll(['code'=>$sms,'create_time'=>$crea,'overdue_time'=>$over],['id'=>$find['id']]);   // 更新验证码

        }else{

            $mem = new HeartSms();
            $mem -> code = (String)$sms;
            $mem -> status = $cmsType;
            $mem -> phone = $phone;
            $mem -> create_time = $crea;
            $mem -> overdue_time = $over;
            $mems = $mem ->save();      // 添加新验证码

//            return  $mem ->getErrors();die;       打印错误信息

        }

        try{
            // $send = sendMms($sms,$phone);  // 发送短信验证码
            return empty($mems) ?   -3 : $sms ;
        }catch (\Exception $e){
            throw $e;
        }

    }

    /** 随机生成X位数
     * @param int $length
     * @return int
     */
    public function createSMSCode($length = ''){
        $min = pow(10 , ($length - 1));
        $max = pow(10, $length) - 1;
        $mem = rand($min, $max);
        return $mem;
    }


    /** 验证短信验证码
     * @param $code              短信验证码
     * @param $phone             手机号
     * @param string $type       验证码状态
     * @return int  -1  = 手机号或验证码不能为空  -2 = 手机号或状态错误  -3 = 验证码错误  -4 = 验证码已过期
     */
    public function VerificatCode($code,$phone,$typex)
    {
        if(empty($code) || empty($phone)) return  -1;

        $query = HeartSms::find()->where('phone = "'.$phone.'" and status = "'.$typex.'"')->one();

        if (empty($query))  return -2 ;

        if($query['code'] != $code)  return -3;

        if(strtotime($query['overdue_time']) < time())  return -4;

        return 1;

    }


}
