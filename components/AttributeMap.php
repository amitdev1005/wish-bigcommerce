<?php 
namespace frontend\modules\wishmarketplace\components;

use Yii;
use yii\base\Component;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\models\WishAttributeMap;

class AttributeMap extends Component
{
	const ATTRIBUTE_PATH_SEPERATOR = '->';
    /**
     * Get Shopify Product Types
     *
     * @param int|null $merchant_id
     * @return array
     */
    public static function getBigcomProductTypes($merchant_id = null)
    {
        if (is_null($merchant_id))
            $merchant_id = MERCHANT_ID;

        $query = "SELECT `product_type`,`category_name`,`category_id`,`category_path` FROM `wish_category_map` WHERE `category_id`!='' AND `merchant_id`=" . $merchant_id;
        $records = Data::sqlRecords($query, 'all');
        if ($records)
            return $records;
        else
            return [];
    }

    /*Coded by Visahl Kumar*/
    public static function getCategoryId($category_name)
    {
        $maincategoryroute = Yii::getAlias('@webroot').'/WISHTAXONOMY/Wish_Taxonomy.csv'; //Contains Parent Category, Main Category and SubCategory
        $str = file_get_contents($maincategoryroute);
        $maincategory = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $str));
        $category=array();
        $category_tree=array();
        $category_tree_data=array();
        $rootcategory = array();
        $head_data=array();
        $category=$maincategory;
        $length=count($category);
        for($i=1;$i<=$length;$i=$i+1)
        {
            if($i==0 && $i<=$length)
            {
                $head_data=$category[$i];
            }
            else
            {   if($i<=$length)
                {
                    if(isset($category[$i]))
                    {
                        $category_tree_middle[]=$category[$i];
                    }
                }
                
            }
        }
        $category_tree_data=$category_tree_middle;
        $category_id=array();
        foreach($category_tree_data as $key=>$value)
        {
            if(isset($value['5']))
            {
                if($category_name == $value['5'])
                {
                    $category_id =$value['4'];
                }
            }
        }
        return $category_id;  
    } 
    public static function getWishCategoryAttributes($category_name, $parent_id)
    {
        $minicategory_id = "";
        $minicategory_id = self::getCategoryId($category_name);
        $required = [];
        $attribute_code = [];
        $attribute_code_id = [];
        $attribute_options = [];
        $variant_attributes=[];
        //get all attributes
        $attributeroute = Yii::getAlias('@webroot').'/WISHTAXONOMY/Wish_Taxonomy_mapping.csv'; //Contains Attributes
        $str = file_get_contents($attributeroute);
        $mainattribute = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $str));
        $validate_attr = array();
        foreach($mainattribute as $key=>$value)
        {
            if($key=='0')
            {
                continue;
            }
            else
            {
                if(isset($value['1']))
                {
                    if($minicategory_id==$value['0'])
                    {
                        if(in_array($value['3'], $validate_attr))
                        {
                            continue;
                        }
                        else
                        {
                            $attribute_code_id[] = $value['1']; //attribute id(s)
                            $attribute_code[] = $value['3']; //display attributes
                            $validate_attr[] = $value['3'];  //prevent repeat
                        }
                    }
                }
            }
        }

        //fetch Attribute Values
        $attributevalueroute = Yii::getAlias('@webroot').'/WISHTAXONOMY/Wish_Taxonomy_attribute_value.csv';
        $str = file_get_contents($attributevalueroute);
        $attributevalue = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $str));
        foreach($attributevalue as $key=>$value)
        {
            if($key=='0')
            {
                continue;
            }
            else
            {
                foreach($attribute_code_id as $k=>$v)
                {
                    if($v==$value['0'])
                    {
                        $attribute_options[] =  $value['1'];  //Get all attribute_values 
                    }
                }
            }
        }
        $data = ['attributes' => $attribute_code, 'attribute_values' => $attribute_options, 'parent_id' => $parent_id];
        return $data;
    }
    /*end by Vishal Kumar*/
    
    /**
     * Add Attribute Values For Unit Type Attributes
     *
     * @param &$attributeValues
     */
     private function addUnitAttributeValues(&$attributeValues)
    {
        $unitAttrValues = [
                    'chainLength->unit'=>'Inches,Micrometers,Feet,Millimeters,Centimeters,Meters,Yards,French,Miles,Mil',
                    'pantSize->waistSize->unit'=>'Inches,Micrometers,Feet,Millimeters,Centimeters,Meters,Yards,French,Miles,Mil',
                    'waistSize->unit'=>'Inches,Micrometers,Feet,Millimeters,Centimeters,Meters,Yards,French,Miles,Mil',
                    'screenSize->unit'=>'Inches,Micrometers,Feet,Millimeters,Centimeters,Meters,Yards,French,Miles,Mil',
                    'ramMemory->unit'=>'Terabytes,Kibibytes,Mebibytes,Gibibytes,Kilobytes,Gigabytes,Tebibytes,Megabyte',
                    'hardDriveCapacity->unit'=>'Terabytes,Kibibytes,Mebibytes,Gibibytes,Kilobytes,Gigabytes,Tebibytes,Megabyte',
                    'cableLength->unit'=>'Inches,Micrometers,Feet,Millimeters,Centimeters,Meters,Yards,French,Miles,Mil',
                    'heelHeight->unit'=>'Inches,Micrometers,Feet,Millimeters,Centimeters,Meters,Yards,French,Miles,Mil',
                    'carats->unit'=>'Carat',
                    'focalLength->unit'=>'Inches,Micrometers,Feet,Millimeters,Centimeters,Meters,Yards,French,Miles,Mil',
                    'displayResolution->unit'=>'Dots Per Square Inch,Pixels Per Inch,Volumetric Pixels,Megapixels,Resolution Element,Surface Element,Dots Per Inch,Texels',
                    'volts->unit'=>'Volts',
                    'amps->unit'=>'Amps',
                    'gallonsPerMinute->unit'=>'',
                    'minimumWeight->unit'=>'Kilograms Per Meter,Kilograms,Milligrams,Ounces,Pounds,Grams,Carat',
                    'maximumWeight->unit'=>'Kilograms Per Meter,Kilograms,Milligrams,Ounces,Pounds,Grams,Carat'
                    ];

        $attributeValues = array_merge($attributeValues,$unitAttrValues);
    }
    
    public static function getCatAttributeSessionIdx($category_id)
    {
    	$index = 'walmart_cat_attributes_for_'.addslashes($category_id);
    	return $index;
    }
    /**
     * Get Shopify Product Attributes
     *
     * @param string $product_type
     * @param int|null $merchant_id
     * @return array
     */
    public static function getBigcomProductAttributes($product_type, $merchant_id=null)
    {
        if(is_null($merchant_id))
            $merchant_id = MERCHANT_ID;

        $query = 'SELECT `bigcom_attr` FROM `jet_product` WHERE `product_type`="'.$product_type.'" AND `merchant_id`='.$merchant_id;
        
        $records = Data::sqlRecords($query, 'all');
        
        
        $bigcommerce_attributes = [];
        if($records)
        {
            foreach ($records as $value) {
                $attr_ids = json_decode($value['bigcom_attr']);
                if($attr_ids){
                    foreach ($attr_ids as $option_id => $attr_id) {
                        if(!in_array($attr_id, $bigcommerce_attributes))
                            $bigcommerce_attributes[] = $attr_id;
                    }

                }
            }
        }
        return $bigcommerce_attributes;
    }

    /**
     * get Saved Values of Attribute Mapping
     *
     * @param string $product_type
     * @param int|null $merchant_id
     * @return array
     */
    public static function getAttributeMapValues($product_type, $merchant_id=null)
    {
        if(is_null($merchant_id))
            $merchant_id = MERCHANT_ID;

        $query = 'SELECT `walmart_attribute_code`,`attribute_value_type`,`attribute_value` FROM `wish_attribute_map` WHERE `shopify_product_type`="'.$product_type.'" AND `merchant_id`='.$merchant_id;
        
        $records = Data::sqlRecords($query, 'all');


        $mapped_values = [];
        if($records)
        {
            foreach ($records as $value) {
                if($value['attribute_value'] != '') {
                    $mapped_values[$value['walmart_attribute_code']] = ['type'=>$value['attribute_value_type'], 'value'=>$value['attribute_value']];
                }
            }
        }
        return $mapped_values;
    }

    /**
     * Get all Mappings for Shopify Attributes
     *
     * @param string $product_type
     * @param int|null $merchant_id
     * @return array
     */
    public static function getMappedWalmartAttributes($shopify_product_type, $option_id, $merchant_id=null)
    {
        $mapped_attributes = [];
        $attribute_values = [];
        $common_attributes = [];

        $attributeMapValues = self::getAttributeMapValues($shopify_product_type);
        //print_r($attributeMapValues);die;
        foreach ($attributeMapValues as $walAttrCode => $walAttrValue) {
            if($walAttrValue['type'] == WishAttributeMap::VALUE_TYPE_BIGCOM) {
                $mapped_attributes[$walAttrCode] = $walAttrValue['value'];
            }
            elseif($walAttrValue['type'] == WishAttributeMap::VALUE_TYPE_TEXT || 
                $walAttrValue['type'] == WishAttributeMap::VALUE_TYPE_WISH) {
                $common_attributes[$walAttrCode] = $walAttrValue['value'];
            }
        }

        $shopify_attribute_map = [];
        $productOptionValues = self::getOptionValuesForProduct($option_id,$merchant_id);
        foreach ($productOptionValues as $key => $value) {
            foreach ($mapped_attributes as $wal_attr => $map_value) {
                if(in_array($key, explode(',', $map_value))) {
                    $shopify_attribute_map[$key] = [$wal_attr];
                    $attribute_values[$wal_attr] = $value;
                }
            }
        }

        if(count($shopify_attribute_map))
            $shopify_attribute_map = json_encode($shopify_attribute_map);
        else
            $shopify_attribute_map = '';

        if(count($attribute_values))
            $attribute_values = json_encode($attribute_values);
        else
            $attribute_values = '';

        if(count($common_attributes))
            $common_attributes = json_encode($common_attributes);
        else
            $common_attributes = '';

        return [
                    'mapped_attributes' => $shopify_attribute_map, 
                    'attribute_values' => $attribute_values, 
                    'common_attributes' => $common_attributes
                ];
    }

    /**
     * Get Shopify Option Values for Product
     *
     * @param int $product_option_id
     * @return array
     */
    public static function getOptionValuesForProduct($product_option_id,$merchant_id)
    {


        $query = 'SELECT `product_id` FROM `jet_product_variants` WHERE `option_id`="'.$product_option_id.'" ';
        
        $records = Data::sqlRecords($query, 'one');
        
        // $query = "SELECT `jp`.`additional_info`,`jpv`.`variant_option1`,`jpv`.`variant_option2`,`jpv`.`variant_option3` FROM `jet_product_variants` `jpv` INNER JOIN `jet_product` `jp` ON `jp`.`bigproduct_id`=`jpv`.`product_id` WHERE  `jpv`.`option_id`=".$product_option_id."  LIMIT 0,1";

        // $records = Data::sqlRecords($query, 'one');

        $query = 'SELECT `bigcom_attr` FROM `jet_product` WHERE `bigproduct_id`="'. $records['product_id'].'" and `merchant_id`="'.$merchant_id.'"';
        
        $records = Data::sqlRecords($query, 'one');


        $attributes=$records['bigcom_attr'];

        $values = [];
        if($records)
        {
            $bigcommerce_attributes = $attributes;
            
            if($bigcommerce_attributes){
	            if(is_array($bigcommerce_attributes))
	                $bigcommerce_attributes = array_values($bigcommerce_attributes);
				//print_r($shopify_attributes);die;
	            foreach ($bigcommerce_attributes as $key=>$attr) {
	                    $values[$attr] = $records['variant_option'.($key+1)];
	            }
            }
        }
        //print_r($values);die;
        return $values;
    }

    /**
     * To check if attributes are mapped for the given shopify product type
     *
     * @param int $product_option_id
     * @return array
     */
    public static function isProductTypeAttributeMapped($product_type)
    {
        $query = 'SELECT `id` FROM `walmart_attribute_map` WHERE `shopify_product_type`="'.$product_type.'" LIMIT 0,1';

        $records = Data::sqlRecords($query, 'one');

        if($records)
            return true;
        else
            return false;
    }

    public static function unsetAttrMapSession()
    {
        $session = Yii::$app->session;
        $main_index = self::getAttrMapSessionMainIdx(MERCHANT_ID);
        $session->remove($main_index);
        $session->close();
    }

    public static function getAttrMapSessionMainIdx($merchant_id)
    {
        $index = 'walmart_attribute_map_'.$merchant_id;
        return $index;
    }

}
