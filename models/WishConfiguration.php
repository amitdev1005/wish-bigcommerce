<?php

namespace frontend\modules\wishmarketplace\models;

use Yii;

/**
 * This is the model class for table "walmart_configuration".
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property string $consumer_id
 * @property string $secret_key
 * @property string $consumer_channel_type_id
 * @property string $skype_id
 */
class WishConfiguration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wish_configuration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['merchant_id', 'client_id', 'client_secret_key','redirect_url'], 'required'],
            [['merchant_id'], 'integer'],
            [['client_id', 'client_secret_key','code','access_token','redirect_url'], 'string'],
            [['auth_key'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => 'Merchant ID',
            'client_id' => 'Client Id',
            'client_secret_key' => 'Client Secret key',
            'access_token' => 'Token',
            'redirect_url'=>'Redirect Url'
        ];
    }
}
