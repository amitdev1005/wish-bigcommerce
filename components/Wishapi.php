<?php
namespace frontend\modules\wishmarketplace\components;

use Yii;
use yii\base\Component;
use frontend\modules\wishmarketplace\models\WishProduct;
use frontend\modules\wishmarketplace\components\Signature;
use frontend\modules\wishmarketplace\components\Generator;
use frontend\modules\wishmarketplace\components\Xml\Parser;
use frontend\modules\wishmarketplace\components\Jetproductinfo;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\models\WishAttributeMap;
use yii\base\Response;
use frontend\modules\wishmarketplace\components\AttributeMap;
use frontend\modules\wishmarketplace\components\WishCategory;
use frontend\modules\wishmarketplace\components\WishProductValidate;
use frontend\modules\wishmarketplace\components\WishRepricing;
use frontend\modules\wishmarketplace\components\WishPromoStatus;
use frontend\modules\wishmarketplace\models\WishExtensionDetail;
use frontend\modules\wishmarketplace\components\WishProduct as WishProductComponent;

class Wishapi extends Component
{

  const GET_FEEDS_ITEMS_ADD_SUB_URL = "v2/product/add";
  const GET_ITEMS_UPDATE_SUB_URL = "v2/variant/update";
  const ITEMS_DELETE_SUB_URL = "v2/product/disable";
  const GET_ITEMS_SUB_URL = "v2/product";
  const GET_ORDERS_SUB_URL = "v2/order/get-fulfill";
  const VARIANT_ADD_URL = "v2/variant/add";
  protected $client_id;
  protected $client_secret_key;
  protected $code;
  protected $access_token;  


