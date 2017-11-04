<?php
namespace frontend\modules\wishmarketplace\components;

use Yii;
use yii\base\Component;
use yii\helpers\Url;
use frontend\modules\wishmarketplace\components\Data;

class Helper extends Component
{
	/**
     * Get Url
     * for example : $path = post/index
     *				 $url = www.baseurl.com/wishmarketplace/post/index
     * 
     * @param $path string
     * @return string
     */
	public static function getUrl($path)
	{
		$url = '';
		if($path!='')
		{
			$path = '/wishmarketplace/'.$path;
			$url = Url::to([$path]);
		}
		return $url;
	}


    public static function createLog($message, $path = 'newegg-common.log', $mode = 'a', $sendMail = false)
    {
        $file_path = Yii::getAlias('@webroot') . '/var/newegg/' . $path;
        $dir = dirname($file_path);
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }
        $file_path1 = Yii::getAlias('@webroot') . '/var/newegg/' . $path . '.log';
        $fileOrig = fopen($file_path1, $mode);
        fwrite($fileOrig, "\n" . date('d-m-Y H:i:s') . "\n" . $message);
        fclose($fileOrig);

        /* if ($sendMail) {
            self::sendEmail($file_path, $message);
        } */
    }

    public static function createExceptionLog($functionName,$msg,$shopName = 'common'){
        $dir = \Yii::getAlias('@webroot').'/var/exceptions/'.$functionName.'/'.$shopName.'/'.date('d-m-Y');
        if (!file_exists($dir)){
            mkdir($dir,0775, true);
        }
        $filenameOrig = $dir.'/'.time().'.txt';
        $handle = fopen($filenameOrig,'a');
        $msg = date('d-m-Y H:i:s')."\n".$msg;
        fwrite($handle,$msg);
        fclose($handle);
        Data::sendEmail($filenameOrig,$msg);
    }

    public static function configurationDetail($merchant_id){
       $query='select seller_id,authorization,secret_key,manufacturer from `newegg_configuration` where merchant_id="'.$merchant_id.'"';
        $product = Data::sqlRecords($query,"one","select");
        return $product;
    }

    public static function storeDetail($merchant_id){
       $query='select * from `newegg_shop_details` where merchant_id="'.$merchant_id.'"';
        $product = Data::sqlRecords($query,"one","select");
        return $product;
    }

    public static function getMerchantId($shop_url){
        $merchant_id = Data::sqlRecords('SELECT `merchant_id` FROM `newegg_shop_detail` WHERE `shop_url`="'.$shop_url.'" AND `purchase_status` != "'.Data::PURCHASE_STATUS_TRAILEXPIRE .'" AND `install_status`=1','one');
        return $merchant_id;
    }

    /*public static function deleteLog($file)
    {

        if (!unlink($file))
        {
            return false;
        }
        else
        {
            echo ("Deleted $file");
            return true;
        }
    }*/

    public static function categoryManufacturerDetail($shopify_product_type){
        $manufacturer = Data::sqlRecords('SELECT `manufacturer` FROM `newegg_category_map` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `product_type` = "'.$shopify_product_type .'"','one');
        return $manufacturer;
    }


    /*Get Product sku using product Id */
    public static function getProdcutSku($product_id){
        $product_sku = Data::sqlRecords('SELECT `sku` as `sku` FROM `jet_product` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `id` = "'.$product_id .'"','one');
        if(empty($product_sku)){
            $product_sku = Data::sqlRecords('SELECT `option_sku` as `sku` FROM `jet_product_variants` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `product_id` = "'.$product_id .'"','one');
        }
        return $product_sku['sku'];
    }

    /*Get Product id using product sku */
    public static function getProdcutId($product_sku){
        $product_id = Data::sqlRecords('SELECT `id` as `id` FROM `jet_product` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `sku` = "'.$product_sku .'"','one');
        if(empty($product_id)){
            $product_id = Data::sqlRecords('SELECT `product_id` as `id` FROM `jet_product_variants` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `option_sku` = "'.$product_sku .'"','one');
        }
        return $product_id['id'];
    }

      /*Get Product status using product id */
    public static function getProdcutStatus($product_id){
        $product_status = Data::sqlRecords('SELECT `upload_status` as `status` FROM `newegg_product` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `product_id` = "'.$product_id .'"','one');
        return $product_status['status'];
    }

         /*variants attribute Data  using product id */
    public static function getVaraintsAttribute($product_id){
        $variant_option = Data::sqlRecords('SELECT `variant_option1` FROM `jet_product_variants` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `option_id` = "'.$product_id .'"','one');
        if(empty($variant_option['variant_option1'])){
            $variant_option = Data::sqlRecords('SELECT `variant_option2` FROM `jet_product_variants` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `option_id` = "'.$product_id .'"','one');
            if(empty($variant_option['variant_option2'])){
                $variant_option = Data::sqlRecords('SELECT `variant_option3` FROM `jet_product_variants` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `option_id` = "'.$product_id .'"','one');
               return $variant_option['variant_option3']; 
            }
            return $variant_option['variant_option2'];
        }
        return $variant_option['variant_option1'];
    }
}
