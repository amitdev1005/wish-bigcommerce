<?php

namespace frontend\modules\wishmarketplace\models;

use Yii;

/**
 * This is the model class for table "walmart_registration".
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property string $name
 * @property string $legal_company_name
 * @property string $store_name
 * @property string $shipping_source
 * @property string $other_shipping_source
 * @property string $mobile
 * @property string $email
 * @property string $annual_revenue
 * @property string $reference
 * @property string $agreement
 * @property string $other_reference
 * @property string $website
 * @property string $amazon_seller_url
 * @property integer $product_count
 * @property string $company_address
 * @property string $country
 * @property string $have_valid_tax
 * @property string $selling_on_walmart
 * @property string $selling_on_walmart_source
 * @property string $other_selling_source
 * @property string $contact_to_walmart
 * @property string $approved_by_walmart
 * @property string $usa_warehouse
 * @property string $products_type_or_category
 */
class WishRegistration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wish_registration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['merchant_id', 'name', 'mobile', 'email', 'shipping_source', 'agreement'], 'required'],
            [['merchant_id'], 'integer'],

            [['name','email','shipping_source', 'other_shipping_source','other_selling_source',], 'string', 'max' => 255],
            //[['mobile'], 'string', 'max' => 15],
            [['agreement'], 'string', 'max' => 10],

            [['mobile'], 'number','message' => '"{value}" is invalid {attribute}. Only Numbers are allowed.'],
            [['merchant_id'], 'unique'],
            [['email'],'email','message'=>'Please enter a valid {attribute}.'],
            ['agreement', 'required', 'requiredValue' => 1, 'message' => 'You must agree to the terms and conditions.'],
            ['other_reference', 'required', 'message' => 'This field cannot be blank.', 'when' => function ($model) {
                return $model->reference == 'Other';
            }, 'whenClient' => "function (attribute, value) {
                    return $('#walmartregistration-reference').val() == 'Other';
            }"],
            
            ['contact_to_walmart', 'required', 'message' => 'This field cannot be blank.', 'when' => function ($model) {
                return $model->selling_on_walmart == 'no';
            }, 'whenClient' => "function (attribute, value) {
                    return $('#walmartregistration-selling_on_walmart').val() === 'no';
            }"],
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
            'name' => 'Name',
            'shipping_source' => 'Shipping Source',
            'other_shipping_source' => 'Other Shipping Source',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'agreement' => 'Agreement',
            'contact_to_walmart' => 'Contact To Wish',
        ];
    }
}
