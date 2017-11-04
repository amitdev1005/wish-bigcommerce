<?php
namespace frontend\modules\wishmarketplace\controllers;

use Yii;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\components\AttributeMap;
use frontend\modules\wishmarketplace\models\WishAttributeMap;

use frontend\modules\wishmarketplace\components\BigcommerceClientHelper;
use frontend\modules\wishmarketplace\components\Wishapi;

class WishAttributemapController extends WishmainController
{
    public function actionIndex()
    {
        $attributes = [];
        $bigcom_product_types = AttributeMap::getBigcomProductTypes();
        foreach ($bigcom_product_types as $type_arr) 
        {

            $product_type = $type_arr['product_type'];
            $WishCategoryId = $type_arr['category_name'];
            $category_path = $type_arr['category_path'];
            $parent = explode(',',$category_path);
            $wishAttributes = [];
            if(!is_null($WishCategoryId)) {
                $wishAttributes = AttributeMap::getWishCategoryAttributes($WishCategoryId,$parent)?:[];
            }
            $bigcomAttributes = AttributeMap::getBigcomProductAttributes($product_type);
            $mapped_values = AttributeMap::getAttributeMapValues($product_type);
            $attributes[$product_type] = [
                                            'product_type' => $product_type,
                                            'wish_attributes' => $wishAttributes,
                                            'bigcom_attributes' => $bigcomAttributes,
                                            'mapped_values' => $mapped_values,
                                            'wish_category_id' => $WishCategoryId
                                        ];
        }
        return $this->render('index',['attributes'=>$attributes]);
    }

    public function actionSave()
    {
        $data = Yii::$app->request->post();
        if($data && isset($data['walmart']))
        {
            $merchant_id = MERCHANT_ID;
            $insert_value = [];
            foreach($data['walmart'] as $key => $value)
            {
                $shopifyProductType = addslashes($key);
                foreach ($value as $walmart_attr => $value) {
                    $walmartAttrCode = $walmart_attr;
                    $attrValueType = '';
                    $attrValue = '';
                    if(is_array($value)) {
                        if(count($value) > 1) {
                            unset($value['text']);
                            $attrValueType = WishAttributeMap::VALUE_TYPE_BIGCOM;
                            $attrValue = implode(',', $value);
                        } elseif(count($value) == 1) {
                            if(isset($value['text'])) {
                                $attrValueType = WishAttributeMap::VALUE_TYPE_TEXT;
                                $attrValue = $value['text'];
                            } else {
                                $attrValueType = WishAttributeMap::VALUE_TYPE_BIGCOM;
                                $attrValue = reset($value);
                            }
                        }
                    }
                    elseif ($value != '') {
                        $attrValueType = WishAttributeMap::VALUE_TYPE_WISH;
                        $attrValue = $value;
                    }

                    if($attrValueType != '' && $attrValue != '')
                    {
                        $insert_value[] = "(".$merchant_id.",'".$shopifyProductType."','".addslashes($walmartAttrCode)."','".addslashes($attrValueType)."','".addslashes($attrValue)."')";
                    }
                }
            }
            if(count($insert_value)) {
                //remove attr map from session
                AttributeMap::unsetAttrMapSession(MERCHANT_ID);

                $delete = "DELETE FROM `wish_attribute_map` WHERE `merchant_id`=".$merchant_id;
                Data::sqlRecords($delete, null, 'delete');

                $query = "INSERT INTO `wish_attribute_map`(`merchant_id`, `shopify_product_type`, `walmart_attribute_code`, `attribute_value_type`, `attribute_value`) VALUES ".implode(',', $insert_value);
                Data::sqlRecords($query, null, 'insert');

                Yii::$app->session->setFlash('success', "Attributes Have been Mapped Successfully!!");
            }
        }
        return $this->redirect(['index']);
    }

    /*public function actionUpdateattribute()
    {
        $shop = Yii::$app->user->identity->username;
        $sc = new ShopifyClientHelper($shop, TOKEN, WALMART_APP_KEY, WALMART_APP_SECRET);
        $countProducts = $sc->call('GET', '/admin/products/count.json');
        $pages = ceil($countProducts/250);
        $simpleProducts = [];
        for($index=0; $index < $pages; $index++) {
            $products = $sc->call('GET', '/admin/products.json', array('published_status'=>'published','limit'=>250,'page'=>$index));
            foreach ($products as $product) 
            {
                if(count($product['variants']) == 1)
                {
                    $attr_ids = Data::getOptionValuesForSimpleProduct($product);
                    $simpleProducts[$product['id']] = $attr_ids;
                    $query = "UPDATE `jet_product` SET `attr_ids`= '".$attr_ids."' WHERE `id`=".$product['id'];
                    Data::sqlRecords($query, null, 'update');
                }
            }
        }
        print_r($simpleProducts);
    }*/
}
