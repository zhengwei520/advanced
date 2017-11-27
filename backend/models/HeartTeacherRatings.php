<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "heart_teacher_ratings".
 *
 * @property integer $id
 * @property integer $uid
 * @property double $experience
 * @property integer $experience_number
 * @property double $service_attitude
 * @property integer $service_attitude_number
 * @property double $response_time
 * @property integer $response_time_number
 */
class HeartTeacherRatings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'heart_teacher_ratings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'experience_number', 'service_attitude_number', 'response_time_number'], 'integer'],
            [['experience', 'service_attitude', 'response_time'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '导师uid',
            'experience' => '体验满意度平均分',
            'experience_number' => '评分人数',
            'service_attitude' => '服务态度平均分',
            'service_attitude_number' => '服务态度评分人数',
            'response_time' => '回复及时性平均分',
            'response_time_number' => '回复及时性评分人数',
            'create_time'=>'创建时间',
        ];
    }

    /**
     *  获取用户对导师的平均评价
     * @param $uid
     */
    public function GetUserAverageComment($uid)
    {

        return self::find()->select([
            'experience',
            'service_attitude',
            'response_time'
        ])->where(['uid'=>$uid])->one();

    }

}
