<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "heart_orders".
 *
 * @property integer $id
 * @property integer $hid
 * @property string $tids
 * @property string $uids
 * @property string $order_num
 * @property integer $types
 * @property string $money
 * @property integer $status
 * @property string $question
 * @property string $answer
 * @property integer $start_state
 * @property integer $is_view
 * @property string $create_time
 * @property string $end_time
 */
class HeartOrders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'heart_orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hid', 'types', 'status', 'start_state', 'is_view'], 'integer'],
            [['tids', 'uids', 'question', 'answer'], 'string'],
            [['money'], 'number'],
            [['create_time', 'end_time'], 'safe'],
            [['order_num'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hid' => '房主id',
            'tids' => '导师id集合',
            'uids' => '用户id集合',
            'order_num' => '订单编号',
            'types' => '预约类型（1=心光爱  2=语音咨询  3=有偿问答）',
            'money' => '预约总金额',
            'status' => '预约方式（1=一对一 2=一对多 3=多对一 4=多对多 5=语聊1 6=语聊3  7=语聊5  8=问题咨询 ）',
            'question' => '用户提问内容',
            'answer' => '导师回答内容',
            'start_state' => '开始状态（1=未进行  2=进行中  3=已结束）',
            'is_view' => '是否已查看（1=否  2=是）',
            'create_time' => '创建时间',
            'end_time' => '结束时间',
        ];
    }


    /**
     * 获取我的预约订单
     * @param       data          数据集
     */
    public function BookingOrder($data)
    {

        $where['types'] = $data['types'];

        if($data['status'] == '1'){      // 导师的预约订单

            $where['hid'] = $data['uid'];

        }else{     // 普通用户的预约订单

//            $where['']

            
        }

    }

}
