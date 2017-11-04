<?php

namespace frontend\modules\wishmarketplace\models;

use Yii;

/**
 * This is the model class for table "wish_order_import_error".
 *
 * @property integer $id
 * @property integer $wish_order_id
 * @property integer $merchant_id
 * @property string $reason
 * @property string $created_at
 */
class WishOrderImportError extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wish_order_import_error';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wish_order_id', 'merchant_id', 'reason'], 'required'],
            [['wish_order_id', 'merchant_id'], 'integer'],
            [['reason'], 'string'],
            [['created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wish_order_id' => 'Wish Order ID',
            'merchant_id' => 'Merchant ID',
            'reason' => 'Reason',
            'created_at' => 'Created At',
        ];
    }
}
