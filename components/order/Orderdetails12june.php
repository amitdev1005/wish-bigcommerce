<?php
/**
 * Created by PhpStorm.
 * User: cedcoss
 * Date: 11/1/17
 * Time: 6:06 PM
 */
namespace frontend\modules\neweggmarketplace\components\order;

use frontend\modules\neweggmarketplace\components\Cronrequest;
use frontend\modules\neweggmarketplace\components\Data;
use frontend\modules\neweggmarketplace\components\Helper;
use frontend\modules\neweggmarketplace\components\Neweggapi;
use frontend\modules\neweggmarketplace\components\Sendmail;
use frontend\modules\neweggmarketplace\components\ShopifyClientHelper;
use frontend\modules\neweggmarketplace\models\NeweggOrderDetail;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Url;

class Orderdetails extends Component
{
    public static function orderdetails($status, $config = [])
    {
        if (!empty($config)) {

            $merchant_id = $config['merchant_id'];
        } else {
            $merchant_id = MERCHANT_ID;
        }
        $type = 'OrderDetails';
        $return = '';
        $body = array(
            "OperationType" => "GetOrderInfoRequest",
            "RequestBody" => array(
                "RequestCriteria" => array(
                    "Status" => $status,
                ),
            ),
        );
        $path = '/order/fetchorderdetail/' . $merchant_id . '/' . date('d-m-Y') . '/' . time();
        Helper::createLog("---order request send--- " . PHP_EOL . json_encode($body) . PHP_EOL, $path, 'a+');

        $url = '/ordermgmt/order/orderinfo';
        $param = ['append' => '&version=304', 'body' => json_encode($body), 'config' => $config];
        $response = Cronrequest::getRequest($url, $param, $config);

        Helper::createLog("---order fetch response from newegg --- " . PHP_EOL . $response . PHP_EOL, $path, 'a+');

        $data = json_decode($response, true);

        if (!empty($data['ResponseBody']['OrderInfoList'])) {
            if (isset($data['ResponseBody']['OrderInfoList'])) {
                foreach ($data['ResponseBody']['OrderInfoList'] as $value) {
                    $sku_array = array();
                    foreach ($value['ItemInfoList'] as $val) {
                        $sku_array[] = $val['SellerPartNumber'];
                    }

                    $sku = implode(',', $sku_array);
                    $query = "SELECT `order_number` FROM `newegg_order_detail` WHERE `merchant_id` = '" . $merchant_id . "' AND `order_number` = '" . $value['OrderNumber'] . "'";
                    $order_number = Data::sqlRecords($query, null, 'one');

                    $order_import_number = Data::sqlRecords("SELECT `order_number` FROM `newegg_order_import_error` WHERE `merchant_id` = '" . $merchant_id . "' AND `order_number` = '" . $value['OrderNumber'] . "'", 'one');

                    if ($order_import_number['order_number']) {
                        $query = 'DELETE FROM `newegg_order_import_error` where merchant_id="' . $merchant_id . '" AND `order_number`="' . $value['OrderNumber'] . '"';

                        Data::sqlRecords($query, null, "delete");
                    }

                    if ($order_number) {
                        try {
                            $order_data = addslashes(json_encode($value));
                            $date = date("d-m-Y H:i:s", strtotime($value['OrderDate']));

                            $query = "UPDATE `newegg_order_detail` SET `merchant_id`='" . $merchant_id . "',`order_total`='" . $value['OrderTotalAmount'] . "',`seller_id`='" . $data['SellerID'] . "',`order_number`='" . $value['OrderNumber'] . "',`order_data`='" . $order_data . "',`order_status_description`='" . $value['OrderStatusDescription'] . "',`invoice_number`='" . $value['InvoiceNumber'] . "',`order_date`='" . $date . "',`sku`= '" . $sku . "' WHERE `merchant_id`='" . $merchant_id . "' AND `order_number`='" . $value['OrderNumber'] . "'";
                            Data::sqlRecords($query, null, 'update');

                            $return[] = ['status' => 'success', 'updated' => $value['OrderNumber']];

                        } catch (Exception $e) {
                            $return[] = ['status' => 'error', 'error' => $order_number . '=> OrderNumber' . $e->getMessage()];
                        }
                    } else {
                        try {
                            $order_data = addslashes(json_encode($value));
                            $date = date("d-m-Y H:i:s", strtotime($value['OrderDate']));

                            $query = "INSERT into `newegg_order_detail` (`merchant_id`,`seller_id`,`order_number`,`order_data`,`order_status_description`,`invoice_number`,`order_date`,`order_total`,`sku`) VALUES ('" . $merchant_id . "','" . $data['SellerID'] . "','" . $value['OrderNumber'] . "','" . $order_data . "','" . $value['OrderStatusDescription'] . "','" . $value['InvoiceNumber'] . "','" . $date . "','" . $value['OrderTotalAmount'] . "','" . $sku . "')";
                            Data::sqlRecords($query, null, 'insert');

                            //Order fetch mail

                            $emailData = Data::sqlRecords('SELECT * FROM `newegg_shop_detail` WHERE `merchant_id`="' . $merchant_id . '"', 'one');
                            Sendmail::fetchOrder($emailData['email'], $value['OrderNumber'], $value['OrderStatusDescription']);

                            $return[] = ['status' => 'success', 'inserted' => $value['OrderNumber']];

                        } catch (Exception $e) {
                            $return[] = ['status' => 'error', 'error' => $e->getMessage()];

                        }
                    }

                }
                // creating order in shopify
                if ($status == '0') {
                    if (!empty($config)) {
                        Orderdetails::syncorders($path, $config);
                        /*$url = Yii::getAlias('@webneweggurl')."/walmart-webhook/inventoryupdate?maintenanceprocess=1";
                        Data::sendCurlRequest($inventoryData,$url);*/
                    } else {
                        Orderdetails::syncorders($path);
                    }
                }

            }
        } else {
            $return[] = ['status' => 'error', 'error' => 'No data found'];
        }
        return $return;
    }

