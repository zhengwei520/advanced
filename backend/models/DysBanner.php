<?php

namespace backend\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "dys_banner".
 *
 * @property integer $id
 * @property integer $distinguish
 * @property string $title
 * @property integer $sort
 * @property string $images
 * @property integer $jump_scheme
 * @property string $scheme_content
 * @property string $link_address
 * @property string $remarks
 * @property integer $state
 * @property string $create_time
 */
class DysBanner extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dys_banner';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['distinguish', 'sort', 'jump_scheme', 'state'], 'integer'],
            [['scheme_content', 'link_address', 'remarks'], 'string'],
            [['create_time'], 'safe'],
            [['title'], 'string', 'max' => 50],
            [['images'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'distinguish' => '1=普通banner   2=精选banner',
            'title' => 'banner标题',
            'sort' => 'banner排序',
            'images' => 'banner图',
            'jump_scheme' => '跳转方案（1=用户主页 ，2=社区内容，3=作品详情，4=内部页面，5=外部链接，6=不跳转）',
            'scheme_content' => '跳转内容',
            'link_address' => '内部跳转链接地址',
            'remarks' => '备注',
            'state' => '状态（1=已发布   2=未发布  3=删除）',
            'create_time' => '创建时间',
        ];
    }

    public function getBanner()
    {
        $query=new Query();
        $query->from(DysBanner::tableName());
        return $query->all();
    }
}
