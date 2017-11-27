<?php
namespace backend\controllers;
use backend\common\helper\BaseHelper;
use backend\common\helper\YunxinServer;
use backend\models\DysBanner;
use backend\models\HeartSms;
use backend\models\HeartUsers;

class LoginController extends RevertController
{

    private $model;
    private $AppKey = '97293f96960e47e379413cbb8b756d4d';     // key
    private $AppSecret = '5b0cb9a5d885';  // secret

    /**
     * 测试
     * @return string
     */
    public function actionAbc()
    {
        //        $parasm = \Yii::$app->request->get();
        $a=1 ;
        $a=1 ;
//        $banner = new DysBanner();
//        $a=$banner->getBanner();
//        var_dump(BaseHelper::a());
//        die;
        return 111;
    }

    public function _initialize(){
        // 实例云信的库
        $this->model = new YunxinServer($this->AppKey,$this->AppSecret,'curl');
    }

    /**
     * 创建云信ID
     * @param    $userid           创建的用户id
     */
    public function createUserId($userid)
    {
        $state = new HeartUsers();      // 实例化用户表

        $data = $state->getUsersInfo($userid);   // 获取用户基础信息

        $token = md5($data['uid'].'abc');
        // 写入到云信服务器
        $accid = $data['uid'];

        $name = $data['nickname'];

        $icon = '';

        $info = new YunxinServer($this->AppKey,$this->AppSecret,'curl');

        $infos = $info->createUserIds($accid,$name,'{}',$icon,$token);

        return $infos;
    }


    /**
     * 发送短信验证码
     * @param      phone           手机号
     * @param      cmsType         短信验证码状态( 1=注册  2=导师登录   3=找回密码)
     */
    public function actionSendcode()
    {

        $param = \Yii::$app->request->post();

        $sms = new HeartSms();

        $data = $sms->sendout($param['phone'],$param['cmsType']);

        if($data > 0){
            $arr = array(
                'msg'=> '发送成功',
                'code' => 1,
                'data' => $data,
            );
        }else{
            switch ($data){
                case -1 : $msg = '手机号不能为空';break;
                case -2 : $msg = '手机号已被注册';break;
                case -3 : $msg = '数据异常';break;
            }

            $arr = array(
                'msg'=> $msg,
                'code' => $data,
                'data' => array(),
            );
        }

        return $this->revert($arr);

    }
    

    /**
     * 注册
     * @param      phone                   手机号
     * @param      pwd                     密码
     * @param      code                    验证码
     * @param      cmsType                 短信验证码状态( 1=注册  2=导师登录   3=找回密码)
     * @param      nickname（no）          昵称
     * @param      reference_phone（no）   推荐人手机号
     */
    public function actionRegistrat()
    {

        $data = \Yii::$app->request->post();

        $user = new HeartUsers();

        $registrat = $user->registrat($data);   // 获取注册返回信息

        if($registrat > 0){

            $yunxin = $this->createUserId($registrat);     // 创建云信ID   返回accid     token

            if($yunxin['code'] == '200' ){       //   创建成功

                $save = $user->UpdateToken($yunxin['info']['accid'],$yunxin['info']['token']);

                $arr = array(
                    'msg'=> '注册成功',
                    'code'=>1,
                    'data'=>array(),
                );

            }else{
                $arr = array(
                    'msg'=> '创建云信ID错误',
                    'code' => '-'.$yunxin['code'],
                    'data' => array(),
                );
            }

        }else{
            switch ($registrat){
                case -1 : $msg = '手机号或验证码不能为空';break;
                case -2 : $msg = '手机号或状态错误';break;
                case -3 : $msg = '验证码错误';break;
                case -4 : $msg = '验证码已过期';break;
                default:  $msg = $registrat;
            }

            $arr = array(
                'msg'=> $msg,
                'code' => $registrat,
                'data' => array(),
            );
        }

        return $this->revert($arr);

    }


