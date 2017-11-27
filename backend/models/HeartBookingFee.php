<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "heart_booking_fee".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $onetoone
 * @property string $onetomany
 * @property string $foronemore
 * @property string $manytomany
 * @property string $voice_chat_one
 * @property string $voice_chat_three
 * @property string $voice_chat_five
 * @property string $consulting_with
 * @property string $create_time
 */
class HeartBookingFee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'heart_booking_fee';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid'], 'integer'],
            [['onetoone', 'onetomany', 'foronemore', 'manytomany', 'voice_chat_one', 'voice_chat_three', 'voice_chat_five', 'consulting_with'], 'number'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '导师id',
            'onetoone' => '一对一收费金额（元/次）',
            'onetomany' => '一对多收费金额（元/次）',
            'foronemore' => '多对一收费金额（元/次）',
            'manytomany' => '多对多收费金额（元/次）',
            'voice_chat_one' => '语音聊天一小时（元/次）',
            'voice_chat_three' => '语音聊天三小时（元/次）',
            'voice_chat_five' => '语音聊天五小时（元/次）',
            'consulting_with' => '问题咨询（元/次）',
            'create_time' => '创建时间',
        ];
    }
    
    /**
     * 获取导师主页信息
     * @param     uid      导师id
     * @return    -1       导师信息不能为空
     * @return    -2       导师信息错误
     */
    public function GetTutorsInfo($uid)
    {

        if (empty($uid)) return -1 ;

        $info = self::find()->select([
            'onetoone',
            'onetomany',
            'foronemore',
            'manytomany',
            'voice_chat_one',
            'voice_chat_three',
            'voice_chat_five',
            'consulting_with',
        ])->where(['uid'=>$uid])->one();

        return empty($info) ? -2 : $info;

    }
}
