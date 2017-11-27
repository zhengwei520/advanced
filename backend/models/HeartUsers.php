<?php

namespace backend\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "heart_users".
 *
 * @property integer $id
 * @property string $reference_phone
 * @property string $nickname
 * @property string $pwd
 * @property string $account_pwd
 * @property string $salt
 * @property string $real_name
 * @property string $phone
 * @property string $photo
 * @property string $signature
 * @property string $introduction_mentor
 * @property string $heart_light
 * @property string $influence
 * @property integer $grade
 * @property string $wealth_value
 * @property integer $state
 * @property integer $status
 * @property string $create_time
 */
class HeartUsers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'heart_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['photo', 'signature', 'introduction_mentor'], 'string'],
            [['grade', 'state', 'status'], 'integer'],
            [['wealth_value'], 'number'],
            [['create_time'], 'safe'],
            [['reference_phone', 'phone'], 'string', 'max' => 11],
            [['nickname', 'pwd', 'account_pwd'], 'string', 'max' => 50],
            [['salt', 'heart_light', 'influence'], 'string', 'max' => 10],
            [['real_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户ID',
            'token' => '云信token',
            'reference_phone' => '推荐人手机号',
            'nickname' => '昵称',
            'pwd' => '密码',
            'account_pwd' => '账户密码',
            'salt' => '盐',
            'real_name' => '真实姓名',
            'phone' => '手机号',
            'photo' => '头像',
            'signature' => '个性签名',
            'introduction_mentor' => '导师介绍',
            'heart_light' => '心光值',
            'influence' => '影响力',
            'grade' => '用户等级（1=普通会员  2=VIP会员  3=导师  4=仙师   5=直播导师）',
            'wealth_value' => '财富值',
            'state' => '是否接收预约（1=否  2=是）',
            'status' => '账户状态（1=正常  2=锁定）',
            'create_time' => '创建时间',
        ];
    }

    /** 注册
     * @param $data    注册信息
     */
    public function registrat($data)
    {

        $sms = new HeartSms();

        $smstype = $sms->VerificatCode($data['code'],$data['phone'],$data['cmsType']);   // 验证短信验证码

        if($smstype < 1 ) return $smstype;

        $salt = (string)$sms->createSMSCode('8');    // 获取盐

        $name = empty($data['nickname']) ? $data['phone'] : $data['nickname'];

        try{
            /** @var 注册 start $mem */
            $mem = new HeartUsers();
            if($data['reference_phone']) $mem -> reference_phone = $data['reference_phone'];
            $mem -> nickname = $name;
            $mem -> phone = $data['phone'];
            $mem -> pwd = $this->encryption($data['pwd'],$salt);
            $mem -> photo = 'http://dayisi.oss-cn-shanghai.aliyuncs.com/Uploads/2017-10-21/59eb03aea5dd9.png';
            $mem -> salt = $salt;
            $mem -> heart_light = '100';
            $mem -> create_time = date('Y-m-d H:i:s',time());
            $add = $mem ->save();

            if($data['reference_phone']){   // 根据推荐人电话更新影响力

                $addInfluence = self::find()->where(['phone'=> $data['reference_phone']])->one();

                if($addInfluence){

                    $addInfluence['influence'] += 100;   // 添加影响力

                    $addInfluence->save();
                }

        }

            /** @var 注册 end $mem */
            return $mem['id'];
        }catch (Exception $e){
             return $e;
        }

    }


    /**
     * 更新云信token
     * @param      $uid        云信注册成功返回的accid
     * @param      $token      云信注册成功返回的token
     */
    public function UpdateToken($uid,$token)
    {

        $save  = self::find()->where(['id'=> $uid])->one();

        $save['token'] = $token;

        return $save->save();
    }


    /**
     * 加密
     * @param   pwd    明文
     * @param   salt   盐
     * return    password   密文
     */
    public function encryption($pwd , $salt){
        $enc = md5($pwd.$salt.$salt);
        return $enc;
    }


    /**
     * 根据手机号查询Id以及账号状态
     */
    public function getUid($phone)
    {
        return HeartUsers::find()->select(['id','status'])->where(['phone' => $phone])->one();
    }


    /** 统计未查看的新预约订单数
     * @param $uid
     */
    public function statisticalOrderNumber($uid)
    {

        return self::find()->andWhere(['id'=>$uid])->count('id');

    }
    
    /**
     * 根据用户id获取用户信息
     * @param    $uid         用户id
     * @return   $array       数组返回
     */
    public function getUsersInfo($uid)
    {

        return HeartUsers::find()->select(['uid'=>'id','token','nickname','real_name','phone','photo','signature',
        'introduction_mentor','heart_light','influence','grade','wealth_value','state'])->where(['id' => $uid])->asArray()->one();

    }


    /** 会员登录验证
     * @param $loginName
     * @param $pwd
     * @return    -1      登录账号或密码不能为空
     * @return    -2      此账号未注册
     * @return    -3      该账号已被锁定
     * @return    -4      密码错误
     * @return    -5      数据异常
     * @return    int     用户id
     */
    public function Logins($loginName,$pwd)
    {

        if(empty($loginName) || empty($pwd))    return -1;

        $getuserinfo = HeartUsers::find()->select(['id','pwd','salt','status'])->where(['phone'=>$loginName])->one();

        if(empty($getuserinfo))    return -2;

        if($getuserinfo['status'] == '2') return -3;

        $pwds = $this->encryption($pwd,$getuserinfo['salt']);

        if($pwds != $getuserinfo['pwd']) return -4;

        return $getuserinfo['id'] > 0 ? $getuserinfo['id'] : -5;

    }


    /**
     * 找回密码
     * @param        phone       手机号
     * @param        pwd         新密码
     * @return       -5          手机号错误或已被锁定
     * @return       -6          系统错误
     */
    public function RetrievePwd($phone,$pwd)
    {

        $save = self::find()->where(['phone'=>$phone,'status'=>1])->one();

        if($save){

            $sms = new HeartSms();

            $salt = (string)$sms->createSMSCode('8');    // 获取新盐

            $save-> pwd = $this->encryption($pwd,$salt);

            $save -> salt = $salt;

            $mem = $save->save();

            if($mem){

                return 1;

            }else{

                return -6;

            }

        }else{

            return -5;

        }


    }

}
