<?php

namespace frontend\modules\wishmarketplace\models;

use Yii;
use frontend\modules\wishmarketplace\models\JetProduct;
use frontend\modules\wishmarketplace\models\WishProductVariants;

/**
 * This is the model class for table "walmart_product".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $merchant_id
 * @property string $walmart_attributes
 * @property string $product_type
 * @property string $category
 * @property string $error
 * @property string $tax_code
 * @property double $min_price
 * @property string $short_description
 * @property string $self_description
 *
 * @property User $merchant
 * @property JetProduct $product
 */
class WishProduct extends \yii\db\ActiveRecord
{
    const PRODUCT_STATUS_UPLOADED = 'pending';//require changes approved
    const PRODUCT_STATUS_APPROVED = 'approved';
    const PRODUCT_STATUS_REJECTED = 'rejected';//correct
    const PRODUCT_STATUS_STAGE = 'STAGE';
    const PRODUCT_STATUS_NOT_UPLOADED = 'Not Uploaded';
    const PRODUCT_STATUS_PROCESSING = 'Items Processing';
    const PRODUCT_STATUS_PARTIAL_UPLOADED = 'PARTIAL UPLOADED';
    const PRODUCT_STATUS_DELETE = 'DELETED';
    
    public $option_status,$option_variants_count;
    public $price_from;
    public $price_to;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wish_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'merchant_id', 'product_type'], 'required'],
            [['product_id', 'merchant_id'], 'integer'],
            [['walmart_attributes', 'error', 'short_description', 'self_description'], 'string'],
            [['min_price'], 'number'],
            [['product_type', 'category', 'tax_code'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'merchant_id' => 'Merchant ID',
            'walmart_attributes' => 'Walmart Attributes',
            'product_type' => 'Product Type',
            'category' => 'Category',
            'error' => 'Error',
            'tax_code' => 'Tax Code',
            'min_price' => 'Min Price',
            'short_description' => 'Short Description',
            'self_description' => 'Self Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMerchant()
    {
        return $this->hasOne(User::className(), ['id' => 'merchant_id']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getJet_product()
    {
        return $this->hasOne(JetProduct::className(), ['bigproduct_id' => 'product_id','merchant_id'=>'merchant_id']);
    }

    public function getWalmart_product_variants()
    {
        return $this->hasMany(WishProductVariants::className(), ['product_id' => 'product_id','merchant_id'=>'merchant_id']);
    }

    public function getWalmart_product_repricing()
    {
        return $this->hasMany(WishProductRepricing::className(), ['product_id' => 'product_id']);
    }
}