    /**
     * 会员登录
     * @param           login_name       登录名
     * @param           pwd              登录密码
     *
     */
    public function actionLogins()
    {

        $data = \Yii::$app->request->post();

        $login = new HeartUsers();

        $uid = $login->Logins(trim($data['login_name'],' '),$data['pwd']);   // 获取用户uid

        if($uid > 0){

            $getUserInfo = $login->getUsersInfo($uid);

            $getUserInfo['views'] = $login->statisticalOrderNumber($uid);     // 获取新预约订单数

            if($getUserInfo){

                $arr = array(
                    'msg'=> '获取成功',
                    'code'=> '1',
                    'data'=>$getUserInfo,
                );

            }else{
                $arr = array(
                    'msg'=> '账号异常',
                    'code'=> '-6',
                    'data'=>array(),
                );
            }

        }else{

            switch ($uid){
                case -1 : $msg = "登录账号或密码不能为空";break;
                case -2 : $msg = "此账号未注册";break;
                case -3 : $msg = "该账号已被锁定";break;
                case -4 : $msg = "密码错误";break;
                case -5 : $msg = "数据异常";break;
            }

            $arr = array(
                'msg'=> $msg,
                'code'=>$uid,
                'data'=>array(),
            );
        }

        return $this->revert($arr);

    }


    /**
     * 导师登录
     * @param    code           短信验证码
     * @param    phone          手机号
     * @param    type           验证码状态
     */
    public function actionMentorlogin()
    {

        $data = \Yii::$app->request->post();

        $sms = new HeartSms();

        $smss = $sms->VerificatCode($data['code'],$data['phone'],$data['type']);  // 导师登录验证短信验证码

        if($smss > 0 ){    // 验证通过

            $getUserInfo = new HeartUsers();

            $uid = $getUserInfo->getUid($data['phone']);

            if($uid['status'] == '1' && $uid['id'] > 0){

                $getUserInfos = $getUserInfo->getUsersInfo($uid['id']);    //  获取导师信息

                $getUserInfo['views'] = $getUserInfo->statisticalOrderNumber($uid['id']);     // 获取新预约订单数

                $arr = array(
                    'msg'=> '登录成功',
                    'code'=> '1',
                    'data'=> $getUserInfos,
                );

            }else{

                $arr = array(
                    'msg'=> '该账号已被锁定',
                    'code'=> '-5',
                    'data'=> array(),
                );

            }

        }else{  // 验证失败

            switch ($smss){
                case -1 : $msg = "手机号或验证码不能为空";break;
                case -2 : $msg = "手机号或状态错误";break;
                case -3 : $msg = "验证码错误";break;
                case -4 : $msg = "验证码已过期";break;
            }

            $arr = array(
                'msg' => $msg,
                'code'=> $smss,
                'data'=> array(),
            );

        }

        return $this->revert($arr);

    }
    
    
    /**
     * 找回密码
     * @param          phone    手机号
     * @param          code     验证码
     * @param          pwd      新密码
     * @param          cmsType  短信验证码状态（1=注册  2=导师登录   3=找回密码）
     */
    public function actionRetrievepwd()
    {

        $data = \Yii::$app->request->post();

        $sms = new HeartSms;

        $smss = $sms->VerificatCode($data['code'],$data['phone'],$data['cmsType']);       // 验证短信验证码

        if($smss > 0){

            $user = new HeartUsers();        // 更换密码

            $savepwd = $user->RetrievePwd($data['phone'],$data['pwd']);

            if($savepwd > 0){

                $arr = array(
                    'msg' => '更新成功',
                    'code' => '1',
                    'data' => array(),
                );

            }else{

                switch ($savepwd){
                    case -5 : $msg = "手机号错误或已被锁定";break;
                    case -6 : $msg = "系统错误";break;
                }

                $arr = array(
                    'msg'=> $msg,
                    'code'=>$savepwd,
                    'data'=>array(),
                );

            }

        }else{

            switch ($smss){
                case -1 : $msg = '手机号或验证码不能为空';break;
                case -2 : $msg = '手机号或状态错误';break;
                case -3 : $msg = '验证码错误';break;
                case -4 : $msg = '验证码已过期';break;
            }

            $arr = array(
                'msg'=> $msg,
                'code'=>$smss,
                'data'=> array(),
            );

        }

        return $this->revert($arr);
        
    }


}