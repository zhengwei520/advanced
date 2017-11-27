<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "heart_user_comments".
 *
 * @property integer $id
 * @property integer $benefit_id
 * @property integer $mentor_id
 * @property string $content
 * @property integer $is_delete
 * @property string $create_time
 */
class HeartUserComments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'heart_user_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['benefit_id', 'mentor_id', 'is_delete'], 'integer'],
            [['content'], 'string'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '评价id',
            'benefit_id' => '评价人id',
            'mentor_id' => '导师id',
            'content' => '评价内容',
            'is_delete' => '是否删除（1=否  2=是）',
            'create_time' => '创建时间',
        ];
    }

}