    public function viewOrder($id)
    {
        $merchant_id = MERCHANT_ID;
        if ($id) {
            try {
                $orderdata = Data::sqlRecords("SELECT `order_status_description`,`order_number` FROM `newegg_order_detail` WHERE `merchant_id`='" . $merchant_id . "' AND `id`='" . $id . "'", 'one');
                $type = 'OrderDetails';
                $return = '';
                $body = array(
                    "OperationType" => "GetOrderInfoRequest",
                    "RequestBody" => array(
                        "RequestCriteria" => array(
                            "OrderNumberList" => array(
                                "OrderNumber" => $orderdata['order_number']
                            ),
                        ),
                    ),
                );
                $path = '/order/vieworder/' . $merchant_id . '/' . date('d-m-Y') . '/' . time();
                Helper::createLog("---order request send--- " . PHP_EOL . json_encode($body) . PHP_EOL, $path, 'a+');

                $url = '/ordermgmt/order/orderinfo';
                $param = ['append' => '&version=304', 'body' => json_encode($body)];
                $response = Cronrequest::getRequest($url, $param);

                Helper::createLog("---order fetch response from newegg --- " . PHP_EOL . $response . PHP_EOL, $path, 'a+');

                $data = json_decode($response, true);

                if (!empty($data['ResponseBody']['OrderInfoList'])) {
                    if (isset($data['ResponseBody']['OrderInfoList'])) {
                        foreach ($data['ResponseBody']['OrderInfoList'] as $value) {
                            $sku_array = array();
                            foreach ($value['ItemInfoList'] as $val) {
                                $sku_array[] = $val['SellerPartNumber'];
                            }

                            $sku = implode(',', $sku_array);
                            $query = "SELECT `order_number` FROM `newegg_order_detail` WHERE `merchant_id` = '" . $merchant_id . "' AND `order_number` = '" . $value['OrderNumber'] . "'";
                            $order_number = Data::sqlRecords($query, null, 'one');
                            if ($order_number) {
                                try {
                                    $order_data = addslashes(json_encode($value));
                                    $date = date("d-m-Y H:i:s", strtotime($value['OrderDate']));

                                    $query = "UPDATE `newegg_order_detail` SET `merchant_id`='" . $merchant_id . "',`order_total`='" . $value['OrderTotalAmount'] . "',`seller_id`='" . $data['SellerID'] . "',`order_number`='" . $value['OrderNumber'] . "',`order_data`='" . $order_data . "',`order_status_description`='" . $value['OrderStatusDescription'] . "',`invoice_number`='" . $value['InvoiceNumber'] . "',`order_date`='" . $date . "',`sku`= '" . $sku . "' WHERE `merchant_id`='" . $merchant_id . "' AND `order_number`='" . $value['OrderNumber'] . "'";
                                    Data::sqlRecords($query, null, 'update');

                                    $return[] = ['status' => 'success', 'updated' => $value['OrderNumber']];

                                } catch (Exception $e) {
                                    $return[] = ['status' => 'error', 'error' => $order_number . '=> OrderNumber' . $e->getMessage()];
                                }
                            }

                        }
                    }
                } else {
                    $return[] = ['status' => 'error', 'error' => 'No data found'];
                }

                $query = "SELECT `order_data` FROM `newegg_order_detail` WHERE `merchant_id`='" . $merchant_id . "' AND `id`='" . $id . "'";
                $data = Data::sqlRecords($query, null, 'one');

                return $data;
            } catch (Exception $e) {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            return ['status' => 'error', 'message' => 'Id not defined'];
        }
    }

    public function syncorders($path = [], $config = [])
    {

        if (!empty($config)) {
            $merchant_id =928;
        } else {
            $merchant_id = MERCHANT_ID;
        }
        print_r($merchant_id);die;
        $countOrder = 0;
        $count = 0;
        $shopifyError = "";
        $return = [];
        $model = Data::sqlRecords("SELECT shop_url,token FROM `newegg_shop_detail` where merchant_id='" . $merchant_id . "'", 'one');

        $fieldname = 'shopify_order_sync';
        $value = Data::getConfigValue($merchant_id, $fieldname);

        if ($value == 'No' && empty($config))
        {
            $return = ['status' => 'error', 'message' => 'Merchant do not want to sync order in shopify.'];
            return $return;
        }
        /*if(empty($path)){*/
        //$path = '/order/fetchorderdetail/'.$merchant_id.'/'.date('d-m-Y').'/'.time();
        $path = '/order/syncorderdetail/' . $merchant_id . '/' . date('d-m-Y') . '/' . time();
        /* }*/

        Helper::createLog("---order sync start--- " . PHP_EOL, $path, 'a+');
        Helper::createLog("---merchant id--- " . PHP_EOL . $merchant_id . PHP_EOL, $path, 'a+');
        Helper::createLog("---user--- " . PHP_EOL . json_encode($model) . PHP_EOL, $path, 'a+');

        if (!empty($model) && !empty($model['token'])) {
            try {
                $token = "";
                $shopname = "";
                $item_array = array();
                $ikey = 0;
                $token = $model['token'];
                $shopname = $model['shop_url'];
                $shopifyError = "";
                $data = Data::sqlRecords("SELECT * FROM `newegg_order_detail` WHERE merchant_id='" . $merchant_id . "' AND order_status_description='Unshipped' AND shopify_order_id IS NULL ", 'all');
                $sc = new ShopifyClientHelper($shopname, $token, PUBLIC_KEY, PRIVATE_KEY);

                if (count($data) > 0) {

                    foreach ($data as $value) {

                        $result = '';
                        $reason = '';
                        $newegg_item_no = '';
                        $result = json_decode($value['order_data'], true);

                        if (count($result) > 0) {

                            $firstaddress = $result['ShipToAddress1'];
                            $secondaddress = $result['ShipToAddress2'];
                            $phone_number = $result['CustomerPhoneNumber'];
                            $billing_addr = array(
                                "customer_name" => $result['CustomerName'],
                                "address1" => $firstaddress,
                                "address2" => $secondaddress,
                                "phone" => $phone_number,
                                "city" => $result['ShipToCityName'],
                                "province" => $result['ShipToStateCode'],
                                "country" => $result['ShipToCountryCode'],
                                "zip" => $result['ShipToZipCode']
                            );
                            $shipping_addr = array(
                                "first_name" => $result['ShipToFirstName'],
                                "last_name" => $result['ShipToLastName'],
                                "address1" => $firstaddress,
                                "address2" => $secondaddress,
                                "phone" => $phone_number,
                                "city" => $result['ShipToCityName'],
                                "province" => $result['ShipToStateCode'],
                                "country" => $result['ShipToCountryCode'],
                                "zip" => $result['ShipToZipCode']
                            );
                            $ikey = 0;
                            $itemArray = [];
                            foreach ($result['ItemInfoList'] as $val) {

                                $collection = "";

                                $collection = Data::sqlRecords("SELECT id,sku,vendor,variant_id,qty,title FROM `jet_product` WHERE merchant_id='" . $merchant_id . "' AND sku='" . $val['SellerPartNumber'] . "'", 'one');
                                if ($collection == "") {

                                    $collectionOption = "";
                                    $collectionOption = Data::sqlRecords("SELECT option_id,product_id,option_sku,vendor,option_qty FROM `jet_product_variants` WHERE merchant_id='" . $merchant_id . "' AND option_sku='" . $val['SellerPartNumber'] . "'", 'one');
                                    if ($collectionOption == "") {
                                        $reason[] = 'Product sku' . $val['SellerPartNumber'] . ' not available in shopify';
                                        $newegg_item_no[] = $val['NeweggItemNumber'];

                                        $error_array[$result['OrderNumber']] = array(
                                            'order_number' => $result['OrderNumber'],
                                            'merchant_id' => $merchant_id,
                                            'reason' => $reason,
                                            'created_at' => date("Y-d-m H:i:s"),
                                            'newegg_item_number' => $newegg_item_no
                                        );
                                        $count++;

                                        continue;
                                    } elseif ($collectionOption && $result['OrderQty'] > $collectionOption['option_qty']) {

                                        $reason[] = 'Requested Order quantity is not available for product Option sku: ' . $val['SellerPartNumber'] . ' not available in shopify';
                                        $newegg_item_no[] = $val['NeweggItemNumber'];

                                        $count++;
                                        $error_array[$result['OrderNumber']] = array(
                                            'order_number' => $result['OrderNumber'],
                                            'merchant_id' => $merchant_id,
                                            'reason' => $reason,
                                            'created_at' => date("Y-d-m H:i:s"),
                                            'newegg_item_number' => $newegg_item_no,
                                        );

                                        continue;

                                    } else {
                                        $itemArray[$ikey]['product_id'] = $collectionOption['product_id'];
                                        $itemArray[$ikey]['title'] = $collection['product_title'];
                                        $itemArray[$ikey]['variant_id'] = $collectionOption['option_id'];
                                        $itemArray[$ikey]['vendor'] = $collectionOption['vendor'];
                                        $itemArray[$ikey]['sku'] = $collectionOption['option_sku'];
                                        $itemArray[$ikey]['price'] = $val['UnitPrice'];
                                        $itemArray[$ikey]['quantity'] = $val['OrderedQty'];

                                    }
                                } elseif ($collection && $result['OrderQty'] > $collection['qty']) {
                                    continue;
                                } else {
                                    $itemArray[$ikey]['product_id'] = $collection['id'];
                                    $itemArray[$ikey]['title'] = $collection['title'];//$value['product_title']; // if error ['line item: title is too long']
                                    $itemArray[$ikey]['variant_id'] = $collection['variant_id'];
                                    $itemArray[$ikey]['vendor'] = $collection['vendor'];
                                    $itemArray[$ikey]['sku'] = $collection['sku'];
                                    $itemArray[$ikey]['price'] = $val['UnitPrice'];
                                    $itemArray[$ikey]['quantity'] = $val['OrderedQty'];
                                }
                                $ikey++;
                            }

                            if (!empty($itemArray) && (count($result['ItemInfoList']) == count($itemArray))) {

                                $Orderarray['order'] = array(
                                    "line_items" => $itemArray,
                                    "customer" => array(
                                        "first_name" => $result['CustomerName'],
                                        "email" => $result['CustomerEmailAddress']
                                    ),
                                    "billing_address" => $billing_addr,
                                    "shipping_address" => $shipping_addr,
                                    "note" => "Newegg-Integration(newegg.com)",
                                    'tags' => "jet.com",
                                    "email" => $result['CustomerEmailAddress'],
                                    "inventory_behaviour" => "decrement_obeying_policy",
                                    "financial_status" => "paid",
                                    "shipping_lines" => array(
                                        array(
                                            "title" => $result['ShipService'],
                                            "price" => $result['ShippingAmount'],
                                            "code" => $result['ShipService'],
                                            "source" => "Newegg",
                                            "requested_fulfillment_service_id" => null,
                                            "delivery_category" => null,
                                        )
                                    ),
                                    "format" => "json"
                                );

                                Helper::createLog("---request to shopify--- " . PHP_EOL . json_encode($Orderarray) . PHP_EOL, $path, 'a+');

                                $response = array();
                                $response = $sc->call('POST', '/admin/orders.json', $Orderarray);

                                Helper::createLog("---shopify response--- " . PHP_EOL . json_encode($response) . PHP_EOL, $path, 'a+');

                                if (isset($itemArray) && !empty($itemArray)) {
                                    $lineArray = json_encode($itemArray);
                                    //. implode(',', $lineArray) .

                                } else {
                                    $lineArray = array();
                                }
                                if (!array_key_exists('errors', $response)) {

                                    try {
                                        $query = "UPDATE `newegg_order_detail` SET  shopify_order_name='" . $response['name'] . "',shopify_order_id='" . $response['id'] . "',lines_items='" . $lineArray . "'
                                                    where merchant_id='" . $merchant_id . "' AND seller_id='" . $result['SellerID'] . "' AND order_number='" . $result['OrderNumber'] . "'";

                                        $countOrder++;

                                        Data::sqlRecords($query, null, 'update');

                                        //mail for order

                                        $emailData = Data::sqlRecords('SELECT * FROM `newegg_shop_detail` WHERE `merchant_id`="' . $merchant_id . '"', 'one');
                                        Sendmail::neworderMail($emailData['email'], $result['OrderNumber'], $response['id']);


                                    } catch (Exception $e) {
                                        echo $e->getMessage();
                                    }

                                } else {
                                    $shopifyError .= $result['SellerID'] . "=> Error: " . json_encode($response['errors']) . "\n";
                                }
                            } else {
                                $response['errors'] = 'Order Number => ' . $result['OrderNumber'] . '=>' . $val['SellerPartNumber'] . 'items not defined';
                                $shopifyError .= $result['SellerID'] . "=> Error: " . json_encode($response['errors']) . "\n";
                            }
                        }
                    }

                }

            } catch (Exception $e) {
                echo $e->getMessage();
                Helper::createLog("---shopify error--- " . PHP_EOL . $e->getMessage() . PHP_EOL, $path, 'a+');

            }
        } else {
            Helper::createLog("---shopify error--- " . PHP_EOL . 'Invalid username or auth key' . PHP_EOL, $path, 'a+');
        }
        //create order import error
        $errorCount = 0;
        if ($count > 0 && count($error_array) > 0) {
            $errorFlag = false;
            $message1 = "";

            $config_data = Data::sqlRecords("SELECT * FROM `newegg_config` WHERE `merchant_id`= '" . $merchant_id . "' AND `data`='cancel_order'", 'one');

            foreach ($error_array as $order_error) {

                if (isset($config_data['value']) && $config_data['value'] == 'Yes') {
                    self::cancelorder($order_error['order_number']);
                }

                $result = "";
                $result = Data::sqlRecords("SELECT * FROM `newegg_order_import_error` WHERE order_number='" . $order_error['order_number'] . "' AND merchant_id='" . $merchant_id . "'", 'one');
                /*                $result = Data::sqlRecords("SELECT * FROM `newegg_order_import_error` WHERE order_number='" . $order_error['order_number'] . "' AND newegg_item_number='" . $order_error['newegg_item_number'] . "'", 'one');*/
                if ($result && ($result['order_number'] == $order_error['order_number'])) {

                    $reason = implode(',', $order_error['reason']);
                    $newegg_item_no = implode(',', $order_error['newegg_item_number']);

                    /*print_r($order_error);
                    print_r($reason);*/

                    $sql1 = 'UPDATE `newegg_order_import_error` SET `error_reason`="' . $reason . '" , `newegg_item_number`="' . $newegg_item_no . '" WHERE `order_number` = "' . $order_error['order_number'] . '" AND `merchant_id`="' . $order_error['merchant_id'] . '"';
                    /*echo '<pre>';
                    print_r($sql1);*/
                    Data::sqlRecords($sql1, null, 'update');

                } else {

                    $reason = implode(',', $order_error['reason']);
                    $newegg_item_no = implode(',', $order_error['newegg_item_number']);

                    $sql = 'INSERT INTO `newegg_order_import_error`(`order_number`,`merchant_id`,`error_reason`,`newegg_item_number`,`created_at`)
                            VALUES("' . $order_error['order_number'] . '","' . $order_error['merchant_id'] . '","' . $reason . '","' . $newegg_item_no . '","' . $order_error['created_at'] . '")';


                    $emailData = Data::sqlRecords('SELECT * FROM `newegg_shop_detail` WHERE `merchant_id`="' . $merchant_id . '"', 'one');
                    Sendmail::orderError($emailData['email'], $order_error['order_number'], $reason);

                    /*echo '<pre>';
                    print_r($sql);*/
                    try {
                        $errorCount++;
                        $model = Data::sqlRecords($sql, null, 'insert');
                    } catch (Exception $e) {
                        $message1 .= 'Invalid query: ' . $e->getMessage() . "\n";
                    }
                }
            }

        }
        if (count($shopifyError) > 0) {

            Helper::createLog("---shopify error--- " . PHP_EOL . $shopifyError . PHP_EOL, $path, 'a+');

            $return = ['status'=>'error','message'=>"Order(s) not created in shopify:\n" . $shopifyError];
            return $return;
//            Yii::$app->session->setFlash('error', "Order(s) not created in shopify:\n" . $shopifyError);
//            return false;
        }
        if ($countOrder > 0) {
//            Yii::$app->session->setFlash('success', $countOrder . " Order Created in shopify...");
//            return true;
            $return = ['status'=>'success','message'=>$countOrder . " Order Created in shopify..."];
            return $return;
        }
        return false;
    }

    public static function cancelorder($order_number)
    {
        $type = 'CancelOrder';
        $return[] = '';
        $action = 1;
        $value = 24;

        $merchant_id = MERCHANT_ID;
        if ($order_number) {
            try {
                $data = Data::sqlRecords("SELECT `order_number`,`seller_id` FROM `newegg_order_detail` WHERE `merchant_id`='" . $merchant_id . "' AND `order_number`='" . $order_number . "'", 'one');
                $body = array(
                    "Action" => $action,
                    "Value" => $value
                );
                $url = '/ordermgmt/orderstatus/orders/' . $data['order_number'];
                /*$param = ['append' => '&version=304', 'body' => json_encode($body)];*/

                $newegg_config = Helper::configurationDetail($merchant_id);

                $newegg_helper = new Neweggapi($newegg_config['seller_id'], $newegg_config['authorization'], $newegg_config['secret_key']);

                $param = [
                    'append' => '&version=304',
                    'body' => json_encode($body),
                    'authorization' => $newegg_config['authorization'],
                    'secretKey' => $newegg_config['secret_key'],
                    'url' => $url
                ];

                $response = $newegg_helper->putRequest($url, $param);
                /*$response = Neweggapi::putRequest($url, $param);*/

                /*$log = self::orderlog($response, $type, MERCHANT_ID);*/

                $lastchar = substr($response, strlen($response) - 1);
                $firstchar = substr($response, 0);
                if ($firstchar[0] == '[') {
                    $string = substr($response, 0);
                } else {
                    $string = $response;
                }
                if ($lastchar == ']') {
                    $string = substr($string, 0, -1);
                }
                $value = json_decode($string, true);
                if (!isset($value['Code']) && isset($value['IsSuccess'])) {

                    $query = "UPDATE `newegg_order_detail` SET `order_status_description`='" . $value['Result']['OrderStatus'] . "' WHERE `merchant_id`='" . $merchant_id . "' AND `order_number`='" . $value['Result']['OrderNumber'] . "' AND `seller_id`= '" . $data['seller_id'] . "'";
                    Data::sqlRecords($query, null, 'update');

                    $emailData = Data::sqlRecords('SELECT * FROM `newegg_shop_detail` WHERE `merchant_id`="' . $merchant_id . '"', 'one');
                    Sendmail::cancelOrder($emailData['email'], $value['Result']['OrderNumber'], $value['Result']['OrderStatus']);

                    $return = ['status' => 'success', 'message' => 'Cancelled order successfully'];
                } else {
                    $return = ['status' => 'error', 'message' => $value['Message']];
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                $return = ['status' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            $return = ['status' => 'error', 'message' => 'Id not defined'];
        }

        return $return;
    }

    public function shiporder($id)
    {
        $type = 'ShipOrder';
        $return = [];
        $merchant_id = MERCHANT_ID;
        $action = '2';
        $emailData = Data::sqlRecords('SELECT * FROM `newegg_shop_detail` WHERE `merchant_id`="' . $merchant_id . '"', 'one');
        $ship_error = [];

        if ($id) {
            try {
                $model = Data::sqlRecords("SELECT * FROM `newegg_order_detail` WHERE `merchant_id`='" . $merchant_id . "' AND `id`='" . $id . "'", 'one');

                $path = '/order/ship/' . $merchant_id . '/' . date('d-m-Y') . '/' . time();

                Helper::createLog("---order shipment data--- " . json_encode($model), $path, 'a+');


                $order_detail = json_decode($model['order_data'], true);

                if (isset($model['unfulfilled_array'])) {
                    $model_unfulfilled = json_decode($model['unfulfilled_array'], true);
                }

                $shipmentDetails = array();

                /*$shopname = Yii::$app->user->identity->username;
                $token = Yii::$app->user->identity->auth_key;*/

                $storeDetail = Helper::storeDetail($merchant_id);

                $sc = new ShopifyClientHelper($storeDetail['shop_url'], $storeDetail['token'], PUBLIC_KEY, PRIVATE_KEY);

                $shipmentDetails = $sc->call('GET', '/admin/orders/' . $model['shopify_order_id'] . '.json');

                Helper::createLog("---order shipment response from shopify--- " . json_encode($shipmentDetails), $path, 'a+');

                if (!empty($shipmentDetails) && !isset($shipmentDetails['errors'])) {
                    $Unfulfilled_item = [];


                    if (isset($shipmentDetails['fulfillments']) && !empty($shipmentDetails['fulfillments'])) {

                        $package = [];

                        foreach ($shipmentDetails['fulfillments'] as $fulfillments) {

                            $Item = [];
                            if (!empty($shipmentDetails['fulfillments'])) {

                                foreach ($fulfillments['line_items'] as $value) {

                                    if ($value['fulfillment_status'] == 'fulfilled' && $value['fulfillable_quantity'] == 0) {

                                        $Item[] = [
                                            'SellerPartNumber' => $value['sku'],
                                            'ShippedQty' => $value['quantity']
                                        ];

                                    } elseif ($value['fulfillment_status'] == 'partial' && $value['fulfillable_quantity'] > 0) {

                                        $Unfulfilled_item[] = $value;
                                    }

                                }

                            }
                            /*$tracking_company = ['UPS','UPS MI','FedEX','DHL','USPS'];
                            if(!in_array($fulfillments['tracking_company'],$tracking_company))
                            {
                                $fulfillments['tracking_company'] = 'Other';
                            }*/
                            if (!empty($Item)) {
                                $package[] = [
                                    'TrackingNumber' => $fulfillments['tracking_number'],
                                    'ShipCarrier' => $fulfillments['tracking_company'],
                                    'ShipService' => $fulfillments['tracking_company'],
                                    'ItemList' => [
                                        'Item' => $Item
                                    ],
                                ];
                            }

                        }
                        if (!empty($Unfulfilled_item)) {
                            $unfulfilled_array = addslashes(json_encode($Unfulfilled_item));

                            $query = "UPDATE `newegg_order_detail` SET `unfulfilled_array`='" . $unfulfilled_array . "' WHERE `merchant_id` = '" . $merchant_id . "' AND `seller_id` ='" . $model['seller_id'] . "' AND `order_number` = '" . $model['order_number'] . "'";
                            $update = Data::sqlRecords($query, null, 'update');
                        }

                        $body = ['Action' => $action,
                            'Value' => [
                                'Shipment' => [
                                    'Header' => [
                                        'SellerID' => $order_detail['SellerID'],
                                        'SONumber' => $order_detail['OrderNumber']
                                    ],
                                    'PackageList' => [
                                        'Package' => $package,
                                    ]
                                ]
                            ]
                        ];

                        $url = '/ordermgmt/orderstatus/orders/' . $order_detail['OrderNumber'];
                        /*$param = ['append' => '&version=304', 'body' => json_encode($body)];*/

                        $newegg_config = Helper::configurationDetail($merchant_id);

                        $newegg_helper = new Neweggapi($newegg_config['seller_id'], $newegg_config['authorization'], $newegg_config['secret_key']);

                        $param = [
                            'append' => '&version=304',
                            'body' => json_encode($body),
                            'authorization' => $newegg_config['authorization'],
                            'secretKey' => $newegg_config['secret_key'],
                            'url' => $url
                        ];

                        $response = $newegg_helper->putRequest($url, $param);

                        /*$response = Neweggapi::putRequest($url, $param);*/

                        Helper::createLog("---order shipment response from newegg--- " . $response, $path, 'a+');

                        $lastchar = substr($response, strlen($response) - 1);
                        $firstchar = substr($response, 0);
                        if ($firstchar[0] == '[') {
                            $string = substr($response, 0);
                        } else {
                            $string = $response;
                        }
                        if ($lastchar == ']') {
                            $string = substr($string, 0, -1);
                        }
                        $data = json_decode($string, true);

                        if (!isset($data['Code']) && $data['IsSuccess']) {
                            if (!empty($data['PackageProcessingSummary'])) {
                                if (isset($data['PackageProcessingSummary']['SuccessCount']) && ($data['PackageProcessingSummary']['SuccessCount']) > 0) {
                                    $OrderStatus = $data['Result']['OrderStatus'];
                                    try {
                                        $query = "UPDATE `newegg_order_detail` SET `order_status_description`='" . $OrderStatus . "' WHERE `merchant_id` = '" . $merchant_id . "' AND `seller_id` = '" . $model['seller_id'] . "' AND `order_number` = '" . $model['order_number'] . "'";

                                        $update = Data::sqlRecords($query, null, 'update');

                                        Sendmail::shipOrder($emailData['email'], $model['order_number'], $OrderStatus);

                                        $return = ['status' => 'success', 'message' => 'Successfully shipped '];
                                    } catch (Exception $e) {
                                        $return = ['status' => 'error', 'message' => $e->getMessage()];
                                    }
                                } elseif (isset($data['PackageProcessingSummary']['FailCount']) && ($data['PackageProcessingSummary']['FailCount']) > 0) {

                                    $return = ['status' => 'error', 'message' => $data['Result']['Shipment']['PackageList'][0]['ProcessResult']];
                                }
                            }
                        } else {
                            $return = ['status' => 'error', 'message' => $data['Message']];
                        }

                    } else {
                        $return = ['status' => 'error', 'message' => 'Order not Fulfilled from shopify'];
                    }
                } else {
                    if (isset($shipmentDetails['errors'])) {
                        $return = ['status' => 'error', 'message' => $shipmentDetails['errors']];
                    }
                }

            } catch (Exception $e) {
                $return = ['status' => 'error', 'message' => $e->getMessage()];
            }

        } else {
            $return = ['status' => 'error', 'message' => 'Id not defined'];
        }

        if (isset($return['message'])) {
            $message = $return['message'];
//            $message = implode(',',$return['message']);
            /*Data::sqlRecords("UPDATE `newegg_order_detail` SET `order_error` = '".$message."' WHERE `merchant_id` = '".$merchant_id."' AND `id`='".$id."' ","update");*/

            $query = "UPDATE `newegg_order_detail` SET `order_error`='" . $message . "' WHERE `merchant_id` = '" . $merchant_id . "' AND `id` ='" . $id . "'";


            $update = Data::sqlRecords($query, null, 'update');
        }

        Sendmail::failedshipment($emailData['email'], $id, $return['message']);

        return $return;
    }

    public static function curlprocessfororder()
    {
        die('dfg');
        $errorMessage = "";
        $shopname = "";
        $type = 'CURLShipOrder';
        $return = [];
        $action = '2';

        $orderData = NeweggOrderDetail::find()->where(['shopify_order_id' => $data['id'], 'shopify_order_name' => $data['name']])->one();
        $id = $orderData->id;
        $merchant_id = $orderData->merchant_id;

//        $shop = Data::sqlRecords("SELECT `shop_url` FROM `newegg_shop_detail` WHERE `merchant_id`='".$merchant_id ."'",'one');

        $path = 'order/ship/webhook/' . $merchant_id . '/' . date('d-m-Y') . '/'.$id.'/' . time();
        Helper::createLog("order shipment in newegg start".PHP_EOL.'---- WEBHOOK DATA ---'.json_encode($data),$path,'a+');

        $order_number = $orderData->order_number;
        $newegg_order_data = json_decode($orderData->order_data, true);
        $seller_id = $orderData->seller_id;

        if ($orderData) {

            Helper::createLog("---Newegg Order Data---" . PHP_EOL . json_encode($newegg_order_data), $path, 'a+');

            $neweggConfig = [];
            $neweggConfig = Data::sqlRecords("SELECT `seller_id`,`authorization`,`secret_key` FROM `newegg_configuration` WHERE merchant_id='" . $merchant_id . "'", 'one');
            if ($neweggConfig) {
                define("SELLER_ID", $neweggConfig['seller_id']);
                define("AUTHORIZATION", $neweggConfig['authorization']);
                define("SECRET_KEY", $neweggConfig['secret_key']);
            } else {
                return false;
            }

            $newegg_helper = new Neweggapi($neweggConfig['seller_id'], $neweggConfig['authorization'], $neweggConfig['secret_key']);

            if ($orderData->order_status_description == "Unshipped") {

                Helper::createLog("---Order Status in DB :---" . PHP_EOL . $orderData->order_status_description . PHP_EOL, $path, 'a+');

                try {
                    $data['timestamp'] = date("d-m-Y H:i:s");

                    if (isset($data['fulfillments']) && !empty($data['fulfillments'])) {

                        $package = [];

                        foreach ($data['fulfillments'] as $fulfillments) {

                            $Item = [];
                            if (!empty($data['fulfillments'])) {

                                foreach ($fulfillments['line_items'] as $value) {

                                    if ($value['fulfillment_status'] == 'fulfilled' && $value['fulfillable_quantity'] == 0) {


                                        $Item[] = [
                                            'SellerPartNumber' => $value['sku'],
                                            'ShippedQty' => $value['quantity']
                                        ];


                                    } elseif ($value['fulfillment_status'] == 'partial' && $value['fulfillable_quantity'] > 0) {

                                        $Unfulfilled_item[] = $value;
                                    }

                                }

                            }
                            /*$tracking_company = ['UPS','UPS MI','FedEX','DHL','USPS'];
                            if(!in_array($fulfillments['tracking_company'],$tracking_company))
                            {
                                $fulfillments['tracking_company'] = 'Other';
                            }*/
                            if (!empty($Item)) {
                                $package[] = [
                                    'TrackingNumber' => $fulfillments['tracking_number'],
                                    'ShipCarrier' => $fulfillments['tracking_company'],
                                    'ShipService' => $fulfillments['tracking_company'],
                                    'ItemList' => [
                                        'Item' => $Item
                                    ],
                                ];
                            }

                        }
                        if (!empty($Unfulfilled_item)) {
                            $unfulfilled_array = addslashes(json_encode($Unfulfilled_item));

                            $query = "UPDATE `newegg_order_detail` SET `unfulfilled_array`='" . $unfulfilled_array . "' WHERE `merchant_id` = '" . $merchant_id . "' AND `seller_id` ='" . $seller_id . "' AND `order_number` = '" . $order_number . "'";


                            $update = Data::sqlRecords($query, null, 'update');
                        }

                        $body = ['Action' => $action,
                            'Value' => [
                                'Shipment' => [
                                    'Header' => [
                                        'SellerID' => $seller_id,
                                        'SONumber' => $order_number
                                    ],
                                    'PackageList' => [
                                        'Package' => $package,
                                    ]
                                ]
                            ]
                        ];

                        $url = '/ordermgmt/orderstatus/orders/' . $order_number;
                        /*$param = ['append' => '&version=304', 'body' => json_encode($body)];*/

                        $newegg_config = Helper::configurationDetail($merchant_id);

                        $newegg_helper = new Neweggapi($newegg_config['seller_id'], $newegg_config['authorization'], $newegg_config['secret_key']);

                        $param = [
                            'append' => '&version=304',
                            'body' => json_encode($body),
                            'authorization' => $newegg_config['authorization'],
                            'secretKey' => $newegg_config['secret_key'],
                            'url' => $url
                        ];

                        /*$response = $newegg_helper->putRequest($url, $param);*/
                        /*$response = Neweggapi::putRequest($url, $param);*/

                        $response = $newegg_helper->putRequest($url, $param);

                        Helper::createLog("---newegg order shipment response---" . PHP_EOL . $response . PHP_EOL, $path, 'a+');

                        $lastchar = substr($response, strlen($response) - 1);
                        $firstchar = substr($response, 0);
                        if ($firstchar[0] == '[') {
                            $string = substr($response, 0);
                        } else {
                            $string = $response;
                        }
                        if ($lastchar == ']') {
                            $string = substr($string, 0, -1);
                        }
                        $data = json_decode($string, true);


                        if (!isset($data['Code']) && $data['IsSuccess']) {
                            if (!empty($data['PackageProcessingSummary'])) {
                                if (isset($data['PackageProcessingSummary']['SuccessCount']) && ($data['PackageProcessingSummary']['SuccessCount']) > 0) {
                                    $OrderStatus = $data['Result']['OrderStatus'];
                                    try {
                                        $query = "UPDATE `newegg_order_detail` SET `order_status_description`='" . $OrderStatus . "' WHERE `merchant_id` = '" . $merchant_id . "' AND `seller_id` = '" . $seller_id . "' AND `order_number` = '" . $order_number . "'";

                                        $update = Data::sqlRecords($query, null, 'update');
                                        $return = ['status' => 'success', 'message' => 'Successfully shipped '];
                                    } catch (Exception $e) {
                                        $return = ['status' => 'error', 'message' => $e->getMessage()];
                                    }
                                } elseif (isset($data['PackageProcessingSummary']['FailCount']) && ($data['PackageProcessingSummary']['FailCount']) > 0) {

                                    $return = ['status' => 'error', 'message' => $data['Result']['Shipment']['PackageList'][0]['ProcessResult']];
                                }
                            }
                        } else {
                            $return = ['status' => 'error', 'message' => $data['Message']];
                        }
                    } else {
                        $errorMessage .= 'Order not Fulfilled from shopify';
                    }

                } catch (ShopifyApiException $e) {
                    $errorMessage .= $shopname . "[" . date('d-m-Y H:i:s') . "]\n" . "Error in shopify api" . $e->getMessage() . "\n";
                    Helper::createLog("---error---" . PHP_EOL . $errorMessage . PHP_EOL, $path, 'a+');

                    return;
                } catch (ShopifyCurlException $e) {
                    $errorMessage .= $shopname . "[" . date('d-m-Y H:i:s') . "]\n" . "Error in shopify api" . $e->getMessage() . "\n";
                    Helper::createLog("---error---" . PHP_EOL . $errorMessage . PHP_EOL, $path, 'a+');

                    return;
                } catch (Exception $e) {
                    $errorMessage .= $shopname . "[" . date('d-m-Y H:i:s') . "]\n" . "Error exception" . $e->getMessage() . "\n";
                    Helper::createLog("---error---" . PHP_EOL . $errorMessage . PHP_EOL, $path, 'a+');

                    return;
                }

            }

        } else {
            return;
        }
    }

    public static function courtesyrefund($id)
    {
        $type = 'courtesyrefund';
        $return = [];
        $merchant_id = MERCHANT_ID;
        $path = '/order/courtesyrefund/' . $merchant_id . '/' . date('d-m-Y') . '/' . time();
        if ($id) {
            try {
                $model = Data::sqlRecords("SELECT * FROM `newegg_courtesyrefund_detail` WHERE `merchant_id`='" . $merchant_id . "' AND `id`='" . $id . "'", 'one');

                if (!empty($model)) {

                    $body = array(
                        "OperationType" => "GetOrderInfoRequest",
                        "RequestBody" => array(
                            "IssueCourtesyRefund" => array(
                                "SourceSONumber" => $model['order_number'],
                                "RefundReason" => $model['reason'],
                                "TotalRefundAmount" => $model['refund_amount'],
                                "NoteToCustomer" => $model['note_to_customer']
                            ),
                        ),
                    );
                    Helper::createLog("---order courtesyrefund request send--- " . PHP_EOL . json_encode($body) . PHP_EOL, $path, 'a+');

                    $url = '/servicemgmt/courtesyrefund/new';
                    $param = ['body' => json_encode($body)];
                    $response = Cronrequest::postRequest($url, $param);
                    Helper::createLog("---order courtesyrefund response from newegg--- " . PHP_EOL . $response . PHP_EOL, $path, 'a+');

                    $data = json_decode($response, true);
                    $return = ['status' => 'success', 'message' => $data];
                } else {

                    $return = ['status' => 'error', 'message' => 'order data not found'];
                    Helper::createLog("---order courtesyrefund response from newegg--- " . PHP_EOL . json_encode($return) . PHP_EOL, $path, 'a+');

                }


            } catch (Exception $e) {
                $return = ['status' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            $return = ['status' => 'error', 'message' => 'Id not found'];
        }
        return $return;
    }

    public static function getcourtesyrefund()
    {
        $type = 'GetCourtesyRefund';
        $return = [];
        $merchant_id = MERCHANT_ID;
        try {

            $body = [
                "OperationType" => "GetCourtesyRefundInfo",
                "RequestBody" => [
                    "PageInfo" => [
                        "PageIndex" => '1',
                        "PageSize" => '100'
                    ],
                    "KeywordsType" => '0',
                    "Status" => '0',
                ],
            ];

            $url = '/servicemgmt/courtesyrefund/info';

            $newegg_config = Helper::configurationDetail($merchant_id);

            $newegg_helper = new Neweggapi($newegg_config['seller_id'], $newegg_config['authorization'], $newegg_config['secret_key']);

            $param = [
                'body' => json_encode($body),
                'authorization' => $newegg_config['authorization'],
                'secretKey' => $newegg_config['secret_key'],
                'url' => $url
            ];

            $response = $newegg_helper->putRequest($url, $param);

            $path = '/order/getcourtesyrefundorders/' . $merchant_id . '/' . date('d-m-Y') . '/' . time();

            $data = json_decode($response, true);

            if (isset($data['NeweggAPIResponse']) and $data['NeweggAPIResponse']['IsSuccess']) {
                $seller_id = $data['NeweggAPIResponse']['SellerID'];
                if (!empty($data['NeweggAPIResponse']['ResponseBody']['CourtesyRefundInfoList'])) {
                    foreach ($data['NeweggAPIResponse']['ResponseBody']['CourtesyRefundInfoList'] as $model) {

                        Helper::createLog("order courtesy refund response" . json_encode($model), $path, 'a+');

                        $query = Data::sqlRecords("SELECT * FROM `newegg_courtesyrefund_detail` WHERE `merchant_id`= '" . $merchant_id . "' AND `seller_id` = '" . $seller_id . "' AND `order_number` = '" . $model['SONumber'] . "'", 'one');

                        if (!empty($query)) {

                            $query2 = "UPDATE `newegg_courtesyrefund_detail` SET `courtesy_refund_id`='" . $model['CourtesyRefundID'] . "' , `order_number`='" . $model['SONumber'] . "' , `order_amount` = '" . $model['SOAmount'] . "',`invoice_number`= '" . $model['InvoiceNumber'] . "', `refund_amount`= '" . $model['RefundAmount'] . "', `reason`='" . $model['Reason'] . "', `status`='" . $model['Status'] . "', `is_newegg_refund` = '" . $model['IsNeweggRefund'] . "', `in_user_name`='" . $model['InUserName'] . "',`in_date`='" . $model['InDate'] . "',`	edit_user_name`='" . $model['EditUserName'] . "',`edit_date`='" . $model['EditDate'] . "' WHERE `merchant_id`='" . $merchant_id . "' AND `seller_id`='" . $seller_id . "' AND `order_number`= '" . $model['SONumber'] . "'";
                            Data::sqlRecords($query2, null, 'update');

                        } else {

                            $query1 = "INSERT into `newegg_courtesyrefund_detail` (`merchant_id`,`seller_id`,`courtesy_refund_id`,`order_number`,`order_amount`,`invoice_number`,`refund_amount`,`reason`,`status`,`is_newegg_refund`,`in_user_name`,`in_date`,`edit_user_name`,`edit_date`) VALUES ('" . $merchant_id . "','" . $seller_id . "','" . $model['CourtesyRefundID'] . "','" . $model['SONumber'] . "','" . $model['SOAmount'] . "','" . $model['InvoiceNumber'] . "','" . $model['RefundAmount'] . "', '" . $model['Reason'] . "','" . $model['Status'] . "','" . $model['IsNeweggRefund'] . "','" . $model['InUserName'] . "','" . $model['InDate'] . "','" . $model['EditUserName'] . "','" . $model['EditDate'] . "')";

                            Data::sqlRecords($query1, null, 'insert');
                        }

                    }

                } else {

                    $return = ['status' => 'error', 'message' => 'Id not found'];
                }
            }
        } catch (Exception $e) {
            $return = ['status' => 'error', 'message' => $e->getMessage()];
        }

        return $return;
    }

    /*public function validateCarrier()
    {

    }*/

    /*    public static function orderlog($postData, $type, $merchant_id, $OrderNumber = '')
        {
            if (!empty($OrderNumber)) {
                $file_dir = dirname(\Yii::getAlias('@webroot')) . '/var/order/' . $type . '/' . $merchant_id . '/' . $OrderNumber . '';
            } else {
                $file_dir = dirname(\Yii::getAlias('@webroot')) . '/var/order/' . $type . '/' . $merchant_id . '';
            }

            if (!file_exists($file_dir)) {
                mkdir($file_dir, 0775, true);
            }
            $filenameOrig = "";
            $filenameOrig = $file_dir . '/' . time() . '.json';
            $fileOrig = "";
            $fileOrig = fopen($filenameOrig, 'w+');
            fwrite($fileOrig, $postData);
            fclose($fileOrig);
        }*/

}