  public function __construct($client_id="",$client_secret_key="",$code="",$access_token=""){

    $this->client_id =$client_id;
    $this->client_secret_key = $client_secret_key;
    $this->code = $code;
    $this->access_token=$access_token;
}

public function CPostRequest($method,$postFields){
    $url = 'https://merchant.wish.com/api/'.$method;
    $curl = curl_init();
    $options = array(
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      );

    $postFields['access_token'] =$this->access_token;

    $options[CURLOPT_POSTFIELDS] = $postFields;
    $options[CURLOPT_URL] = $url;

    curl_setopt ($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

    curl_setopt_array($curl, $options);

    $result = curl_exec($curl);

    $error = curl_errno($curl);

    $error_message = curl_error($curl);

    if($error){
      return array('success'=>false ,'message'=>$error_message);
  }
  $httpStatus = curl_getinfo($curl,CURLINFO_HTTP_CODE);
  $headerSize = curl_getinfo($curl,CURLINFO_HEADER_SIZE);

  curl_close($curl);
  $response = json_decode($result,true);

  if (is_array($response)) {
      if($response['code']==0){
        $data = $response['data']; 
        if(isset($data['Product']) && $data['Product'])
          return array('success'=>true ,'id' =>$data['Product']['id']);
      else if(isset($data['Variant']) && $data['Variant'])
          return array('success'=>true ,'id' => $data['Variant']['id']);
      else if(isset($data['Order']) && $data['Order'])
          return array('success'=>true ,'order' => $data);
      else if(isset($data['job_id']) && $data['job_id'])
          return array('success'=>true ,'job_id' => $data);
      else if(isset($data['status']) && $data['status'])
          return array('success'=>true ,'status' => $data);
      else  
          return array('success'=>true ,'message' => 'Successfully');  
  }
  if($response['code']){
    return array('success'=>false ,'message'=>$response['message']);
}
} else{
  return array('success'=>false ,'message'=> $result);
}
}


public function CPutRequest($method,$postFields){
//print_r($postFields);
    $url ='https://merchant.wish.com/api/v2/variant/update';   
    $curl = curl_init();
    $options = array(
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      );
    $postFields['access_token'] =$this->access_token;

    $options[CURLOPT_POSTFIELDS] = $postFields;
    $options[CURLOPT_URL] = $url;
    curl_setopt ($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

    curl_setopt_array($curl, $options);

    $result = curl_exec($curl);

    $error = curl_errno($curl);

    $error_message = curl_error($curl);

    if($error){
      return array('success'=>false ,'message'=>$error_message);
  }
  $httpStatus = curl_getinfo($curl,CURLINFO_HTTP_CODE);
  $headerSize = curl_getinfo($curl,CURLINFO_HEADER_SIZE);

  curl_close($curl);
  $response = json_decode($result,true);
  if (is_array($response)) {
      if($response['code']==0){
        $data = $response['data']; 
        if(isset($data['Product']) && $data['Product'])
          return array('success'=>true ,'id' =>$data['Product']['id']);
      else if(isset($data['Variant']) && $data['Variant'])
          return array('success'=>true ,'id' => $data['Variant']['id']);
      else if(isset($data['Order']) && $data['Order'])
          return array('success'=>true ,'order' => $data);
      else if(isset($data['job_id']) && $data['job_id'])
          return array('success'=>true ,'job_id' => $data);
      else if(isset($data['status']) && $data['status'])
          return array('success'=>true ,'status' => $data);
      else  
          return array('success'=>true ,'message' => 'Successfully');  
  }
  if($response['code']){
    return array('success'=>false ,'message'=>$response['message']);
}
} else{
  return array('success'=>false ,'message'=> $result);
}
}
public function CGetRequest($method,$postFields = NULL){
    $url ='https://merchant.wish.com/api/'.$method;

    $curl = curl_init();
    $options = array(
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      );
    $postFields['access_token'] =$this->access_token;
    $options[CURLOPT_POSTFIELDS] = $postFields;
    $options[CURLOPT_URL] = $url;
    curl_setopt ($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

    curl_setopt_array($curl, $options);

    $result = curl_exec($curl);

    $error = curl_errno($curl);

    $error_message = curl_error($curl);

    if($error){
      return array('success'=>false ,'message'=>$error_message);
  }
  $httpStatus = curl_getinfo($curl,CURLINFO_HTTP_CODE);
  $headerSize = curl_getinfo($curl,CURLINFO_HEADER_SIZE);
  curl_close($curl);
  $response = json_decode($result,true);
  if (is_array($response)) {
      if($response['code']==0){
        $data = $response['data'];
        if(isset($data['Product']) && $data['Product'])
          return array('success'=>true ,'status_on_wish' =>$data['Product']['review_status']);
      else if(isset($data['Variant']) && $data['Variant'])
          return array('success'=>true ,'status_on_wish' => $data['Variant']['review_status']);
      else if(isset($data['0']['Order']) && $data['0']['Order'])
          return array('success'=>true ,'order' => $data);
      else if(isset($data['job_id']) && $data['job_id'])
          return array('success'=>true ,'job_id' => $data);
      else if(isset($data['status']) && $data['status'])
          return array('success'=>true ,'status' => $data);
      else  
          return array('success'=>true ,'message' => 'Successfully');  
  }
  if($response['code']){
    return array('success'=>false ,'message'=>$response['message']);
}
} else{
  return array('success'=>false ,'message'=> $result);
}
}



    /**
     * Post Request on https://marketplace.walmartapis.com/
     * @param string $url
     * @param string|[] $params
     * @return string
     */

    public function getRequest($url, $params = [])
    {
      $url ='https://merchant.wish.com/api/v2/product/add';  
      $curl = curl_init();
      $options = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        );
      $postFields['access_token'] =$this->access_token;

      $options[CURLOPT_POSTFIELDS] = $postFields;
      $options[CURLOPT_URL] = $url;


      curl_setopt ($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

      curl_setopt_array($curl, $options);

      $result = curl_exec($curl);

      $error = curl_errno($curl);

      $error_message = curl_error($curl);

      if($error){
        return array('success'=>false ,'message'=>$error_message);
    }
    $httpStatus = curl_getinfo($curl,CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($curl,CURLINFO_HEADER_SIZE);

    curl_close($curl);
    $response = json_decode($result,true);

    if (is_array($response)) {
        if($response['code']==0){
          $data = $response['data']; 
          if(isset($data['Product']) && $data['Product'])
            return array('success'=>true ,'id' =>$data['Product']['id']);
        else if(isset($data['Variant']) && $data['Variant'])
            return array('success'=>true ,'id' => $data['Variant']['id']);
        else if(isset($data['Order']) && $data['Order'])
            return array('success'=>true ,'order' => $data);
        else if(isset($data['job_id']) && $data['job_id'])
            return array('success'=>true ,'job_id' => $data);
        else if(isset($data['status']) && $data['status'])
            return array('success'=>true ,'status' => $data);
        else  
            return array('success'=>true ,'message' => 'Successfully');  
    }
    if($response['code']){
      return array('success'=>false ,'message'=>$response['message']);
  }
} else{
    return array('success'=>false ,'message'=> $result);
}
        /*$signature = $this->apiSignature->getSignature($url, 'GET', $this->apiConsumerId, $this->apiPrivateKey);
        $url = $this->apiUrl . $url;

        $headers = [];
        $headers[] = "WM_SVC.NAME: Wish Marketplace";
        $headers[] = "WM_QOS.CORRELATION_ID: " . base64_encode(\phpseclib\Crypt\Random::string(16));
        $headers[] = "WM_SEC.TIMESTAMP: " . $this->apiSignature->timestamp;
        $headers[] = "WM_SEC.AUTH_SIGNATURE: " . $signature;
        $headers[] = "WM_CONSUMER.ID: " . $this->apiConsumerId;
        $headers[] = "WM_CONSUMER.CHANNEL.TYPE: 7b2c8dab-c79c-4cee-97fb-0ac399e17ade";
        $headers[] = "Content-Type: application/json";
        $headers[] = "Accept: application/xml";
        if (isset($params['headers']) && !empty($params['headers'])) {
            $headers[] = $params['headers'];
        }else{
            $headers[] = "WM_CONSUMER.CHANNEL.TYPE: " . $this->apiConsumerChannelId;
        }
        $headers[] = "HOST: marketplace.wishapis.com";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($server_output, 0, $header_size);
        $response = substr($server_output, $header_size);
        curl_close($ch);*/

        return $response;
    }

    public function putRequest($url, $params)
    {
        $signature = $this->apiSignature->getSignature($url, 'PUT', $this->apiConsumerId, $this->apiPrivateKey);
        $url = $this->apiUrl . $url;
        $body = '';
        if (isset($params['file'])) {
          $body['file'] = new \CurlFile($params['file'], 'application/xml');
      } elseif (isset($params['data'])) {
          $body = $params['data'];
      }

      $headers = [];
      $headers[] = "WM_SVC.NAME: Wish Marketplace";
      $headers[] = "WM_QOS.CORRELATION_ID: " . base64_encode(\phpseclib\Crypt\Random::string(16));
      $headers[] = "WM_SEC.TIMESTAMP: " . $this->apiSignature->timestamp;
      $headers[] = "WM_SEC.AUTH_SIGNATURE: " . $signature;
      $headers[] = "WM_CONSUMER.ID: " . $this->apiConsumerId;
      $headers[] = "WM_CONSUMER.CHANNEL.TYPE: 7b2c8dab-c79c-4cee-97fb-0ac399e17ade";
      if (isset($params['file']) && !empty($params['file'])) {
          $headers[] = "Content-Type: multipart/form-data";
      } elseif (isset($params['data']) && !empty($params['data'])) {
          $headers[] = "Content-Type: application/xml";
      } else {
          $headers[] = "Content-Type: application/json";
      }
      $headers[] = "Accept: application/xml";
        /*if (isset($params['headers']) && !empty($params['headers'])) {
            $headers[] = $params['headers'];
        }else{
            $headers[] = "WM_CONSUMER.CHANNEL.TYPE: " . $this->apiConsumerChannelId;
        }*/
        $headers[] = "HOST: marketplace.wishapis.com";

        //$url = 'https://192.168.0.58/fetchPutRequest.php';
        $ch = curl_init($url);
        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, NULL);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        /*$information = curl_getinfo($ch);//
        Data::createLog(print_r($information,true),'headers.log','a');//
        Data::createLog(PHP_EOL,'headers.log','a');//
        Data::createLog($server_output,'headers.log','a');//*/

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($server_output, 0, $header_size);
        $response = substr($server_output, $header_size);
        curl_close($ch);

        return $response;
    }

    //delete the element
    public function deleteRequest($url, $params = [])
    {
        $signature = $this->apiSignature->getSignature($url, 'DELETE', $this->apiConsumerId, $this->apiPrivateKey);
        $url = $this->apiUrl . $url;

        $headers = [];
        $headers[] = "WM_SVC.NAME: Wish Marketplace";
        $headers[] = "WM_QOS.CORRELATION_ID: " . base64_encode(\phpseclib\Crypt\Random::string(16));
        $headers[] = "WM_SEC.TIMESTAMP: " . $this->apiSignature->timestamp;
        $headers[] = "WM_SEC.AUTH_SIGNATURE: " . $signature;
        $headers[] = "WM_CONSUMER.ID: " . $this->apiConsumerId;
        //$headers[] = "Content-Type: application/json";
        $headers[] = "Accept: application/xml";
        $headers[] = "WM_CONSUMER.CHANNEL.TYPE: 7b2c8dab-c79c-4cee-97fb-0ac399e17ade";

        /*if (isset($params['headers']) && !empty($params['headers'])) {
            $headers[] = $params['headers'];
        }*/
        $headers[] = "HOST: marketplace.wishapis.com";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($server_output, 0, $header_size);
        $response = substr($server_output, $header_size);
        curl_close($ch);

        return $response;
    }

    /**
     * Get a Order
     * @param string $purchaseOrderId
     * @param string $subUrl
     * @return array|string
     */
    public function getOrder($purchaseOrderId, $subUrl = self::GET_ORDERS_SUB_URL)
    {
        //$response = $this->getRequest($subUrl . '?purchaseOrderId=' . $purchaseOrderId);

      $response = $this->getRequest($subUrl . '/' . $purchaseOrderId);
      // print_r($response);die();
      return $response;


  }

    /**
     * Get Orders
     * @param string|[] $params - date in yy-mm-dd
     * @param string $subUrl
     * @return string
     * @link  https://developer.walmartapis.com/#get-all-orders
     */
    public function getOrderbyid($purchaseOrderId, $subUrl = self::GET_ORDERS_SUB_URL){
        
      $response = $this->getRequest($subUrl . '/' . $purchaseOrderId);
      // print_r($response);die('jdfhbf');
      return $response;

    }
    public function getOrders()
    {
      $productUpload = stream_context_create(array(
          'http' => array(
            'method' => 'GET',
            'ignore_errors' => true,
            ),
          ));
/*        $product = [
        'id' => $order_id,
        ];*/
        // $productUpload=$product;
   /*     if($productUpload){*/
          $response=$this->CGetRequest(self::GET_ORDERS_SUB_URL);
          if($response['success']==1){
            return ['success'=>$response];
          }
          else {
              $responseError[] = $response['message'];
              return ['errors' => $responseError];
          }

    // }
      return $responseArray;
    }

public function replaceNs($response)
{
  $response = str_replace('ns1:', '', $response);
  $response = str_replace('ns2:', '', $response);
  $response = str_replace('ns3:', '', $response);
  $response = str_replace('ns4:', '', $response);
  $response = str_replace('ns5:', '', $response);
  return $response;
}

    /**
     * Get Reports
     * @param string|[] $params
     * @param string $subUrl
     * @return string compressed csv file
     * @link https://developer.walmartapis.com/#get-report
     */
    public function getReports($params = [], $subUrl = self::GET_REPORTS_SUB_URL)
    {
      if (!isset($params['type']) || empty($params['type'])) {
        $params['type'] = 'item';
    }
    $queryString = empty($params) ? '' : '?' . http_build_query($params);
    $response = $this->getRequest($subUrl . $queryString);
        //csv file in response
    return $response;
}

    /**
     * Get Item
     * @param string $sku
     * @param string $subUrl
     * @return []
     * @link https://developer.walmartapis.com/#get-an-item
     */


    /**
     * Get Items
     * @param string|[] $params
     * @param string $subUrl
     * @return string
     * @link https://developer.walmartapis.com/#get-all-items
     */

    /**
     * Get Inventory
     * @param string $sku
     * @param string $subUrl
     * @return string
     * @link https://developer.walmartapis.com/#get-inventory-for-an-item
     */
    public function getInventory($sku, $subUrl = self::GET_INVENTORY_SUB_URL)
    {
      $response = $this->getRequest($subUrl . '?sku=' . $sku);
      return json_decode($response, true);
  }

    /**
     * Get Feeds
     * @param null $feedId
     * @param string $subUrl
     * @return string
     * @link https://developer.walmartapis.com/#feeds
     */
    public function getFeeds5646($feedId = null, $subUrl = self::GET_FEEDS_SUB_URL)
    {
      $response = "";
      if ($feedId != null) {
        $response = json_decode($this->getRequest($subUrl . '?feedId=' . $feedId), true);

        return $response;
    }
    return $response;
}
//By srijan
    public function getFeeds($feedId = null, $subUrl = self::GET_FEEDS_ITEMS_ADD_SUB_URL)
    {
      $response = "";
      if ($feedId != null) {
        $response=$this->CPostRequest($subUrl,$productUpload);

        return $response;
    }
    return $response;
}

    /*Coded by Vishal Kumar*/
  public function retireProduct($sku = null)
  {
    $response = "";
    $productDelete = stream_context_create(array(
        'http' => array(
          'method' => 'POST',
          'ignore_errors' => true,
          ),
        ));
      $productdata = [
      'parent_sku' => $sku,
      ];
      $productDelete=$productdata;
      $response = $this->CPostRequest(self::ITEMS_DELETE_SUB_URL,$productDelete);
      if($response['success']==1){
          $response['message'] = "Product Disabled on wish";
          $responseArray['success'] = $response['message'];
          return $responseArray;
        }
        else {
            $responseError['error'] = $response['message'];
            return $responseError;
        }
  }

    //by shivam

public function getItemstatus($sku, $returnField = null, $subUrl = self::GET_ITEMS_SUB_URL)
{
  $response = $this->getRequest($subUrl . '/' . $sku);
  $response = json_decode($response, true);
  if ($returnField && !isset($response['error'])) {
    return $response['MPItemView'][0]['publishedStatus'];
}
return $response;
}

    // end by shivam

public function viewFeed($feedId = null, $limit = 50, $subUrl = self::GET_FEEDS_SUB_URL)
{
  $response = "";
  if ($feedId != null) {
    $response = json_decode($this->getRequest($subUrl . '/' . $feedId . '?includeDetails=' . json_encode(true) . '&limit=' . $limit), true);

}
return $response;
}

    /**
     * To Convert Escaped Characters in XML to HTML chars
     * @param string $path
     * @return bool
     */
    public static function unEscapeData($path)
    {
      if (file_exists($path)) {
        $handle = fopen($path, "r");
        $contents = fread($handle, filesize($path));
        $data = htmlspecialchars_decode($contents);
        fclose($handle);
        $fileOrig = fopen($path, 'w');
        fwrite($fileOrig, $data);
        fclose($fileOrig);
    }
    return false;
}

    /**
     * Create Product on Walmart
     * @param string|[] $ids
     * @return bool
     */
    //By Srijan
   public function createProductOnWish($ids, $merchant_id, $returnPreparedData = false)
    {
      $timeStamp = (string)time();

      $productUpload = stream_context_create(array(
        'http' => array(
          'method' => 'POST',
          'ignore_errors' => true,
          ),
        ));
      
      if (count($ids) > 0) 
      {
        $key = 1;
        $uploadErrors = [];
        $uploadProductIds = [];

        foreach($ids as $id) 
        {
          $query = 'SELECT `product_id`, `variant_id`, `title`, `sku`, `type`, `wal`.`product_type`, `wal`.`status`, `description`, `image`,`additional_images`, `qty`, `price`, `weight`,`bigcom_attr`, `brand`, `upc`, `walmart_attributes`, `category`, `wal`.`parent_category`, `long_description`, `short_description`, `self_description`, `common_attributes`, `sku_override`, `product_id_override`, `wal`.`shipping_exceptions` FROM (SELECT * FROM `wish_product` WHERE `merchant_id`="'.$merchant_id.'") as wal INNER JOIN (SELECT * FROM `jet_product` WHERE `merchant_id`="'.$merchant_id.'") as `jet` ON jet.bigproduct_id=wal.product_id WHERE wal.product_id="'.$id.'" LIMIT 1';

          $productArray = Data::sqlRecords($query, "one", "select");
          if ($productArray) 
          {
            $validateResponse = self::validateProduct($productArray);

                if (is_array($validateResponse) && isset($validateResponse['error'])) {
                  $uploadErrors[$productArray['sku']] = $validateResponse['error'];

                  continue;
              } 

              elseif($validateResponse === true)
              {


                $variantGroupId = (string)$id.(string)time();

                $description = empty($productArray['long_description']) ? $productArray['description'] : $productArray['long_description'];

                $originalmessage = '';

                  //remove <![CDATA[ ]]> from description
                $description = str_replace('<![CDATA[', '', $description);
                $description = str_replace(']]>', '', $description);

                  //trim product description more than 4000 characters
                if (strlen($description) > 3500) {
                  $description = Data::trimString($description, 3500);
                }

                $short_description = Data::trimString($description, 800);
                $brand = addslashes($productArray['brand']);
                $title = trim(addslashes($productArray['title']));
                $maxLength = 14;
                $tags = substr($productArray['title'], 0, $maxLength);
                if (isset($title) && !empty($title)) {
                  $productArray['title'] = $title;
                }

                if ($productArray['type'] == "simple") 
                {
                  $product = [              
                  'id'=>$productArray['product_id'],
                  'name' => $title,             
                  'sku'=>$productArray['sku'],
                  'inventory'=>$productArray['qty'],
                  'price' => (string)$productArray['price'],
                  'product_id' => $productArray['product_id'],
                  'variant_id' => $productArray['variant_id'],
                  'description' => $description,
                  'upc' => $productArray['upc'],
                  'tags'=>$tags,
                  'weight' => (string)$productArray['weight'],
                  'category' => $productArray['category'],
                  'shipping' => '0.0',
                  'brand' => $brand,
                  'extra_images'=>$productArray['additional_images'],
                  'main_image' => $productArray['image'],
                  ];
                }
                else
                {
                  $duplicateSkus = [];
                  $query = 'SELECT jet.option_id,option_title,option_sku,wal.walmart_option_attributes,option_image,option_qty,option_price ,option_weight,option_unique_id, `jet`.`variant_option1`, `jet`.`variant_option2`, `jet`.`variant_option3`, `wal`.`status` FROM (SELECT * FROM `wish_product_variants` WHERE `merchant_id`="' . $merchant_id . '") as wal INNER JOIN (SELECT * FROM `jet_product_variants` WHERE `merchant_id`="' . $merchant_id . '") as jet ON jet.option_id=wal.option_id WHERE wal.product_id="' . $id . '"';
                  $productVarArray = Data::sqlRecords($query, "all", "select");
                  foreach ($productVarArray as $value) 
                  {
                    /*$value['option_price'] = WishRepricing::getProductPrice($value['option_price'], $productArray['type'], $value['option_id'], $merchant_id);
    */
                    if (in_array($value['option_sku'], $duplicateSkus)) {
                      $uploadErrors[$productArray['sku']][$value['option_sku']] = "Variant Sku : '" . $value['option_sku'] . "' is duplicate.";
                      continue;
                    } else
                    $duplicateSkus[] = $value['option_sku'];

                    if (strlen($value['option_sku']) > WishProductValidate::MAX_LENGTH_SKU) {
                      $uploadErrors[$productArray['sku']][$value['option_sku']] = "Variant Sku : " . $value['option_sku'] . " must be fewer than 50 characters.";
                      continue;
                    }

                    //echo $value['option_title'];die;
                    $type = Jetproductinfo::checkUpcType($value['option_unique_id']);

                    $optiontitle=$title.'-'.$value['option_title'];

                    // print_r($productUpload);die("dfyhgfgh");
                    
                         $product = [
                      'sku' => $value['option_sku'],
                      'parent_sku'=>$productArray['sku'], //'name' => Data::getName($productArray['title'] . '~' . $value['option_title']),
                      'name' => $optiontitle,
                      'id' => $productArray['product_id'],
                      'variant_id' => $value['option_id'],
                      'tags'=>$optiontitle,
                      'description' => $description,
                      'identifier_type' => $type,
                      'upc' => (string)$value['option_unique_id'],
                      'price' => (string)$value['option_price'],
                      'weight' => (string)$value['option_weight'],
                      'category' => $productArray['category'],
                      'sku_override' => $productArray['sku_override'],
                      'id_override' => $productArray['product_id_override'],
                      'shipping' => "0.0",
                      'size'=>$value['option_title'], // 'tax_code' => $tax_code,
                      'brand' => $brand,
                      'images' => $productArray['image'],
                      'variantGroupId' => $variantGroupId,
                      'product_status' => $value['status']
                      ];
                      
                         $productSku[]=$product['sku'];
                         if($product['product_status'] != "Not Uploaded"){

                            $response=$this->CPutRequest(self::GET_ITEMS_UPDATE_SUB_URL,$product);
                           
                          }
                          else
                            $response=$this->CGetRequest(self::VARIANT_ADD_URL,$product);
                    
                  }
                }
              }
            }
           
            $productUpload=$product;
            $productSku[]=$product['sku'];
            if($productUpload){

                if($productArray['status'] != "Not Uploaded"){

                  $response=$this->CPutRequest(self::GET_ITEMS_UPDATE_SUB_URL,$productUpload);

                 // $productcount=true;
              }
              else{
                  $response=$this->CPostRequest(self::GET_FEEDS_ITEMS_ADD_SUB_URL,$productUpload);
                 // $productcount=true;
              }
              

              if($response['success']==1){

                  if($response['message']=="Successfully"){

                     $updateresponse[]=$response['success'];

                     $query="UPDATE `wish_product` SET status='Items Processing', error='' where product_id='".$productUpload['id']."' and merchant_id='".MERCHANT_ID."'";
                     Data::sqlRecords($query,null,"update");

                 }
                 else{
                    $responseId[]=$response['id'];

                    $query="UPDATE `wish_product` SET status='Items Processing', error='' where product_id='".$productUpload['id']."' and merchant_id='".MERCHANT_ID."'";
                    Data::sqlRecords($query,null,"update");


                }
            }
            else {
                    //print_r($productUpload);die("ghgfhgf");
                //$responseError[] = $response['message'];
                $responseError[$product['sku']] =$response['message'];
               }
           }
       }


       if (isset($updateresponse)) {
          return ['success'=>$updateresponse,'Product Updated on Wish Store'];
      }
      elseif(isset($responseId)){

          return['successID'=>$responseId,"Product Uploaded on Wish Store"];
      }  
      elseif(isset($responseError))
      {

        return ['feederror' => $responseError];
      } 
    if (count($uploadErrors) > 0) {
  
    return ['errors' => $uploadErrors];
    }
}

return ['errors' => 'No product selected for upload.'];
}



public function beforePrepareMPItemValidation(&$product, $merchant_id)
{
  if ($product['identifier_type'] == '')
    $product['identifier_type'] = "UPC";

if ($merchant_id == 468 || $merchant_id=311) {
    $product['upc'] = 'CUSTOM';
    $product['identifier_type'] = 'GTIN';

} else {
    $skipCategory = ['JewelryCategory', 'Jewelry'];

    if (!in_array($product['category'], $skipCategory)) {
      $upc = trim($product['upc']);
      $flag = true;
      if ($upc == "") {
        $message = "Missing barcode.";
        $flag = false;
    } else {
        $type = Jetproductinfo::checkUpcType($upc);
        if ($type == "") {
          $message = "Invalid barcode type.";
          $flag = false;
      } else {
          $validUpc = Jetproductinfo::validateProductBarcode($upc, $product['variant_id'], $merchant_id);
          if (!$validUpc) {
            $message = "Duplicate barcode.";
            $flag = false;
        } else {
            if (MERCHANT_ID != 849) {
              if (!Data::validateUpc($product['upc'])) {
                $message = "Invalid barcode.";
                $flag = false;
            }
        }
    }
}
}

if ($flag) {
    $upc_length = strlen($product['upc']);

    if ($upc_length < 12) {

      $zero_length = 12 - $upc_length;

      for ($i = 0; $i < $zero_length; $i++) {
        $product['upc'] = '0' . $product['upc'];
    }
    $product['identifier_type'] = "UPC";
} elseif ($upc_length == 13) {
  $product['upc'] = '0' . $product['upc'];
  $product['identifier_type'] = "GTIN";
}

} else {
    $message = "Invalid barcode.";
    return ['status' => false, 'error' => $message];
}
}
}
return ['status' => true];
}

public function prepareMPProduct($merchant_id, $product, $productArray, $variantArray)
{
  $mpProduct = WishProductComponent::getMPProductStructure();

  $additionalProductAttributes = [];

  $mpProduct['productName'] = '<![CDATA[' . $product['name'] . ']]>';

  if ($product['sku_override']) {
    $mpProduct['SkuUpdate'] = 'Yes';
    $mpProduct['ProductIdUpdate'] = 'No';
} 
elseif($product['id_override']) {
    $mpProduct['ProductIdUpdate'] = 'Yes';
    $mpProduct['SkuUpdate'] = 'No';
}

        //$category = 'frontend\modules\walmart\components\Category\\'.$productArray['parent_category'];
        //$categoryObj = new $category($merchant_id, $product, $productArray);

$categoryObj = new Category($merchant_id, $product, $productArray, $variantArray);
$categoryData = $categoryObj->prepareCategoryData($additionalProductAttributes);

if(isset($categoryData['status']) && !$categoryData['status']) {
    $error = isset($categoryData['error']) ? $categoryData['error'] : 'Required MpProduct Attributes not filled.';
    return ['status' => false, 'error' => $error];
}
$mpProduct['category'] = $categoryData;

if(count($additionalProductAttributes))
{
    $mpProduct['additionalProductAttributes'] = self::prepareAdditionalProductAttributes($additionalProductAttributes);
}

$validate = WishProductComponent::validateStructure($mpProduct);
if(!$validate['status']) {
    $error = isset($validate['error']) ? $validate['error'] : 'Required MpProduct Attributes not filled';
    return ['status' => false, 'error' => $error];
}

return $mpProduct;
}

public function prepareMPOffer($merchant_id, $product, $productArray, $variantArray)
{
  $mpOffer = WishProductComponent::getMPOfferStructure();

  $mpOffer['price'] = $product['price'];

  $mpOffer['ShippingWeight'] = ['measure' => $product['price'], 'unit' => 'lb'];

  $mpOffer['ProductTaxCode'] = $product['tax_code'];

  $mpOffer['EndDate'] = self::getOfferEndDate();

  $removeFreeShipping = Data::getConfigValue($merchant_id, 'remove_free_shipping');
  if ($removeFreeShipping) {

    if (!empty($product['shipping_exceptions']) && $product['shipping_exceptions'] != "[]") {
      $shipping_exception = self::addShippingException($product['shipping_exceptions'], $removeFreeShipping);
      $mpOffer['ShippingOverrides'] = $shipping_exception;

  } else {
      $freeShippingTag = self::removeFreeShippingData();
      $mpOffer[$freeShippingTag['key']] = $freeShippingTag['value'];
  }
} else {
    if (!empty($product['shipping_exceptions']) && $product['shipping_exceptions'] != "[]") {
      $shipping_exception = self::addShippingException($product['shipping_exceptions'], $removeFreeShipping);
      $mpOffer['ShippingOverrides'] = $shipping_exception;
  }
}


$validate = WishProductComponent::validateStructure($mpOffer);
if(!$validate['status']) {
    $error = isset($validate['error']) ? $validate['error'] : 'Required MpOffer Attributes not filled';
    return ['status' => false, 'error' => $error];
}

return $mpOffer;
}

    /**
     * Get Offer End date of product
     * By Default we are sending date after 5 years as End Date
     */
    public function getOfferEndDate()
    {
        $limit = 5; # No.of years
        $added_timestamp = strtotime('+'.$limit.' year', time());
        $result = date('Y-m-d', $added_timestamp);

        return $result;
    }

    /**
     * Prepare Formatted Additional Product Attributes
     *
     * @param $additionalAttrs []
     * @return []
     */
    public static function prepareAdditionalProductAttributes($additionalAttrs)
    {
      $formattedAdditionalAttributes = [];
      $key = 0;
      foreach ($additionalAttrs as $additionalAttrKey => $additionalAttrVal) {
        $formattedAdditionalAttributes[$key]['additionalProductAttribute']['productAttributeName'] = $additionalAttrKey;
        $formattedAdditionalAttributes[$key]['additionalProductAttribute']['productAttributeValue'] = $additionalAttrVal;
        $key++;
    }

    if($key)
    {
        return [
        '_attribute' => [],
        '_value' => $formattedAdditionalAttributes
        ];
    }
    return $formattedAdditionalAttributes;
}

    /**
     * Add shipping Exception
     *
     * @return []
     */
    public static function addShippingException($shippingExcetion, $removeFree)
    {
      $data = json_decode($shippingExcetion, true);
      $return = [];
      $returnArray = [];
      $prepare = [];
      $shipping_region = [];
      if(!$removeFree) {
        $returnArray[] = ['ShippingOverrideAction' => 'REPLACE_ALL'];
    }

    foreach ($data['isShippingAllowed'] as $key => $value) {
        if ($removeFree) {
          if (strtoupper($data['shipMethod'][$key]) == 'VALUE') {
            $shipping_region[$key] = $data['shipRegion'][$key];
        } else {
            $prepare['shippingOverride']['ShippingOverrideIsShippingAllowed'] = ($value)?'Yes':'No';
            $prepare['shippingOverride']['ShippingOverrideShipRegion'] = $data['shipRegion'][$key];
            $prepare['shippingOverride']['ShippingOverrideShipMethod'] = strtoupper($data['shipMethod'][$key]);
            $prepare['shippingOverride']['ShippingOverrideshipPrice'] = $data['shipPrice'][$key];
            $returnArray[] = $prepare;
        }
    } else {
      $prepare['shippingOverride']['ShippingOverrideIsShippingAllowed'] = ($value)?'Yes':'No';;
      $prepare['shippingOverride']['ShippingOverrideShipRegion'] = $data['shipRegion'][$key];
      $prepare['shippingOverride']['ShippingOverrideShipMethod'] = strtoupper($data['shipMethod'][$key]);
      $prepare['shippingOverride']['ShippingOverrideshipPrice'] = $data['shipPrice'][$key];
      $returnArray[] = $prepare;
  }
}
if (!empty($shipping_region)) {
    $freeShippingTag = self::removeFreeShippingData($shipping_region);
    $returnArray = array_merge($freeShippingTag['value']['_value'], $returnArray);
    foreach ($shipping_region as $skey => $svalue) {
      $prepare['shippingOverride']['ShippingOverrideIsShippingAllowed'] = ($data['isShippingAllowed'][$skey])?'Yes':'No';
      $prepare['shippingOverride']['ShippingOverrideShipRegion'] = $data['shipRegion'][$skey];
      $prepare['shippingOverride']['ShippingOverrideShipMethod'] = strtoupper($data['shipMethod'][$skey]);
      $prepare['shippingOverride']['ShippingOverrideshipPrice'] = $data['shipPrice'][$skey];
      $returnArray[] = $prepare;
  }
} elseif($removeFree) {
    $freeShippingTag = self::removeFreeShippingData();
    $returnArray = array_merge($freeShippingTag['value']['_value'], $returnArray);
}

$return['_attribute'] = [];
$return['_value'] = $returnArray;
return $return;
}

    /**
     * remove free shipping from product
     *
     * @return []
     */
    public static function removeFreeShippingData($method = [])
    {
      $shipRegions = ['STREET_48_STATES', 'PO_BOX_48_STATES', 'STREET_AK_AND_HI', 'PO_BOX_AK_AND_HI',
      'PO_BOX_US_PROTECTORATES', 'STREET_US_PROTECTORATES', 'APO_FPO'];
      if (!empty($method)) {
        $shipRegions = array_diff($shipRegions, $method);
    }
    if (!empty($shipRegions)) {
        $key = 0;
        $freeShippingTag['value']['_value'][$key++]['ShippingOverrideAction'] = 'REPLACE_ALL';
        foreach ($shipRegions as $shipRegion) {
          $freeShippingTag['value']['_attribute'] = [];
          $freeShippingTag['value']['_value'][$key]['shippingOverride']['ShippingOverrideIsShippingAllowed'] = 'No';
          $freeShippingTag['value']['_value'][$key]['shippingOverride']['ShippingOverrideShipRegion'] = $shipRegion;
          $freeShippingTag['value']['_value'][$key]['shippingOverride']['ShippingOverrideShipMethod'] = 'VALUE';
          $freeShippingTag['value']['_value'][$key++]['shippingOverride']['ShippingOverrideshipPrice'] = '0.0';
      }
      $freeShippingTag['key'] = 'ShippingOverrides';
  }
  return $freeShippingTag;
}

    /**
     * validate product
     * @param [] $product
     * @return array | bool
     */
    public static function validateProduct($product)
    {

      $errorArr = [];

      if (strlen($product['sku']) > WishProductValidate::MAX_LENGTH_SKU) {
        $errorArr[] = "SKU must be fewer than ".WishProductValidate::MAX_LENGTH_SKU." character(s).";
    } elseif(strlen($product['sku']) < WishProductValidate::MIN_LENGTH_SKU) {
        $errorArr[] = "SKU must be longer than ".WishProductValidate::MIN_LENGTH_SKU." character(s).";
    }

    if (!$product['title'] && strlen($product['title']) > WishProductValidate::MAX_LENGTH_NAME) {
        $errorArr[] = "Product title must be maximum of ".WishProductValidate::MAX_LENGTH_NAME." character(s) in length";
    } elseif (!$product['title'] && strlen($product['title']) < WishProductValidate::MIN_LENGTH_NAME) {
        $errorArr[] = "Product title must be minimum of ".WishProductValidate::MIN_LENGTH_NAME." character(s) in length";
    }

    if (!$product['description']) {
        $description=strip_tags($product['description']);
        $errorArr[] = "Product description is required";
    }
    if(!$product['image']){
        $errorArr[] = "Product description is required"; 
    }
       /* if (!$product['brand']) {
            $errorArr[] = "Missing brand";
        }*/

        /*if (!$tax_code = Data::GetTaxCode($product, MERCHANT_ID)) {
            $errorArr[] = "Missing Or Invalid product tax code";
        }*/

        if (!$product['category']) {
            $errorArr[] = "Missing Wish Category";
        }

        /*$image = trim($product['image']);
        $countImage = 0;
        $ImageFlag = false;
        $imageArr = explode(',', $image);
        if ($image != "" && count($imageArr) > 0) {
            foreach ($imageArr as $value) {
                if (self::checkRemoteFile($value) == false)
                    $countImage++;
            }
            if (count($imageArr) == $countImage)
                $ImageFlag = true;
        }
        if ($image == '' || $ImageFlag) {
            $errorArr[] = "Missing or Invalid Image,";
        }*/

        //$skipCategory = ['Jewelry', 'Rings'];

        if ($product['type'] == "simple") 
        {
            $price = trim($product['price']);
            if (($price <= 0 || ($price && !is_numeric($price))) || $price == "") {
              $errorArr[] = "Missing Or Invalid price";
          }

          $qty = trim($product['qty']);
          if ($qty == "") {
              $qty = 0;
          } elseif (!is_numeric($qty)) {
              $errorArr[] = "Invalid Inventory";
          }
      } 
      else 
      {
        $par_qty = trim($product['qty']);
        if ($par_qty == "")
          $par_qty = 0;

      $c_par_qty = true;
      if ((trim($par_qty) <= 0 || !is_numeric($par_qty))) {
          $c_par_qty = false;
      }

      $c_par_price = true;
      $par_price = trim($product['price']);
      if ($par_price <= 0 || (trim($par_price) && !is_numeric($par_price)) || trim($par_price) == "") {
          $c_par_price = false;
      }

      $query='select wal.option_id,option_sku,option_image,option_qty,option_price,option_unique_id,wal.status from `jet_product_variants` jet INNER JOIN `wish_product_variants` wal ON jet.product_id=wal.product_id where jet.product_id="'.$product['product_id'].'" and jet.merchant_id="'.MERCHANT_ID.'" and wal.merchant_id="'.MERCHANT_ID.'"';
      $productVarArray = Data::sqlRecords($query,"all","select");

      foreach ($productVarArray as $pro) 
      {
          $opt_sku = trim($pro['option_sku']);
          $price = trim($pro['option_price']);
          $upc = trim($pro['option_unique_id']);

          $qty = trim($pro['option_qty']);
          if ($qty == "") {
            $qty = 0;
        }

        if (!is_numeric($qty)) {
            $errorArr[] = "Invalid Inventory for variant sku: " . $pro['option_sku'];
        }
    }
}

if (count($errorArr) > 0) {
    $validatedProduct['error'] = $errorArr;
} else {
    $validatedProduct = true;
}

return $validatedProduct;
}

public static function isValidateVariant($category)
{
  return Category::getCategoryVariantAttributes($category);
}

    /**
     * To Explode Attributes of the form 'color->colorValue' to array('color','colorValue')
     *
     * @param string $attribute
     * @return array
     */
    public static function explodeAttributes($attribute)
    {
      $delimeter = AttributeMap::ATTRIBUTE_PATH_SEPERATOR;
      $attributes = [];
      if (strpos($attribute, $delimeter) !== false) {
        $attribute = explode($delimeter, $attribute);
        foreach ($attribute as $value) {
          $attributes[] = trim($value);
      }
  } else {
    $attributes = [$attribute];
}
return $attributes;

        /*$delimeter1 = '(';
        $delimeter2 = ')';

        $attributes = [];
        if(strpos($attribute, $delimeter1) !== false && strpos($attribute, $delimeter2) !== false)
        {
            $attribute = explode($delimeter1, $attribute);
            foreach ($attribute as $value) {
                $attributes[] = str_replace($delimeter2, '', $value);
            }
        }
        else
        {
            $attributes = [$attribute];
        }
        return $attributes;*/
    }

    /*Coded by Vishal Start*/
    /**
     * Update Inventory On Wish
     * @param string|[] $ids
     * @return bool
     */
    public function updateInventoryOnWish($product = [], $datafrom = null, $merchant_id = false)
    {
      foreach($product as $pro)
      {
        $productUpload = stream_context_create(array(
          'http' => array(
            'method' => 'POST',
            'ignore_errors' => true,
            ),
          ));
        $productdata = [
        'sku' => $pro['sku'],
        'inventory' => $pro['qty'],
        ];
        $productUpload=$productdata;
        if($productUpload){
          $response=$this->CPutRequest(self::GET_ITEMS_UPDATE_SUB_URL,$productUpload);
          if($response['success']==1){
            return ['success'=>'Product Updated on Wish'];
        }
        else {
            $responseError[] = $response['message'];
        }
    }
}
if($responseError != "")
{
    return ['errors' => $responseError];
}
}

    /**
     * Update Price On Wish
     * @param string|[] $ids
     * @return bool
     */
    public function updatePriceOnWish($product= [], $datafrom = null)
    {
      $productUpload = stream_context_create(array(
        'http' => array(
          'method' => 'POST',
          'ignore_errors' => true,
          ),
        ));
      $productdata = [
      'sku' => $product['sku'],
      'price' => $product['product_price'],
      ];
      $productUpload=$productdata;
      if($productUpload){
        $response=$this->CPutRequest(self::GET_ITEMS_UPDATE_SUB_URL,$productUpload);
        if($response['success']==1){
          $response['message'] = "Price Updated on wish";
          $responseArray['success'] = $response['message'];
          return $responseArray;
        }
        else {
            $responseError['error'] = $response['message'];
            return $responseError;
        }
      }
    }

/*Coded by Vishal*/
/*public function getProductstatus($product)
{
      $count = "";
      $productUpload = stream_context_create(array(
        'http' => array(
          'method' => 'GET',
          'ignore_errors' => true,
          ),
      ));
      $productdata = [
          // 'id' => $pro['product_id'],
      'parent_sku' => $product['sku'],
      ];
      $productUpload=$productdata;
      if($productUpload){
        $response = $this->CGetRequest(self::GET_ITEMS_SUB_URL,$productUpload);
        if($response['success']==1){
          $responseArray['success'] = $response['status_on_wish'];
          $responseArray['product_id'] =$product['product_id'];
          return $responseArray;
        }
        else {
          $responseError['error'] = $response['message'];
          return $responseArray;
        }
      }
  /*else
  {
    $count = "";
    $productUpload = stream_context_create(array(
      'http' => array(
        'method' => 'GET',
        'ignore_errors' => true,
        ),
    ));
    $productdata = [
        // 'id' => $pro['product_id'],
    'parent_sku' => $pro['sku'],
    ];
    $productUpload=$productdata;
    if($productUpload){
      $response = $this->CGetRequest(self::GET_ITEMS_SUB_URL,$productUpload);
      if($response['success']==1){
        $responseArray['success'] = $response['status_on_wish'];
        $responseArray['product_id'] = isset($product['product_id']);
        return $responseArray;
      }
      else {
        $responseError['error'] = $response['message'];
        return $responseError;
      }
    }*/
// }
public function getProductstatus($product)
{
  $count = "";
  $productUpload = stream_context_create(array(
    'http' => array(
      'method' => 'GET',
      'ignore_errors' => true,
      ),
    ));
  if(isset($product['sku']) && ($product['sku']!=""))
  {
    $productdata = [
        // 'id' => $pro['product_id'],
    'parent_sku' => $product['sku'],
    ];
  }
  else
  {
    $productdata = [
        // 'id' => $pro['product_id'],
    'parent_sku' => $product,
    ];
  }
  $productUpload=$productdata;
  if($productUpload){
    $response = $this->CGetRequest(self::GET_ITEMS_SUB_URL,$productUpload);
    if($response['success']==1){
      $responseArray['success'] = $response['status_on_wish'];
      return $responseArray;
    }
    else {
      $responseError['error'] = $response['message'];
      return $responseError;
    }
  }
}
/* end by Vishal */
public static function checkRemoteFile($url)
{
    stream_context_set_default([
      'ssl' => [
      'verify_peer' => false,
      'verify_peer_name' => false,
      ],
      ]);
    $headers = get_headers($url);
    if (substr($headers[0], 9, 3) == '200') {
      return true;
  } else {
      return false;
  }
}

    //acknowledge order
public function acknowledgeOrder($purchaseOrderId, $subUrl = self::GET_ORDERS_SUB_URL)
{
    $response = $this->postRequest($subUrl . '/' . $purchaseOrderId . '/acknowledge',
      [
      'headers' => 'WM_CONSUMER.CHANNEL.TYPE: ' . $this->apiConsumerChannelId,
      ]
      );
    try {

      $response = json_decode((string)$response, true);

      return isset($response['order']) ? $response['order'] : $response;;
  } catch (\Exception $e) {
            //echo $e->getMessage();die;
      return false;
  }

}

    //ship order
public function shipOrder($postData = null, $subUrl = self::GET_ORDERS_SUB_URL)
{
    $purchaseOrderId = $postData['shipments'][0]['purchase_order_id'];
    $shipArray = [
    'ns2:orderShipment' => [
    '_attribute' => [
    'xmlns:ns2' => "http://wish.com/mp/v3/orders",
    'xmlns:ns3' => "http://wish.com/",
    ],
    '_value' => [
    'ns2:orderLines' => [
    '_attribute' => [
    ],
    '_value' => [

    ]
    ],
    ]
    ]
    ];
    $url = 'www.fedex.com';
    if (isset($postData['shipments'][0]['shipment_tracking_url'])) {
      $url = $postData['shipments'][0]['shipment_tracking_url'];
  }
  foreach ($postData['shipments'] as $key => $values) {
      if (!isset($values['shipment_items']) || count($values['shipment_items']) == 0) {
        continue;
    }
    foreach ($values['shipment_items'] as $value) {
        $lineNumber = $value['lineNumber'];

        if (strtolower($postData['shipments'][0]['carrier']) == 'other') {
          $carrier = [
          'ns2:otherCarrier' => 'other'
          ];
      } else {
          $carrier = [
          'ns2:carrier' => $postData['shipments'][0]['carrier']
          ];
      }


      $shipArray['ns2:orderShipment']['_value']['ns2:orderLines']['_value'][] =
      ['ns2:orderLine' => [
      'ns2:lineNumber' => $lineNumber,
      'ns2:orderLineStatuses' => [
      'ns2:orderLineStatus' => [
      'ns2:status' => 'Shipped',
      'ns2:statusQuantity' => [
      'ns2:unitOfMeasurement' => 'Each',
                                    'ns2:amount' => '1'//(string)$value['response_shipment_sku_quantity']
                                    ],
                                    'ns2:trackingInfo' => [
                                    'ns2:shipDateTime' => $postData['shipments'][0]['carrier_pick_up_date'],
                                    'ns2:carrierName' => $carrier,
                                    'ns2:methodCode' => $postData['shipments'][0]['response_shipment_method'],
                                    'ns2:trackingNumber' => $postData['shipments'][0]['shipment_tracking_number'],
                                    'ns2:trackingURL' => $url
                                    ]
                                    ]
                                    ]
                                    ]
                                    ];

                                }

                            }

                            $customGenerator = new Generator();

                            $customGenerator->arrayToXml($shipArray);

                            $str = preg_replace
                            ('/(\<\?xml\ version\=\"1\.0\"\?\>)/', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',
                              $customGenerator->__toString());
                            $params['data'] = $str;

                            $this->requestedXml = $str;
        //$this->createFile($str, ['type' => 'string', 'name' => 'OrderShip']);
                            $params['headers'] = 'WM_CONSUMER.CHANNEL.TYPE: ' . $this->apiConsumerChannelId;
                            $response = $this->postRequest($subUrl . '/' . $purchaseOrderId . '/shipping',
                              $params);
                            try {
                              $parser = new Parser();
                              $response = str_replace('ns:2', '', $response);
                              $data = $parser->loadXML($response)->xmlToArray();
                              return json_encode($data);
                          } catch (\Exception $e) {
            //echo $e->getMessage();die;
            /*$this->_logger->debug('Walmart : shipOrder : Response: '.$response);
            return false;*/
            return ['errors' => [$e->getMessage()]];
        }
    }

    /**
     * Reject Order
     * @param string $purchaseOrderId
     * @param string $dataship
     * @param string $subUrl
     * @return string
     * @link  https://developer.walmartapis.com/#cancelling-order-lines
     */
    public function rejectOrder($purchaseOrderId, $dataship, $subUrl = self::GET_ORDERS_SUB_URL)
    {
      $cancelArray = [
      'ns2:orderCancellation' => [
      '_attribute' => [
      'xmlns:ns2' => "http://wish.com/mp/v3/orders",
      'xmlns:ns3' => "http://wish.com/",
      ],
      '_value' => [
      'ns2:orderLines' => []
      ]
      ]
      ];

      $counter = 0;
      foreach ($dataship['shipments'] as $values) {
        if (!isset($values['cancel_items'])) {
          echo 'shit';
          continue;
      }
      foreach ($values['cancel_items'] as $value) {
          $lineNumbers = explode(',', $value['lineNumber']);
          $cancelArray['ns2:orderCancellation']['_value']['ns2:orderLines']['_attribute'] = [];
          foreach ($lineNumbers as $lineNumber) {
            $cancelArray['ns2:orderCancellation']['_value']['ns2:orderLines']
            ['_value'][$counter]['ns2:orderLine']['ns2:lineNumber'] = (string)$lineNumber;
            $cancelArray['ns2:orderCancellation']['_value']['ns2:orderLines']
            ['_value'][$counter]['ns2:orderLine']['ns2:orderLineStatuses'] = [
            'ns2:orderLineStatus' => [
            'ns2:status' => 'Cancelled',
            'ns2:cancellationReason' => 'CANCEL_BY_SELLER',
            'ns2:statusQuantity' => [
            'ns2:unitOfMeasurement' => 'EACH',
            'ns2:amount' => '1'
            ]
            ]
            ];
            $counter++;
        }
    }

}
$customGenerator = new Generator();
$customGenerator->arrayToXml($cancelArray);
$str = preg_replace('/(\<\?xml\ version\=\"1\.0\"\?\>)/', '<?xml version="1.0" encoding="UTF-8" ?>',
    $customGenerator->__toString());

$params['data'] = $str;
$params['headers'] = 'WM_CONSUMER.CHANNEL.TYPE: ' . $this->apiConsumerChannelId;
        //$this->createFile($str, ['type' => 'string', 'name' => 'CancelOrder']);
$response = $this->postRequest($subUrl . '/' . $purchaseOrderId . '/cancel',
    $params);

try {
    $parser = new Parser();
    $response = str_replace('ns2:', '', $response);
    $response = str_replace('ns3:', '', $response);
    $response = str_replace('ns4:', '', $response);
    $data = $parser->loadXML($response)->xmlToArray();
    return $data;
} catch (\Exception $e) {
            //$this->_logger->debug('Reject Order : NO JSON Response . Response Was :- '.$response);
    return false;
}
}

    /**
     * Refund Order
     * @param string $purchaseOrderId
     * @param string $orderData
     * @param string $subUrl
     * @return string
     * @link  https://developer.walmartapis.com/#cancelling-order-lines
     */
    public function refundOrder($purchaseOrderId, $orderData, $subUrl = self::GET_ORDERS_SUB_URL)
    {
      $refundData = [
      'ns2:orderRefund' => [
      '_attribute' => [
      'xmlns:ns2' => "http://wish.com/mp/v3/orders",
      'xmlns:ns3' => "http://wish.com/",
      ],
      '_value' => [
      'ns2:purchaseOrderId' => $purchaseOrderId,
      'ns2:orderLines' => [
      'ns2:orderLine' => [
      'ns2:lineNumber' => $orderData['lineNumber'] . '',
      'ns2:refunds' => [
      'ns2:refund' => [
      'ns2:refundComments' => $orderData['refundComments'],
      'ns2:refundCharges' => [
      '_attribute' => [],
      '_value' => [
                                            /*0 => [
                                                'ns2:refundCharge' => [
                                                    'ns2:refundReason' => $orderData['refundReason'],
                                                    'ns2:charge' => [
                                                        'ns2:chargeType' => 'Product',
                                                        'ns2:chargeName' => 'Item Price',
                                                        'ns2:chargeAmount' => [
                                                            'ns2:currency' => 'USD',
                                                            'ns2:amount' => "-".$orderData['amount']
                                                        ],
                                                        'ns2:tax' => [
                                                            'ns2:taxName' => 'Item Price Tax',
                                                            'ns2:taxAmount' => [
                                                                'ns2:currency' => 'USD',
                                                                'ns2:amount' => "-".$orderData['taxAmount']
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                                ]*/
                                                ]
                                                ]
                                                ]
                                                ]
                                                ]
                                                ],
                                                ]
                                                ]
                                                ];

                                                $refundCharges = [];
                                                $refundChargeIndex = 0;
                                                foreach ($orderData['charges'] as $chargeKey => $chargeValue) {
                                                  if ($chargeKey == 'PRODUCT') {
                                                    $chargeName = 'Item Price';
                                                    $taxName = 'Item Price Tax';
                                                } elseif ($chargeKey == 'SHIPPING') {
                                                    if (!$orderData['includeShipping'])
                                                      continue;
                                                  $chargeName = 'Shipping Price';
                                                  $taxName = 'Shipping Tax';
                                              } else {
                                                continue;
                                            }

                                            $refundCharges[$refundChargeIndex] = ['ns2:refundCharge' => [
                                            'ns2:refundReason' => $orderData['refundReason'],
                                            'ns2:charge' => [
                                            'ns2:chargeType' => $chargeKey,
                                            'ns2:chargeName' => $chargeName,
                                            'ns2:chargeAmount' => [
                                            'ns2:currency' => $chargeValue['chargeAmount']['currency'],
                                            'ns2:amount' => "-" . $chargeValue['chargeAmount']['amount']
                                            ],
                                            'ns2:tax' => []
                                            ]
                                            ]
                                            ];

                                            if (isset($chargeValue['tax']) && count($chargeValue['tax'])) {
                                                $refundTax = [
                                                'ns2:taxName' => $taxName,
                                                'ns2:taxAmount' => [
                                                'ns2:currency' => $chargeValue['tax']['taxAmount']['currency'],
                                                'ns2:amount' => "-" . $chargeValue['tax']['taxAmount']['amount']
                                                ]
                                                ];
                                                $refundCharges[$refundChargeIndex]['ns2:refundCharge']['ns2:charge']['ns2:tax'] = $refundTax;
                                            } else {
                                                unset($refundCharges[$refundChargeIndex]['ns2:refundCharge']['ns2:charge']['ns2:tax']);
                                            }
                                            $refundChargeIndex++;
                                        }

                                        if (count($refundCharges)) {
                                          $refundData['ns2:orderRefund']['_value']['ns2:orderLines']['ns2:orderLine']['ns2:refunds']['ns2:refund']['ns2:refundCharges']['_value'] = $refundCharges;

                                          $customGenerator = new Generator();
                                          $customGenerator->arrayToXml($refundData);
                                          $str = preg_replace('/(\<\?xml\ version\=\"1\.0\"\?\>)/', '<?xml version="1.0" encoding="UTF-8" ?>',
                                            $customGenerator->__toString());
                                          $this->requestedXml = $str;
                                          $params['data'] = $str;

            //var_dump($str);die;
            //$params['headers'] = 'WM_CONSUMER.CHANNEL.TYPE: ' . $this->apiConsumerChannelId;
            //$this->createFile($str, ['type' => 'string', 'name' => 'RefundOrder']);
                                          $response = $this->postRequest($subUrl . '/' . $purchaseOrderId . '/refund',
                                            $params);
                                          try {
                                            $parser = new Parser();
                                            $response = str_replace('ns:2', '', $response);
                                            $response = str_replace('ns:4', '', $response);
                                            $response = $parser->loadXML($response)->xmlToArray();
                //var_dump($response);die;
                                            return $response;
                                        } catch (\Exception $e) {
                                            $this->_logger->debug('Refund Order : NO JSON Response . Response Was :- ' . $response);
                                            return false;
                                        }
                                    } else {
                                      return $result['ns4:errors']['ns4:error']['ns4:description'] = "No Items Found for Refund.";
                                  }
                              }

    /**
     * @return array
     */
    public function refundreasonOptionArr()
    {
      return [
      [
      'value' => '', 'label' => __('Please Select an Option')
      ],
      [
      'value' => 'BillingError', 'label' => __('BillingError')
      ],
      [
      'value' => 'TaxExemptCustomer', 'label' => __('TaxExemptCustomer')
      ],
      [
      'value' => 'ItemNotAsAdvertised', 'label' => __('ItemNotAsAdvertised')
      ],
      [
      'value' => 'IncorrectItemReceived', 'label' => __('IncorrectItemReceived')
      ],
      [
      'value' => 'CancelledYetShipped', 'label' => __('CancelledYetShipped')
      ],
      [
      'value' => 'ItemNotReceivedByCustomer', 'label' => __('ItemNotReceivedByCustomer')
      ],
      [
      'value' => 'IncorrectShippingPrice', 'label' => __('IncorrectShippingPrice')
      ],
      [
      'value' => 'DamagedItem', 'label' => __('DamagedItem')
      ],
      [
      'value' => 'DefectiveItem', 'label' => __('DefectiveItem')
      ],
      [
      'value' => 'CustomerChangedMind', 'label' => __('CustomerChangedMind')
      ],
      [
      'value' => 'CustomerReceivedItemLate', 'label' => __('CustomerReceivedItemLate')
      ],
      [
      'value' => 'Missing Parts / Instructions', 'label' => __('Missing Parts / Instructions')
      ],
      [
      'value' => 'Finance -> Goodwill', 'label' => __('Finance -> Goodwill')
      ],
      [
      'value' => 'Finance -> Rollback', 'label' => __('Finance -> Rollback')
      ]
      ];
  }

    /**
     *
     * convert xml response to array
     * @param $xml
     */
    public static function xmlToArray($xml, $inventory = false)
    {
      $parser = new Parser();
      $data = $parser->loadXML($xml)->xmlToArray();

      if ($inventory) {
        $data = self::replaceInventoryString($data);
    } else {
        $data = self::replaceString($data);
    }
    return $data;
}

    /**
     * save product status(s) for uploaded
     * products of products
     * @param Response
     * @param id
     */
    public static function saveStatus($id, $response)
    {
      if (is_array($response) && count($response) > 0 && isset($response['MPItemView'][0], $response['MPItemView'][0]['publishedStatus'])) {
            //update product status
        $query = "update `wish_product` set status='" . $response['MPItemView'][0]['publishedStatus'] . "' where product_id='" . $id . "'";
        Data::sqlRecords($query, null, "update");
    }
}

public static function deleteFeed($feedId)
{
  if ($feedId) {
    $query = "delete from `wish_product_feed` where feedId='" . $feedId . "'";
    Data::sqlRecords($query, null, "delete");
}
}

public static function is_json($string)
{
  try {
    // $data = json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE) ?: FALSE;
} catch (Exception $e) {
    return false;
}
}

public function getTestOrder()
{
  $timestamp = time();
  $data = [
  'elements' => [
  'order' =>
  [
  '0' =>
  [
  'purchaseOrderId' => $timestamp,
  'customerOrderId' => $timestamp,
  'customerEmailId' => 'satyaprakash@cedcoss.com',
  'orderDate' => '1478225612000',
  'shippingInfo' => [
  'phone' => '8608181642',
  'estimatedDeliveryDate' => $timestamp,
  'estimatedShipDate' => $timestamp,
  'methodCode' => 'Standard',
  'postalAddress' =>
  [
  'name' => 'Test',
  'address1' => '15 Brown ave Ext',
  'address2' => '',
  'city' => 'STAFFORD SPRINGS',
  'state' => 'CT',
  'postalCode' => '06076',
  'country' => 'USA',
  'addressType' => 'RESIDENTIAL'
  ]

  ],

  'orderLines' => [

  'orderLine' => $this->getProducts(),

  ]

  ]

  ]

  ]

  ];
  return $data;
}

public function getProducts()
{
  $products = [];

  $query = "SELECT * FROM `jet_product` WHERE `merchant_id`=14 and sku!='' LIMIT 0,5";
  $productCollection = Data::sqlRecords($query, "all", "select");

  $count = 1;
  foreach ($productCollection as $product) {
    $products[] = [
    'lineNumber' => $count++,
    'item' => [
    'productName' => $product['title'],
    'sku' => $product['sku'],
    ],

    'charges' => [
    'charge' => [
    '0' => [
    'chargeType' => 'PRODUCT',
    'chargeName' => 'ItemPrice',
    'chargeAmount' => [
    'currency' => 'USD',
    'amount' => $product['price']
    ],


    ]

    ]

    ],

    'orderLineQuantity' => [
    'unitOfMeasurement' => 'EACH',
    'amount' => 1
    ],

    'statusDate' => '1478226601000',
    'orderLineStatuses' => [
    'orderLineStatus' => [
    '0' => [
    'status' => 'Acknowledged',
    'statusQuantity' => [
    'unitOfMeasurement' => 'EACH',
    'amount' => 1
    ],

    'cancellationReason' => '',
    'trackingInfo' => '',
    ],

    ],

    ],

    'refund' => '',
    ];


}

return $products;

}

public static function replaceString($data = [])
{
  if (is_array($data)) {
    $string = json_encode($data);
    $string = preg_replace('/(ns\d:)+/', '', $string);
    $data = json_decode($string, true);
    return $data;
}
}

public static function replaceInventoryString($data = [])
{
  if (is_array($data)) {
    $string = json_encode($data);
    $string = preg_replace('/(wm:)+/', '', $string);
    $data = json_decode($string, true);
    return $data;
}
}

    /**
     * Update Bulk Promotional Price On Walmart
     * @param string|[] $productIds (comma seperated string of Ids OR array of Ids)
     * @return bool
     */
    public function updateBulkPromotionalPriceOnWalmart($productIds)
    {
      $flag = false;
      $merchant_id = MERCHANT_ID;

      if (is_array($productIds)) {
        $productIds = implode(',', $productIds);
    }

    $query = 'SELECT * FROM `wish_promotional_price` WHERE `merchant_id`=' . $merchant_id . ' AND `product_id` IN (' . $productIds . ')';
    $result = Data::sqlRecords($query, 'all');

    $promoData = [];
    if ($result) {
        foreach ($result as $_result) {
          $promoData[$_result['sku']][] = $_result;
      }
  }

  $promoPriceArray = [
  'PriceFeed' => [
  '_attribute' => [
  'xmlns' => "http://wish.com/",
  ],
  '_value' => [
  0 => [
  'PriceHeader' => [
  'version' => '1.5.1',
                            //'feedDate' => '2016-04-18T11:46:32.483-07:00'
  ]
  ]
  ]
  ]
  ];
  if (count($promoData)) {
    $PriceIndex = 1;
    foreach ($promoData as $sku => $data) {
      $promoPriceArray['PriceFeed']['_value'][$PriceIndex] = [
      'Price' => [
      '_attribute' => [],
      '_value' => [
      'itemIdentifier' => [
      '_attribute' => [],
      '_value' => [
      'sku' => (string)$sku
      ]
      ],
      'pricingList' => [
      '_attribute' => [
      'replaceAll' => 'false'
      ],
      '_value' => []
      ]
      ]
      ]
      ];

      $pricingList = [];

      foreach ($data as $key => $value) {
        $processMode = "UPSERT";
        if ($value['to_delete'])
          $processMode = "DELETE";

      $pricingList[$key] = [
      'pricing' => [
      '_attribute' => [
      'effectiveDate' => $value['effective_date'],
      'expirationDate' => $value['expiration_date'],
      'processMode' => $processMode
      ],
      '_value' => [
      'currentPrice' => [
      '_attribute' => [],
      '_value' => [
      'value' => [
      '_attribute' => [
      'amount' => $value['special_price']
      ],
      '_value' => []
      ]
      ]
      ],
      'currentPriceType' => [
      '_attribute' => [],
      '_value' => $value['current_price_type']
      ],
      'comparisonPrice' => [
      '_attribute' => [],
      '_value' => [
      'value' => [
      '_attribute' => [
      'amount' => $value['original_price']
      ],
      '_value' => []
      ]
      ]
      ]
      ]
      ]
      ];
  }

  if (count($pricingList)) {
    if (!$flag)
      $flag = true;

  $promoPriceArray['PriceFeed']['_value'][$PriceIndex]['Price']['_value']['pricingList']['_value'] = $pricingList;
} else {
    unset($promoPriceArray['PriceFeed']['_value'][$PriceIndex]);
}
$PriceIndex++;
}
}

if ($flag) {
    if (!file_exists(\Yii::getAlias('@webroot') . '/var/product/bulkpromoprice/xml/' . MERCHANT_ID)) {
      mkdir(\Yii::getAlias('@webroot') . '/var/product/bulkpromoprice/xml/' . MERCHANT_ID, 0775, true);
  }

  $fileName = 'PromoPrice-' . time() . '.xml';
  $file = Yii::getAlias('@webroot') . '/var/product/bulkpromoprice/xml/' . MERCHANT_ID . '/' . $fileName;
  $xml = new Generator();
  $xml->arrayToXml($promoPriceArray)->save($file);

  $response = $this->postRequest(self::UPDATE_BULK_PROMOTIONAL_PRICE_SUB_URL, ['file' => $file]);
  $responseArray = self::xmlToArray($response);

  if (isset($responseArray['FeedAcknowledgement']['feedId'])) {
      $query = "UPDATE `wish_promotional_price` SET `walmart_status`='{WalmartPromoStatus::PROMOTIONAL_PRICE_STATUS_PROCESSING}' WHERE `product_id` IN ($productIds)";
      Data::sqlRecords($query, null, 'update');

      $result = ['success' => true, 'message' => 'Successfully Updated!!', 'feedId' => $responseArray['FeedAcknowledgement']['feedId']];
  } elseif (isset($responseArray['errors']['error'])) {
      if (isset($responseArray['errors']['error']['code']) && $responseArray['errors']['error']['code'] == 'UNAUTHORIZED.GMP_GATEWAY_API')
        $result = ['error' => true, 'message' => 'Invalid Api Details.'];
}
} else {
    $result = ['error' => true, 'message' => 'No Promotional Price Found in Selected Products.'];
}
return $result;
}

    /**
     * Update Promotional Price On Walmart
     * @param string|[] $productIds (comma seperated string of Ids OR array of Ids)
     * @return bool
     */
    public function updatePromotionalPriceOnWalmart($productIds)
    {
      $merchant_id = MERCHANT_ID;

      if (is_array($productIds)) {
        $productIds = implode(',', $productIds);
    }

    $query = 'SELECT * FROM `wish_promotional_price` WHERE `merchant_id`=' . $merchant_id . ' AND `product_id` IN (' . $productIds . ')';
    $result = Data::sqlRecords($query, 'all');

    if ($result) {
        $promoData = [];
        foreach ($result as $_result) {
          $promoData[$_result['sku']][] = $_result;
      }
  }

  $promoPriceArray = [];
  if (count($promoData)) {
    $PriceIndex = 0;
    foreach ($promoData as $sku => $data) {
      $promoPriceArray = [
      'Price' => [
      '_attribute' => [
      'xmlns:' => "http://wish.com/",
      ],
      '_value' => [
      'itemIdentifier' => [
      '_attribute' => [],
      '_value' => [
      'sku' => (string)$sku
      ]
      ],
      'pricingList' => [
      '_attribute' => [],
      '_value' => []
      ]
      ]
      ]
      ];

      $pricingList = [];

      foreach ($data as $key => $value) {
        $processMode = "UPSERT";
        if ($value['to_delete'])
          $processMode = "DELETE";

      $pricingList[$key] = [
      'pricing' => [
      '_attribute' => [
      'effectiveDate' => $value['effective_date'],
      'expirationDate' => $value['expiration_date'],
      'processMode' => $processMode
      ],
      '_value' => [
      'currentPrice' => [
      '_attribute' => [],
      '_value' => [
      'value' => [
      '_attribute' => [
      'amount' => $value['special_price']
      ],
      '_value' => []
      ]
      ]
      ],
      'currentPriceType' => [
      '_attribute' => [],
      '_value' => $value['current_price_type']
      ],
      'comparisonPrice' => [
      '_attribute' => [],
      '_value' => [
      'value' => [
      '_attribute' => [
      'amount' => $value['original_price']
      ],
      '_value' => []
      ]
      ]
      ]
      ]
      ]
      ];
  }

  $promoPriceArray['Price']['_value']['pricingList']['_value'] = $pricingList;

  if (count($pricingList)) {
    if (!file_exists(\Yii::getAlias('@webroot') . '/var/product/promoprice/xml/' . MERCHANT_ID)) {
      mkdir(\Yii::getAlias('@webroot') . '/var/product/promoprice/xml/' . MERCHANT_ID, 0775, true);
  }

  $fileName = $sku . '.xml';
  $file = Yii::getAlias('@webroot') . '/var/product/promoprice/xml/' . MERCHANT_ID . '/' . $fileName;
  $xml = new Generator();
  $xml->arrayToXml($promoPriceArray)->save($file);


  $response = $this->putRequest(self::UPDATE_PROMOTIONAL_PRICE_SUB_URL, ['file' => $file]);
  $responseArray = self::xmlToArray($response);
  if (isset($responseArray['FeedAcknowledgement']['feedId'])) {
      $result = ['success' => true, 'message' => 'Successfully Updated!!', 'feedId' => $responseArray['FeedAcknowledgement']['feedId']];
  } elseif (isset($responseArray['errors']['error'])) {
      if (isset($responseArray['errors']['error']['code']) && $responseArray['errors']['error']['code'] == 'UNAUTHORIZED.GMP_GATEWAY_API')
        $result = ['error' => true, 'message' => 'Unauthorized'];
}
}
}
}
        die('updatePromotionalPriceOnWalmart');//remove
    }

    /*Coded by Vishal Kumar Start*/

    public function updateWishprice($product = [])
    {
        $productUpload = stream_context_create(array(
          'http' => array(
            'method' => 'POST',
            'ignore_errors' => true,
            ),
          ));
        $product = [
        'sku' => $product['sku'],
        'price' => $product['price'],
        ];
        $productUpload=$product;
        if($productUpload){
          $response=$this->CPutRequest(self::GET_ITEMS_SUB_URL,$productUpload);
          if($response['success']==1){
            return ['success'=>'Product Uploaded on Wish'];
        }
        else {

            $responseError[] = $response['message'];
            return ['errors' => $responseError];
        }

    }
    return $responseArray;
}

public function updateWishinventory($product = [])
{ 
    $productUpload = stream_context_create(array(
      'http' => array(
        'method' => 'POST',
        'ignore_errors' => true,
        ),
      ));

    $product = [
    'sku' => $product['sku'],
    'inventory' => $product['qty'],
    ];
    $productUpload=$product;
    if($productUpload){
      $response=$this->CPutRequest(self::GET_ITEMS_SUB_URL,$productUpload);
      if($response['success']==1){
        return ['success'=>'Product Uploaded on Wish'];
    }
    else {
        $responseError[] = $response['message'];
        return ['errors' => $responseError];
    }
} 
return $responseArray;
}
/*end by Vishal Kumar*/

public function getItem($sku, $returnField = null, $subUrl = self::GET_ITEMS_SUB_URL)
{
    $response = $this->getRequest($subUrl . '?sku=' . $sku);
    try {
      $response = json_decode($response, true);
      if ($returnField && !isset($response['error'])) {
        return $response['MPItemView'][0]['publishedStatus'];
    }
    return $response;
} catch (Exception $e) {
  return false;
}
}
    /**
     * Update Price On Walmart
     * @param string|[] $ids
     * @return bool
     */
    public function batchupdatePriceOnWalmart($product, $datafrom = null)
    {
       /* $isEnablePutRequest=false;
        if(count($product)=='1'){
            $isEnablePutRequest=true;
        }*/
        $merchant_id = MERCHANT_ID;
        $error = [];
        $timeStamp = (string)time();
        $priceArray = [
        'PriceFeed' => [
        '_attribute' => [
        'xmlns:gmp' => "http://wish.com/",
        ],
        '_value' => [
        0 => [
        'PriceHeader' => [
        'version' => '1.5',
        ],
        ],
        ]
        ]
        ];
        $isPriceFeed = 0;
        $key = 0;
        foreach ($product as $id ) {
            $query = Data::sqlRecords('select jet.id,COALESCE(jpv.option_sku,sku) as sku,type,COALESCE(wpv.option_prices,jpv.option_price,wal.product_price,jet.price) as price,jet.merchant_id,wpv.option_id from (SELECT * FROM `wish_product` WHERE `merchant_id`="' . MERCHANT_ID . '" AND `product_id`="'.$id.'") as wal INNER JOIN (SELECT * FROM `jet_product` WHERE `merchant_id`="'.MERCHANT_ID.'" AND `id`="'.$id.'") as jet ON jet.id=wal.product_id LEFT JOIN (SELECT * FROM `wish_product_variants` WHERE `merchant_id`="' . MERCHANT_ID . '"  AND `product_id`="'.$id.'") as wpv ON wpv.product_id=wal.product_id LEFT JOIN jet_product_variants as jpv ON wpv.option_id=jpv.option_id where (wal.status="'.WishProduct::PRODUCT_STATUS_UPLOADED.'" OR wal.status="'.WishProduct::PRODUCT_STATUS_UNPUBLISHED.'" OR wal.status="'.WishProduct::PRODUCT_STATUS_STAGE.'" OR wpv.status="'.WishProduct::PRODUCT_STATUS_UPLOADED.'" OR wpv.status="'.WishProduct::PRODUCT_STATUS_UNPUBLISHED.'" OR wpv.status="'.WishProduct::PRODUCT_STATUS_STAGE.'")and wal.merchant_id="' . MERCHANT_ID . '"', 'all');
            if(isset($query) && !empty($query)){
              if (is_array($query) && count($query) > 0) {
                foreach ($query as $querykey => $queryvalue) {
                  $isPriceFeed++;
                  if ($queryvalue['type'] == 'variants') {
                    $check = [];
                    $sku = Data::getProductSku($queryvalue['option_id']);
                    $check['sku']=$sku;
                    $isRepricingEnabled = WishRepricing::isRepricingEnabled($check);
                    if($isRepricingEnabled){
                      $error[$sku]="Product Price Not Updated due to repricing enable";
                      continue;
                  }
                  $key += 1;
                                //walmart product price

                  $price = Data::getWalmartPrice($queryvalue['option_id'], MERCHANT_ID);

                  if (isset($price['option_prices']) && !empty($price['option_prices'])) {
                      $queryvalue['price'] = WishRepricing::getProductPrice($price['option_prices'], 'variants', $queryvalue['option_id'], MERCHANT_ID);
                  }
                  else{
                      $queryvalue['price'] = WishRepricing::getProductPrice($queryvalue['price'], 'variants', $queryvalue['option_id'], MERCHANT_ID);
                  }
                  $priceArray['PriceFeed']['_value'][$key] = [
                  'Price' => [
                  'itemIdentifier' => [
                  'sku' => $sku
                  ],
                  'pricingList' => [
                  'pricing' => [
                  'currentPrice' => [
                  'value' => [
                  '_attribute' => [
                  'currency' => CURRENCY,
                  'amount' => $queryvalue['price']
                  ],
                  '_value' => [

                  ]
                  ]
                  ],
                  'currentPriceType' => 'BASE',
                  'comparisonPrice' => [
                  'value' => [
                  '_attribute' => [
                  'currency' => CURRENCY,
                  'amount' => $queryvalue['price']
                  ],
                  '_value' => [

                  ]
                  ]
                  ],
                  ]
                  ]
                  ]
                  ];

              } else {
                $check = [];
                $check['sku'] = $queryvalue['sku'];
                $isRepricingEnabled = WishRepricing::isRepricingEnabled($check);
                if($isRepricingEnabled){
                  $error[$pro['sku']]="Product Price Not Updated due to repricing enable";
                  continue;
              }
                            //update custom price on walmart
                            /*$updatePrice = Data::getCustomPrice($pro['price'],$pro['merchant_id']);
                            if($updatePrice)
                            $pro['price']=$updatePrice;*/
                            /*$queryvalue['price'] = WalmartRepricing::getProductPrice($queryvalue['price'], 'simple', $queryvalue['id'], MERCHANT_ID);*/
                             //walmart product price
                            $price = Data::getWalmartPrice($queryvalue['id'], MERCHANT_ID);

                            if (isset($price['product_price']) && !empty($price['product_price'])) {
                              $queryvalue['price'] = WishRepricing::getProductPrice($price['product_price'], 'simple', $queryvalue['id'], MERCHANT_ID);

                          }
                          else{
                              $queryvalue['price'] = WishRepricing::getProductPrice($queryvalue['price'], 'simple', $queryvalue['id'], MERCHANT_ID);
                          }
                          $key += 1;
                          $priceArray['PriceFeed']['_value'][$key] = [
                          'Price' => [
                          'itemIdentifier' => [
                          'sku' => $queryvalue['sku']
                          ],
                          'pricingList' => [
                          'pricing' => [
                          'currentPrice' => [
                          'value' => [
                          '_attribute' => [
                          'currency' => CURRENCY,
                          'amount' => $queryvalue['price']
                          ],
                          '_value' => [

                          ]
                          ]
                          ],
                          'currentPriceType' => 'BASE',
                          'comparisonPrice' => [
                          'value' => [
                          '_attribute' => [
                          'currency' => CURRENCY,
                          'amount' => $queryvalue['price']
                          ],
                          '_value' => [

                          ]
                          ]
                          ],
                          ]
                          ]
                          ]
                          ];
                      }
                  }
              }

          }
          else{
              $sku = Data::getProductSku($id);
              $error[$sku]="Product Not Found on wish"; 
          }
      }  
      if ($isPriceFeed > 0) {
        if (!file_exists(\Yii::getAlias('@webroot') . '/var/product/xml/' . MERCHANT_ID . '/price')) {
          mkdir(\Yii::getAlias('@webroot') . '/var/product/xml/' . MERCHANT_ID . '/price', 0775, true);
      }
      $file = Yii::getAlias('@webroot') . '/var/product/xml/' . MERCHANT_ID . '/price/MPProduct-' . time() . '.xml';
      $xml = new Generator();

      $xml->arrayToXml($priceArray)->save($file);
                /*if($isEnablePutRequest){
                    $response = $this->putRequest(self::GET_FEEDS_PRICE_SUB_URL, ['file' => $file]);
                    $responseArray = self::xmlToArray($response);
                    if (isset($responseArray['ItemPriceResponse']['message'])) {
                            return ['feedId' => '1235647'];
                        }
                   elseif (isset($responseArray['errors'])) {
                        return ['errors' => $responseArray['errors'],'erroredSkus' => $error,'error_count'=>count($error)];
                    }
                }
                else{*/
                  $response = $this->postRequest(self::GET_FEEDS_PRICE_SUB_URL, ['file' => $file]);
                  $responseArray = self::xmlToArray($response);
                  if (isset($responseArray['FeedAcknowledgement'])) {
                    $result = $this->getFeeds($responseArray['FeedAcknowledgement']['feedId']);
                    if (isset($results['results'][0], $results['results'][0]['itemsSucceeded']) && $results['results'][0]['itemsSucceeded'] == 1) {
                      return ['feedId' => $responseArray['FeedAcknowledgement']['feedId']];
                  }
                  else{
                      return ['feedId' => $responseArray['FeedAcknowledgement']['feedId'],'erroredSkus' => $error,'error_count'=>count($error)];
                  }
              } elseif (isset($responseArray['errors'])) {
                return ['errors' => $responseArray['errors'],'erroredSkus' => $error,'error_count'=>count($error)];
            }
            /* }*/
        }
        else{
          return ['erroredSkus' => $error,'error_count'=>count($error)];
      }

  }


    /**
     * Update Inventory On Walmart
     * @param string|[] $product
     * @return array
     */
//     public function batchupdateInventoryOnWalmart($product, $datafrom = null)
//     {
//         /*$isEnablePutRequest = false;
//         if (count($product) == '1') {
//             $isEnablePutRequest = true;
//         }*/
//         $error = [];
//         $inventoryArray = [
//         'InventoryFeed' => [
//         '_attribute' => [
//         'xmlns' => "http://wish.com/",
//         ],
//         '_value' => [
//         0 => ['InventoryHeader' => [
//         'version' => '1.4',
//         ],
//         ],
//         ]
//         ]
//         ];
//         $timeStamp = (string)time();
//         $isInvFeed = 0;
//         $key = 0;
//         foreach ($product as $id) {
//             $query = Data::sqlRecords('select jet.id,sku,type,qty,jet.merchant_id from (SELECT * FROM `wish_product` WHERE `merchant_id`="' . MERCHANT_ID . '" AND `product_id`="' . $id . '") as wal INNER JOIN (SELECT * FROM `jet_product` WHERE `merchant_id`="' . MERCHANT_ID . '" AND `id`="' . $id . '") as jet ON jet.id=wal.product_id where (wal.status="' . WishProduct::PRODUCT_STATUS_UPLOADED . '" OR wal.status="' . WishProduct::PRODUCT_STATUS_UNPUBLISHED . '" OR wal.status="' . WishProduct::PRODUCT_STATUS_STAGE . '")and wal.merchant_id="' . MERCHANT_ID . '"', 'one');

//             if (isset($query) && !empty($query)) {
//               if (is_array($query) && count($query) > 0) {
//                 $isInvFeed++;
//                 if ($query['type'] == 'variants') {
//                   $varProducts = [];
//                   $query1 = "select option_sku,option_qty from `jet_product_variants` where product_id='" . $query['id'] . "'";
//                   $varProducts = Data::sqlRecords($query1, "all", "select");
//                   foreach ($varProducts as $value) {
//                     $key += 1;
//                     $inventoryArray['InventoryFeed']['_value'][$key] = [
//                     'inventory' => [
//                     'sku' => $value['option_sku'],
//                     'quantity' => [
//                     'unit' => 'EACH',
//                     'amount' => $value['option_qty'],
//                     ],
//                     'fulfillmentLagTime' => isset($query['fulfillment_lag_time']) ? $query['fulfillment_lag_time'] : '1',
//                     ]
//                     ];
//                 }
//             } else {
//               $key += 1;
//               $inventoryArray['InventoryFeed']['_value'][$key] = [
//               'inventory' => [
//               'sku' => $query['sku'],
//               'quantity' => [
//               'unit' => 'EACH',
//               'amount' => $query['qty'],
//               ],
//               'fulfillmentLagTime' => isset($query['fulfillment_lag_time']) ? $query['fulfillment_lag_time'] : '1',
//               ]
//               ];
//           }
//       }

//   } else {
//       $sku = Data::getProductSku($id);
//       $error[$sku] = "Product Not Found on wish";
//   }
// }
// if ($isInvFeed > 0) {
//     $path = 'product/update/' . date('d-m-Y') . '/' . MERCHANT_ID . '/inventory';
//     $dir = \Yii::getAlias('@webroot') . '/var/' . $path;
//     $logFile = $path . '/update.log';
//     if (!file_exists($dir)) {
//       mkdir($dir, 0775, true);
//   }
//   $file = $dir . '/MPProduct-' . time() . '.xml';
//   $xml = new Generator();
//   $xml->arrayToXml($inventoryArray)->save($file);

//   Data::createLog('calling Post Request function : ', $logFile);
//             /*if ($isEnablePutRequest) {
//                 $response = $this->putRequest(self::GET_FEEDS_INVENTORY_SUB_URL, ['file' => $file]);
//                 Data::createLog("inventory response: " . PHP_EOL . $response . PHP_EOL, $logFile);
//                 try {
//                     $responseArray = self::xmlToArray($response, true);
//                 } catch (Exception $e) {
//                     $path = $dir . '/Exception.log';
//                     Data::createLog("inventory response: " . PHP_EOL . $response . PHP_EOL, $path, true);
//                 }
//                 if (isset($responseArray['inventory']['sku'])) {
//                     return ['feedId' => '1235647'];
//                 } elseif (isset($responseArray['errors'])) {
//                     return ['errors' => $responseArray['errors'], 'erroredSkus' => $error, 'error_count' => count($error)];
//                 }
//             } else {*/
//                 $response = $this->postRequest(self::GET_FEEDS_INVENTORY_SUB_URL, ['file' => $file]);
//                 Data::createLog("inventory response: " . PHP_EOL . $response . PHP_EOL, $logFile);
//                 try {
//                   $responseArray = self::xmlToArray($response);
//               } catch (Exception $e) {
//                   $path = $dir . '/Exception.log';
//                   Data::createLog("inventory response: " . PHP_EOL . $response . PHP_EOL, $path, true);
//               }
//               if (isset($responseArray['FeedAcknowledgement'])) {
//                   $result = [];
//                   $result = $this->getFeeds($responseArray['FeedAcknowledgement']['feedId']);
//                   if (isset($result['results'][0], $result['results'][0]['itemsSucceeded']) && $result['results'][0]['itemsSucceeded'] == 1) {
//                     return ['feedId' => $responseArray['FeedAcknowledgement']['feedId'], 'erroredSkus' => $error, 'error_count' => count($error)];
//                 } else {
//                     return ['feedId' => $responseArray['FeedAcknowledgement']['feedId'], 'erroredSkus' => $error, 'error_count' => count($error)];
//                 }
//             } elseif (isset($responseArray['errors'])) {
//               return ['errors' => $responseArray['errors'], 'erroredSkus' => $error, 'error_count' => count($error)];
//           }
//           /* }*/
//       } else {
//         return ['erroredSkus' => $error, 'error_count' => count($error)];
//     }

// }

public function updateSingleInventory()
{
  $url = 'v2/inventory?sku=AKT60LE';

  $signature = $this->apiSignature->getSignature($url, 'PUT', $this->apiConsumerId, $this->apiPrivateKey);
  $url = $this->apiUrl . $url;

  $headers = [];
  $headers[] = "WM_SVC.NAME: Wish Marketplace";
  $headers[] = "WM_QOS.CORRELATION_ID: " . base64_encode(\phpseclib\Crypt\Random::string(16));
  $headers[] = "WM_SEC.TIMESTAMP: " . $this->apiSignature->timestamp;
  $headers[] = "WM_SEC.AUTH_SIGNATURE: " . $signature;
  $headers[] = "WM_CONSUMER.ID: " . $this->apiConsumerId;
  $headers[] = "WM_CONSUMER.CHANNEL.TYPE: 7b2c8dab-c79c-4cee-97fb-0ac399e17ade";

  $headers[] = "Content-Type: application/xml";

  $headers[] = "Accept: application/xml";

  $headers[] = "HOST: marketplace.wishapis.com";

        //Working //$body = '<wm:inventory xmlns:wm="http://walmart.com/"><wm:sku>AKT60LE</wm:sku><wm:quantity><wm:unit>EACH</wm:unit><wm:amount>5</wm:amount></wm:quantity><wm:fulfillmentLagTime>1</wm:fulfillmentLagTime></wm:inventory>';

  $body = '<?xml version="1.0" encoding="UTF-8"?><wm:inventory xmlns:wm="http://wish.com/"><wm:sku>AKT60LE</wm:sku><wm:quantity><wm:unit>EACH</wm:unit><wm:amount>5</wm:amount></wm:quantity><wm:fulfillmentLagTime>1</wm:fulfillmentLagTime></wm:inventory>';

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $server_output = curl_exec($ch);

  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
  $header = substr($server_output, 0, $header_size);
  $response = substr($server_output, $header_size);
  curl_close($ch);

        //var_dump($response);die;

  $responseArray = self::xmlToArray($response);
  var_dump($responseArray);
  die;
}


/*Pre inventory xml data for single product*/

public function prepareInventoryData($inventoryArray,$product=[])
{
  // print_r($inventoryArray);die();
         // $inventoryArray['wm:inventory']['_value'] = [
         //            'wm:sku' => $product['sku'],
         //            'wm:quantity' => [
         //                'wm:unit' => 'EACH',
         //                'wm:amount' => $product['qty'],
         //            ],
         //            'wm:fulfillmentLagTime' => isset($product['fulfillment_lag_time']) ? $product['fulfillment_lag_time'] : '1',
         //    ];
         //    return $inventoryArray;
}


public function underTrial($merchant_id){
  $trialdetail=WishExtensionDetail::find()->where(['merchant_id'=>$merchant_id])->one();
        // $status=$trialdetail->status;
  $app_status=$trialdetail->app_status;

  if($status=='Not Purchase' && $app_status=='install' && $merchant_id!=345 && $merchant_id!=333 && $merchant_id!=294){
    $totalproduct=WishProduct::find()->where(['merchant_id'=>$merchant_id])->all();
    $totalproduct=count($totalproduct);


    $percentproduct=$totalproduct*(10/100);
    $trialdetail=WishProduct::find()->where(['merchant_id'=>$merchant_id])->andFilterWhere(['or',
      ['like','status','PUBLISHED'],
      ['like','status','Items Processing']])->all();

    /**total uploaded product*/
    $product=count($trialdetail);

    if($product<$percentproduct){
      $data=array('not_purchase','1');
      return $data;
  }
  else{
      $data=array('not_purchase','0');
      return $data;
  }
}
else{
    return "purchase";
}
}

public function updateinventoryonapp($product){

  $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);
  $connection=Yii::$app->getDb();
  $merchant_id = MERCHANT_ID;

  foreach ($product as $up_app){

            //echo $up_app['bigproduct_id'];
    $resource='catalog/products/'.$up_app['bigproduct_id'].'?include=variants';
    $products= $bigcom->get($resource);

    $products=$products['data'];
    if($products){

                //$skuData='products/'.$up_app['bigproduct_id'].'/skus';

      $sku=$products['variants'];

      if($sku){
        foreach ($sku as $sku_up){
          $query='UPDATE `jet_product_variants` SET option_qty="'.$sku_up['inventory_level'].'" where product_id="'.$up_app['bigproduct_id'].'" and `merchant_id`="'.$merchant_id.'" and `option_sku`="'.addslashes($sku_up['sku']).'"';
          $updateResult = $connection->createCommand($query)->execute();
      }
  }
                //echo $products->inventory_level;

  $query='UPDATE `jet_product` SET qty="'.$products['inventory_level'].'" where  bigproduct_id="'.$up_app['bigproduct_id'].'" and `merchant_id`="'.$merchant_id.'"';
  $updateResult = $connection->createCommand($query)->execute();
}
}
return 1;
}

public function updatepriceonapp($product){


  $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);
  $connection=Yii::$app->getDb();
  $merchant_id = MERCHANT_ID;


  foreach ($product as $up_app){
    $resource='catalog/products/'.$up_app['bigproduct_id'];
    $products= $bigcom->get($resource);
    $products=$products->data;

    if($products){
                //echo $products->inventory_level;
      if($merchant_id==202){
        $query='UPDATE `jet_product` SET price="'.$products->sale_price.'" where  bigproduct_id="'.$up_app['bigproduct_id'].'" and `merchant_id`="'.$merchant_id.'"';
        $updateResult = $connection->createCommand($query)->execute();
    }
    else{    
        $query='UPDATE `jet_product` SET price="'.$products->price.'" where  bigproduct_id="'.$up_app['bigproduct_id'].'" and `merchant_id`="'.$merchant_id.'"';
        $updateResult = $connection->createCommand($query)->execute();
    }
}
}
return 1;
}
}