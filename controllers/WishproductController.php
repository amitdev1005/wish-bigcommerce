<?php
namespace frontend\modules\wishmarketplace\controllers;

use Yii;
use frontend\modules\wishmarketplace\models\WishProduct;
use frontend\modules\wishmarketplace\models\WishProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\modules\wishmarketplace\components\Jetappdetails;
use frontend\modules\wishmarketplace\components\AttributeMap;
use frontend\modules\wishmarketplace\components\Jetproductinfo;
use frontend\modules\wishmarketplace\models\JetProduct;
use frontend\modules\wishmarketplace\components\Wishapi;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\components\BigcommerceClientHelper;
use frontend\modules\wishmarketplace\models\WishProductVariants;
use frontend\modules\wishmarketplace\models\JetProductVariants;
use frontend\modules\wishmarketplace\components\WishCategory;
use frontend\modules\wishmarketplace\components\Bigcomapi;
use frontend\modules\wishmarketplace\components\WishPromoStatus;
use frontend\modules\wishmarketplace\models\WishExtensionDetail;
use yii\web\UploadedFile;
use frontend\modules\wishmarketplace\components\Appinstall;
/**
 * WalmartproductController implements the CRUD actions for WalmartProduct model.
 */
class WishproductController extends WishmainController
{
    protected $connection;
    protected $wishConfig;
    protected $bigcom;
    public function behaviors()
    {
        return [
        'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
        'delete' => ['post'],
        ],
        ],
        ];
    }

    public function beforeAction ($action)
    {
        if(parent::beforeAction($action))
        {
            //$this->wishHelper = new Wishapi(API_USER,API_PASSWORD,AUTH_KEY);
           $this->wishConfig = new Wishapi(CLIENT_ID,CLIENT_SECRET_KEY,CODE,ACCESS_TOKEN);

           return true;
       }
   }

    /**
     * Lists all WalmartProduct models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        }
        else
        {
            $merchant_id=MERCHANT_ID;
            $notAvaialble=false;
            $this->actionDuplicateproductsdelete();
            $searchModel = new WishProductSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $proCount = Data::sqlRecords("SELECT count(*) as 'pro_count' FROM `jet_product` where merchant_id='".$merchant_id."' LIMIT 0,1","one","select");
            $walCount = Data::sqlRecords("SELECT count(*) as 'wal_count' FROM `wish_product` where merchant_id='".$merchant_id."' LIMIT 0,1","one","select");
            if(isset($proCount['pro_count'],$walCount['wal_count']) && $proCount['pro_count']>$walCount['wal_count'])
            {
                $notAvaialble=true;
            }
           /* if($notAvaialble)
            {
                Data::importWalmartProduct($merchant_id);
            }*/
            // print_r($searchModel);die();
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                ]);
        }
    }
    
    /**
     * Displays a single WalmartProduct model.
     * @param integer $id
     * @return mixed
     */
    
    public function actionView($id)
    {
        if (Yii::$app->user->isGuest) {
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            ]);
    }

    /**
     * Creates a new WalmartProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        }
        $model = new WishProduct();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                ]);
        }
    }

    /**
     * Updates an existing WalmartProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                ]);
        }
    }

    /**
     * Deletes an existing WalmartProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the WalmartProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WalmartProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WishProduct::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * import product from jet product to walmart table
     * configure walmart table
     */  
    
    public function actionImportwalmart()
    {
        if (Yii::$app->user->isGuest) {
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        }
        $model=JetProduct::find()->select('id,type')->where(['merchant_id'=>MERCHANT_ID])->all();
        foreach ($model as $value)
        {
            $walmartModel=WishProduct::find()->where(['product_id'=>$value['id']])->one();
            if(!$walmartModel)
            {
                $modelW=new WishProduct();
                $modelW->product_id=$value['id'];
                $modelW->merchant_id=MERCHANT_ID;
                $modelW->save(false);
            }
            if($type='variants')
            {
                $modelVar=JetProductVariants::find()->select('option_id')->where(['product_id'=>$value['id']])->all();
                foreach ($modelVar as $val)
                {
                    $walmartModelVar=WishProductVariants::find()->where(['option_id'=>$val['option_id']])->one();
                    if(!$walmartModelVar)
                    {
                        $modelvar=new WishProductVariants();
                        $modelvar->option_id=$val['option_id'];
                        $modelvar->merchant_id=MERCHANT_ID;
                        $modelvar->save(false);
                    }
                }
            }
        }
    }
    
     /**
     * Product edit for simple and variant products
     *
     */
    public function actionEditdata()
    {
      $this->layout='main2';
      $id=trim(Yii::$app->request->post('id'));
      $merchant_id=trim(Yii::$app->request->post('merchant_id'));
      $merchant_id = MERCHANT_ID;
      $model = WishProduct::find()->joinWith('jet_product')->where(['wish_product.id'=>$id])->andWhere(['wish_product.merchant_id'=>$merchant_id])->one();
      $Category=[];
      $category_path="";
      $query="SELECT category_name,category_id,category_path FROM `wish_category_map` WHERE category_id='".$model->category."' LIMIT 1";
      $Category = Data::sqlRecords($query,"one","select");
      $parent_id="";
      if(is_array($Category) && count($Category)>0)
      {
        $category__name = $Category['category_name'];
        $category_path = $Category['category_path'];
        $temp_cname = str_replace($category__name,'', $category_path);
        $category_path = $temp_cname.'->'.$category__name;
        }
        $attributes=[];
        $requiredAttrValues=[];
        $optionalAttrValues=[];
        $required=[];
        $common_required_attributes = array();
        $category_path = $Category['category_path'];
        $parent = explode(',',$category_path);
        // $result = WishCategory::getCategoryVariantAttributes($Category['category_id']);
        $result = $wishAttributes = AttributeMap::getWishCategoryAttributes($Category['category_name'],$parent)?:[];
        if(isset($result['common_attributes'])) {
            $common_attributes = $result['common_attributes'];
            $common_required_attributes = $common_attributes;
            unset($result['common_attributes']);
        }
        $attribute_values = [];
        if(isset($result['attribute_values'])) {
            $attribute_values = $result['attribute_values'];
            unset($result['attribute_values']);
        }
        if(isset($result['parent_id'])) {
            $parent_id = $result['parent_id'];
            unset($result['parent_id']);
        }
        $required = [];
        if(isset($result['required_attributes'])) {
            $required = $result['required_attributes'];
            unset($result['required_attributes']);
        }
        $unitAttributes = [];
        if(isset($result['unit_attributes'])) {
            $unitAttributes = $result['unit_attributes'];
            unset($result['unit_attributes']);
        }
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $val) {
                    $index = implode('->', $val);
                    if (!isset($result[$index])) {
                        if (count($unitAttributes)) {
                            foreach ($unitAttributes as $unitAttributeKey => $unitAttributeVal) {
                                if (is_array($unitAttributeVal)) {
                                    $diff = array_diff($unitAttributeVal, $val);
                                    if (count($diff) != 0) {
                                        $common_required_attributes[] = $attributes[$key];
                                        unset($attributes[$key]);
                                        break;
                                    }
                                } else {
                                    $common_required_attributes[] = $attributes[$key];
                                    unset($attributes[$key]);
                                    break;
                                }
                            }
                        } else {
                            $common_required_attributes[] = $attributes[$key];
                            unset($attributes[$key]);
                        }
                    }
                }
            } else {
                if (!isset($result[$value])) {
                    if (count($unitAttributes)) {
                        foreach ($unitAttributes as $unitAttributeKey => $unitAttributeVal) {
                            if (!is_array($unitAttributeVal)) {
                                $diff = array_diff($unitAttributeVal, $val);
                                if ($unitAttributeVal != $value) {
                                    $common_required_attributes[] = $attributes[$key];
                                    unset($attributes[$key]);
                                    break;
                                }
                            } else {
                                $common_required_attributes[] = $attributes[$key];
                                unset($attributes[$key]);
                                break;
                            }
                        }
                    } else {
                        $common_required_attributes[] = $attributes[$key];
                        unset($attributes[$key]);
                    }
                }
            }
        }
        $_attributes = [];
        foreach ($result as $result_key => $result_value) {
            if(is_array($result_value))
            {
                $key = reset($result_value);
                $_attributes[] = [$key=>$result_value];
            }
            else
                $_attributes[] = $result_value;
        }
        /* code by himanshu start */
        if(count($requiredAttrValues))
        {
            $requiredAttrValues_copy = $requiredAttrValues;
            $requiredAttrValues = [];
            foreach($requiredAttrValues_copy as $attr_val)
            {
                if(is_array($attr_val) && count($attr_val))
                {
                   $attr_val_key = key($attr_val);
                   $attr_val_value = reset($attr_val);
                   $requiredAttrValues[$attr_val_key] = $attr_val_value;
                }
            }
            $requiredAttrValues = array_merge($requiredAttrValues,$attribute_values);
        }
        else
        {
            foreach ($attribute_values as $attr_code=>$attribute_value) {
                $requiredAttrValues[$attr_code] = $attribute_value;
            }
        }
        $session = Yii::$app->session;
        $productData = [
        'model'=>$model,
        'category_path'=>$category_path,
        'attributes'=>$_attributes,
        'optional_attr'=>[]/*$optional_attr*/,
        'requiredAttrValues'=>$requiredAttrValues,
        'optionalAttrValues'=>$optionalAttrValues,
        'common_required_attributes'=>$common_required_attributes,
        'required' => $required,
        'unit_attributes' => $unitAttributes,
        'category_data' => $Category
        ];
        $session_key = 'product'.$id;
        $session->set($session_key, $productData);
        $session->close();
        /* code by himanshu end */
        $html = $this->render('editdata',array('id'=>$id,'model'=>$model,'category_path'=>$category_path,'attributes'=>$_attributes,'optional_attr'=>[]/*$optional_attr*/,'requiredAttrValues'=>$requiredAttrValues,'optionalAttrValues'=>$optionalAttrValues, 'common_required_attributes'=>$common_required_attributes, 'required'=>$required,'unit_attributes'=>$unitAttributes),true);
        unset($connection);
        unset($attributes);
        return $html;
    }

    public function actionUpdateajaxdesc($id){
       if (Yii::$app->user->isGuest) {
        return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
    }
    $connection = Yii::$app->getDb();
    $model = WishProduct::find()->joinWith('jet_product')->where(['wish_product.product_id'=>$id])->andWhere(['wish_product.merchant_id'=>MERCHANT_ID])->one();
    $data=array();
    $sku=$model->jet_product->sku;
    $merchant_id = $model->merchant_id;
    $bigproduct_id=$model->product_id;

    if($_POST['JetProduct']['product_description']){

        $sql="UPDATE `wish_product` SET short_description='".addslashes($_POST['JetProduct']['product_description'])."' where merchant_id='".$merchant_id."' and product_id='".$id."'";
        $model1 = $connection->createCommand($sql)->execute();

        $return_status['success']="Product information has been saved successfully..";
        return json_encode($return_status);

    }   
    }


public function actionSaveDescription()
{
    $description = Yii::$app->request->post('description', false);
    $product_id = Yii::$app->request->post('product_id', false);
    if ($product_id && $description && is_numeric($product_id)) {
        $maxLength = Data::MAX_LENGTH_LONG_DESCRIPTION;
            //htmlspecialchars($description,ENT_XHTML);
        $length = strlen($description);
        if ($length > $maxLength) {
            return json_encode(['error' => true, 'message' => 'Description Should be less than 4000 characters.']);
        } else {
            $query = "UPDATE `wish_product` SET `long_description`='" . addslashes($description) . "' WHERE `product_id`='" . $product_id . "'";
            Data::sqlRecords($query, null, 'update');

            return json_encode(['success' => true, 'message' => 'Description saved successfully.']);
        }
    } else {
        return json_encode(['error' => true, 'message' => 'Please Provide Valid Data.']);
    }
}

    /**
     * Product ajax update
     * get product data and save records in database
     * @param integer $id 
     */
    public function actionUpdateajax($id)
    {
        // print_r(Yii::$app->request->post());die();
        if (Yii::$app->user->isGuest) {
            return \Yii::$app->getResponse()->redirect(Data::getUrl('site/login'));
        }
        $connection = Yii::$app->getDb();
        $model = WishProduct::find()->joinWith('jet_product')->where(['wish_product.product_id'=>$id])->andWhere(['wish_product.merchant_id'=>MERCHANT_ID])->one();
        $data = array();
        $sku = $model->jet_product->sku;
        $merchant_id = $model->merchant_id;

        //print_r(Yii::$app->request->post());die;

        if (Yii::$app->request->post()) {
            /*-------------------newly added on 1 April starts----------------------------------*/
            $product_barcode = "";
            $product_sku = "";
            $product_id = "";
            $product_upc = "";
            $product_asin = "";
            $product_short = "";
            $product_self = "";
            $product_vendor = "";
            $return_status = [];
            $product_error = [];

            $product_id = $model->product_id;

            $variant_id = $model->jet_product->variant_id;
            $exceptions = isset($_POST['exceptions']) ? json_encode($_POST['exceptions']) : json_encode([]);
            $product_sku = $_POST['JetProduct']['sku'];
            $product_upc = trim($_POST['JetProduct']['upc']);
            //$product_mpn=trim($_POST['JetProduct']['mpn']);
            $product_vendor = trim($_POST['JetProduct']['brand']);
            $product_short = trim($_POST['JetProduct']['short_description']);
            $product_self = trim($_POST['JetProduct']['self_description']);
            $category = "";
            $category = trim($_POST['JetProduct']['fulfillment_node']);
            if ($product_vendor == "") {
                $return_status['error'] = "Brand is required field.";
                return json_encode($return_status);
            }

            /*if($product_tax != '' && count($product_tax) != 7 )
            {
                $product_error['invalid_taxcode'][] = $product_sku;
            }*/

            if ($product_barcode == "") {
                $product_barcode = Jetproductinfo::checkUpcType($product_upc);
            }

            /* Code By Himanshu Start */
            if (Yii::$app->request->post('common_attributes', false)) {
                $common_attr_data = [];
                foreach (Yii::$app->request->post('common_attributes') as $key => $value) {
                    $value = trim($value);
                    if (!empty($value))
                        $common_attr_data[$key] = $value;
                }

                if (count($common_attr_data))
                    $common_attr = json_encode($common_attr_data);
                else
                    $common_attr = null;

                $model->common_attributes = $common_attr;
            } else {
                $model->common_attributes = null;
            }


            $model->shipping_exceptions = $exceptions;
            /*if (isset($_POST['sku_override'])) {
                $sku_override = Yii::$app->request->post('sku_override');
                $model->sku_override = $sku_override;
            } else {
                $model->sku_override = 0;
            }

            if (isset($_POST['product_id_override'])) {
                $product_id_override = Yii::$app->request->post('product_id_override');
                $model->product_id_override = $product_id_override;
            } else {
                $model->product_id_override = 0;
            }

            if (isset($_POST['fulfillment_lag_time'])) {
                $lag_time = Yii::$app->request->post('fulfillment_lag_time');
                $model->fulfillment_lag_time = $lag_time;
            } else {
                $model->fulfillment_lag_time = 1;
            }*/
            /* Code By Himanshu End */

            if (Yii::$app->request->post('product-type') == 'variants') {

                $walmart_attr = array();
                $options = array();
                $new_options = array();
                $pro_attr = array();
                $walmart_attributes = array();
                $attributes_of_jet = array();
                $other_vari_opt = array();
                $common_attr = "";
                if (Yii::$app->request->post('jet_attributes')) {
                    $walmart_attributes = Yii::$app->request->post('jet_attributes');
                }

                if (Yii::$app->request->post('attributes_of_jet')) {

                }
                if (Yii::$app->request->post('jet_varients_opt')) {
                    //$product_error = [];
                    $other_vari_opt = Yii::$app->request->post('jet_varients_opt');
                    $er_msg = "";
                    $chek_flag = false;
                    if (is_array($other_vari_opt) && count($other_vari_opt) > 0) {

                        foreach ($other_vari_opt as $k_opt_id => $v_opt) {

                            $option_id = $k_opt_id;
                            $opt_upc = "";
                            $opt_asin = "";
                            $opt_mpn = "";
                            $option_sku = "";
                            $er_msg1 = "";
                            $opt_upc = trim($v_opt['upc']);
                            $option_sku = $v_opt['optionsku'];
                            $opt_barcode = "";
                            /*-------newly added on 1 April starts------------*/
                            if ($opt_barcode == "") {
                                $opt_barcode = Jetproductinfo::checkUpcType($opt_upc);
                            }
                            $upc_success_flag = true;
                            $mpn_success_flag = true;
                            $invalid_asin = false;
                            $invalid_upc = false;
                            $invalid_mpn = false;
                            $upc_error_msg = "";
                            $asin_success_flag = true;
                            $asin_error_msg = "";

                            /*
                            * validate upc
                            */
                            $category_id = trim($_POST['category_id']);
                            $skipCategory = ['Jewelry', 'Rings'];
                            if (!empty($category_id) && !in_array($category_id, $skipCategory)) {
                                if (isset($opt_upc) && !empty($opt_upc)) {
                                    $var = Data::validateUpc($opt_upc);
                                    if ($var == true) {
                                        $invalid_upc = false;
                                    } else {
                                        $invalid_upc = true;
                                    }
                                }

                                /*if (strlen($opt_upc) > 0) {
                                    list($upc_success_flag, $upc_error_msg) = Jetproductinfo::checkProductOptionBarcodeOnUpdate($other_vari_opt, $v_opt, $k_opt_id, $opt_barcode, $product_barcode, $product_upc, $product_id, $product_sku, $connection);
                                }*/
                                $validate = Jetproductinfo::validateProductBarcode($opt_upc, $option_id, $merchant_id);
                                // print_r($validate);die();
                                if ($opt_upc == "" || !is_numeric($opt_upc) || (is_numeric($opt_upc) && !$opt_barcode) || (is_numeric($opt_upc) && $opt_barcode && !$validate)) {
                                    $invalid_upc = true;
                                }

                                if ($invalid_upc) {
                                    $chek_flag = true;
                                    unset($other_vari_opt[$option_id]['upc']);
                                    $product_error['invalid_asin'][] = $option_sku;
                                }
                            }

                        }
                    }
                    /*if (count($product_error) > 0) {
                        $error = "";

                        if (isset($product_error['invalid_asin']) && count($product_error['invalid_asin']) > 0) {
                            $error .= "Invalid/Missing Barcode for sku(s): " . implode(', ', $product_error['invalid_asin']) . "<br>";
                        }
                        $return_status['error'] = $error;

                        unset($error);
                        unset($product_error);
                        //return json_encode($return_status);
                    }*/
                } else {
                    $upc_success_flag = false;
                    $asin_success_flag = false;
                    $mpn_success_flag = false;
                    $invalid_upc = false;
                    $invalid_asin = false;
                    $invalid_mpn = false;
                    $chek_flag = false;
                    $er_msg = "";
                    $type = "";
                    $type = Jetproductinfo::checkUpcType($product_upc);

                    /*
                     * validate upc
                     */
                    $category_id = trim($_POST['category_id']);
                    $skipCategory = ['Jewelry', 'Rings'];
                    if (!empty($category_id) && !in_array($category_id, $skipCategory)) {

                        if (isset($product_upc) && !empty($product_upc)) {
                            $var = Data::validateUpc($product_upc);
                            if ($var == true) {
                                $invalid_upc = false;
                            } else {
                                $invalid_upc = true;
                            }
                        }

                        if (strlen($product_upc) > 0) {
                            $upc_success_flag = Jetproductinfo::checkUpcVariantSimple($product_upc, $product_id, $product_sku, $connection);
                        }
                        if ($product_upc == "" || !is_numeric($product_upc) || (is_numeric($product_upc) && $type = "") || (is_numeric($product_upc) && $type && $upc_success_flag)) {
                            $invalid_upc = true;
                        }

                        if ($invalid_upc) {
                            $chek_flag = true;
                            $er_msg .= "Invalid/Missing Barcode, must be unique" . "<br>";
                        }
                    }
                    if ($chek_flag) {
                        $return_status['error'] = $er_msg;
                        unset($er_msg);
                        //return json_encode($return_status);
                    }
                    /*-------------check asin and upc for variant-simple here ends----------*/
                }
                if ($walmart_attributes) {
                    foreach ($walmart_attributes as $attr_id => $value_arr) {
                        $flag = false;
                        if (is_array($value_arr) && count($value_arr) > 0) {
                            $walmart_attr_id = "";
                            foreach ($value_arr as $val_key => $chd_arr) {
                                if ($val_key == "jet_attr_id" && trim($chd_arr) == "") {
                                    $flag = true;
                                    foreach ($value_arr as $v_key => $c_ar) {
                                        if ($v_key == "jet_attr_id") {
                                            continue;
                                        } elseif ($v_key == "jet_attr_name") {
                                            continue;
                                        } elseif (is_array($c_ar)) {
                                            $new_options[trim($v_key)][trim($attr_id)] = trim($c_ar['value']);
                                        }
                                    }
                                    break;
                                } else {
                                    if ($val_key == "jet_attr_id") {
                                        $str_id = "";
                                        $str_id_arr = array();
                                        $str_id = trim($chd_arr);
                                        $str_id_arr = explode(',', $str_id);
                                        $walmart_attr_id = trim($str_id_arr[0]);
                                    } elseif ($val_key == "jet_attr_name") {
                                        $unit = "";
                                        $s_unit = [];
                                        if (count($attributes_of_jet) > 0 && array_key_exists($walmart_attr_id, $attributes_of_jet)) {
                                            $unit = $attributes_of_jet[$walmart_attr_id]['unit'];
                                        }
                                        $s_unit[] = trim($walmart_attr_id);
                                        if ($unit != "") {
                                            $s_unit[] = trim($unit);
                                        }
                                        //$s_unit=trim($s_unit);
                                        $pro_attr[trim($chd_arr)] = $s_unit;
                                    } elseif (is_array($chd_arr)) {
                                        //$options[$attr_id]['option_id'][]=trim($val_key);
                                        $options[trim($val_key)][$walmart_attr_id] = trim($chd_arr['value']);
                                        $new_options[trim($val_key)][trim($attr_id)] = trim($chd_arr['value']);
                                    }

                                }
                            }
                        }
                        if ($flag) {
                            //unset($options[$attr_id]);
                            continue;
                        }

                    }
                }
                $walmart_attr = $options;
                //$connection = Yii::$app->getDb();
                $product_id = '';
                $product_id = trim($id);
                if (is_array($walmart_attr) && count($walmart_attr) > 0) {
                    $opt_count = 0;
                    foreach ($walmart_attr as $opt_key => $option_value) {
                        $option_id = "";
                        $option_id = trim($opt_key);
                        $options_save = "";
                        $options_save = json_encode($option_value);
                        //$opt_price="";
                        //$opt_qty="";
                        $opt_upc = "";
                        $opt_asin = "";
                        $opt_mpn = "";
                        $opt_sku = "";
                        if (is_array($other_vari_opt) && count($other_vari_opt) > 0) {
                            //$opt_price=$other_vari_opt[$option_id]['price'];
                            //$opt_qty=$other_vari_opt[$option_id]['qty'];

                            $opt_upc = isset($other_vari_opt[$option_id]['upc']) ? $other_vari_opt[$option_id]['upc'] : '';
                            $opt_sku = $other_vari_opt[$option_id]['optionsku'];
                        }
//                        print_r($opt_upc);
//                        print_r($option_id);
                        $sql = "";
                        $model2 = "";
                        $query = "SELECT `option_id` from `jet_product_variants` WHERE option_id='" . $option_id . "' LIMIT 1";
                        $model2 = Data::sqlRecords($query, "one", "select");
                        if ($model2) {
                            $new_variant_option_1 = "";
                            $new_variant_option_2 = "";
                            $new_variant_option_3 = "";
                            if (is_array($new_options[trim($opt_key)]) && count($new_options[trim($opt_key)]) > 0) {
                                $v_opt_count = 1;
                                foreach ($new_options[trim($opt_key)] as $opts_k => $opts_v) {
                                    if ($v_opt_count == 1) {
                                        $new_variant_option_1 = $opts_v;
                                    }
                                    if ($v_opt_count == 2) {
                                        $new_variant_option_2 = $opts_v;
                                    }
                                    if ($v_opt_count == 3) {
                                        $new_variant_option_3 = $opts_v;
                                    }
                                    $v_opt_count++;
                                }
                            }
                            if (!empty($opt_upc)) {
                                $sql = "UPDATE `jet_product_variants` SET
                                option_unique_id='" . trim($opt_upc) . "',
                                option_qty ='" . $other_vari_opt[$option_id]['walmart_product_inventory'] . "'
                                where option_id='" . $option_id . "'";
                            } else {
                                $sql = "UPDATE `jet_product_variants` SET
                                option_qty ='" . $other_vari_opt[$option_id]['walmart_product_inventory'] . "'
                                where option_id='" . $option_id . "'";
                            }

                            Data::sqlRecords($sql, null, "update");
                            //$connection->createCommand($sql)->execute();
                            $model3 = "";
                            $query = "SELECT `option_id` from `wish_product_variants` WHERE option_id='" . $option_id . "' LIMIT 1";
                            $model3 = Data::sqlRecords($query, "one", "select");
                            if ($model3 !== "") {
                                $sql = "";
                                $sql = "UPDATE `wish_product_variants` SET
                                new_variant_option_1='" . addslashes($new_variant_option_1) . "',
                                new_variant_option_2='" . addslashes($new_variant_option_2) . "',
                                new_variant_option_3='" . addslashes($new_variant_option_3) . "',
                                walmart_option_attributes='" . addslashes($options_save) . "' ,
                                option_prices =" . $other_vari_opt[$option_id]['walmart_product_price'] . "
                                where option_id='" . $option_id . "'";
                                Data::sqlRecords($sql, null, "update");
                            }
                        }

                        if ($option_id == $variant_id) {
                            $model->jet_product->upc = trim($opt_upc);
                            $model->jet_product->qty = trim($other_vari_opt[$option_id]['walmart_product_inventory']);
                            $model->jet_product->brand = trim($product_vendor);
                            $model->product_price = $other_vari_opt[$option_id]['walmart_product_price'];
                        }
                        $opt_count++;
                    }
                } else {
                    if (is_array($other_vari_opt) && count($other_vari_opt) > 0) {
                        $opt_count = 0;
                        foreach ($other_vari_opt as $opt_id => $v_arr) {
                            $model2 = "";
                            $option_id = "";
                            $option_id = trim($opt_id);
                            //$opt_price="";
                            //$opt_qty="";
                            $opt_upc = "";
                            $opt_asin = "";
                            $opt_mpn = "";
                            //$opt_price=$other_vari_opt[$option_id]['price'];
                            //$opt_qty=$other_vari_opt[$option_id]['qty'];
                            $opt_upc = $other_vari_opt[$option_id]['upc'];
                            if ($opt_sku == $product_sku) {
                                //if(trim($opt_upc)!=""){
                                $model->jet_product->upc = trim($opt_upc);
                                $model->jet_product->vendor = trim($product_vendor);
                                //}
                            }
                            $sql = "";
                            $model2 = "";
                            $query = "SELECT `option_id` from `jet_product_variants` WHERE option_id='" . $option_id . "' LIMIT 1";
                            $model2 = Data::sqlRecords($query, "one", "select");
                            if ($model2 !== "") {
                                $sql = "";
                                $sql = "UPDATE `jet_product_variants` SET
                                option_unique_id='" . trim($opt_upc) . "',
                                option_qty ='" . $other_vari_opt[$option_id]['walmart_product_inventory'] . "'
                                where option_id='" . $option_id . "'";
                                //$connection->createCommand($sql)->execute();
                                Data::sqlRecords($sql, null, "update");

                            }
                            $model3 = "";
                            $model3 = $connection->createCommand("SELECT `option_id` from `wish_product_variants` WHERE option_id='" . $option_id . "'")->queryOne();
                            if ($model3 !== "") {
                                $sql = "";
                                $sql = "UPDATE `wish_product_variants` SET
                                walmart_option_attributes='',
                                option_prices =" . $other_vari_opt[$option_id]['walmart_product_price'] . "
                                where option_id='" . $option_id . "'";
                                Data::sqlRecords($sql, null, "update");
                                //$connection->createCommand($sql)->execute();
                            }
                            $opt_count++;
                        }
                    }
                }
                unset($model2);
                unset($sql);
                unset($options_save);
                if (count($pro_attr) == 0)
                    $model->walmart_attributes = '';
                else
                    $model->walmart_attributes = json_encode($pro_attr);
                $model->jet_product->brand = $product_vendor;
                $model->short_description = $product_short;
                $model->self_description = $product_self;
                if (isset($_POST['walmart_product_title'])) {
                    $model->product_title = $_POST['walmart_product_title'];
                }
                $model->jet_product->save(false);
                $model->save(false);
                unset($walmart_attributes);
                unset($other_vari_opt);
                unset($attributes_of_jet);

            } else {

                /*-------------check asin and upc for simple here----------*/
                $upc_success_flag = false;
                $asin_success_flag = false;
                $mpn_success_flag = false;
                $chek_flag = false;
                $invalid_upc = false;
                $er_msg = "";
                $type = "";
                $true=0;
                $product_upc = trim($product_upc);

                /*
                 *  validate upc
                 */
                if (isset($product_upc) && !empty($product_upc)) {
                    $var = Data::validateUpc($product_upc);
                    if ($var == true) {
                        $invalid_upc = false;
                    } else {
                        $invalid_upc = true;
                    }
                }

                $type = Jetproductinfo::checkUpcType($product_upc);
                if (strlen($product_upc) > 0) {
                    $upc_success_flag = Jetproductinfo::checkUpcSimple($product_upc, $product_id, $connection);
                }

                if ($product_upc == "" || !is_numeric($product_upc) || (is_numeric($product_upc) && !$type) || (is_numeric($product_upc) && $type && $upc_success_flag)) {
                    // echo "duplicate upc";
                    $invalid_upc = true;
                }


                if ($invalid_upc) {
                    $chek_flag = true;
                    //echo "duplicate upc/asin";
                    $er_msg .= "Invalid/Missing Barcode , please fill unique barcode" . "<br>";
                }
                //echo $er_msg;die;
                if ($chek_flag) {
                    $return_status['error'] = $er_msg;
                    return json_encode($return_status);
                }
                /*-------------check asin and upc for simple here ends----------*/
                $walmart_attributes1 = "";
                if (Yii::$app->request->post('jet_attributes1')) {
                    $walmart_attributes1 = Yii::$app->request->post('jet_attributes1');
                }
                $walmart_attr = array();
                if ($walmart_attributes1) {
                    foreach ($walmart_attributes1 as $key => $value) {
                        if (count($value) == 1 && $value[0] != '') {
                            $walmart_attr[$key] = array(0 => $value[0]);
                        } elseif (count($value) == 2 && $value[0] != '' && $value[1] != '') {
                            $walmart_attr[$key] = array(0 => $value[0], 1 => $value[1]);
                        }
                    }
                }

                if (count($walmart_attr) == 0)
                    $model->walmart_attributes = '';
                else
                    $model->walmart_attributes = json_encode($walmart_attr);

                if((!is_numeric($_POST['walmart_product_price']) && ($_POST['walmart_product_inventory']!=''))|| ($_POST['walmart_product_price']<0))
                {
                    $return_status['error'][]="Price is not valid";
                    $true=1;
                    //return json_encode($return_status);
                }

                if($_POST['walmart_product_price']==''){
                    $return_status['error'][]="Price is required";
                    $true=1;
                }

                if((!is_numeric($_POST['walmart_product_inventory']) && ($_POST['walmart_product_inventory']!=''))|| ($_POST['walmart_product_inventory']<0)) 
                {
                    $return_status['error'][]="Inventory is not valid.";
                    $true=1;
                    //return json_encode($return_status);
                }
                if($_POST['walmart_product_inventory']==''){
                    $return_status['error'][]="Inventory is required";
                    $true=1;
                }

               /* if(preg_match('^[0-9]+\.[0-9]{2}$',$_POST['walmart_product_inventory'])){
                    $return_status['error'][]="Inventory must be an integer value";
                    $true=1;
                }*/

                if($true){
                    return json_encode($return_status);
                }

                $model->jet_product->upc = $product_upc;
                $model->jet_product->brand = $product_vendor;
                $model->short_description = $product_short;
                $model->self_description = $product_self;
                if (isset($_POST) && !empty($_POST['walmart_product_price']) && !empty($_POST['walmart_product_inventory'])) {

                    $model->product_price = $_POST['walmart_product_price'];
                    $model->product_title = $_POST['walmart_product_title'];
                    $model->jet_product->qty = $_POST['walmart_product_inventory'];
                }
                //$model->category=$category;
                $model->jet_product->save(false);
                $model->save(false);

                // `product_qty`='".$_POST['walmart_product_inventory']."',

                /*if(isset($_POST) && !empty($_POST['walmart_product_price']) && !empty($_POST['walmart_product_inventory'])){
                    $query ="UPDATE `walmart_product` SET `product_price`= '".$_POST['walmart_product_price']."', `product_title`='".addslashes($_POST['walmart_product_title'])."' WHERE `merchant_id`='".$merchant_id."' AND `product_id`='".$_POST['JetProduct']['product_id']."'";

                    Data::sqlRecords($query,null,'update');

                }*/
                unset($walmart_attr);
            }
            if (isset($return_status['error'])) {
                return json_encode($return_status);
            }
            $return_status['success'] = "Product information has been saved successfully..";
            return json_encode($return_status);
        } else {
            //not post successfully
        }
    }
    
    /**
     * Product bulk upload
     * select all product, validate and upload on walmart
     */
    public function actionBulk()
    {
    	$countProducts = "SELECT * FROM `jet_product` WHERE merchant_id='226'";
    	$result = Data::sqlRecords($countProducts,null,"select");
    	foreach ($result as $r){
         $query='UPDATE `wish_product` SET `product_type`="'.addslashes($r['product_type']).'" where product_id="'.$r['bigproduct_id'].'" and `merchant_id`="226"';
         $updateResult =Data::sqlRecords($query,null,"update");
     }


//         if (Yii::$app->user->isGuest) {
//             return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
//         }
//         $action=Yii::$app->request->post('action');
//         echo $action;die;
//         $selection=(array)Yii::$app->request->post('selection');
//         if(count($selection)==0){
//             Yii::$app->session->setFlash('error', "No Product selected...");
//             return $this->redirect(['index']);
//         }
       // $connection=Yii::$app->getDb();

//         if($action=='batch-upload')
//         {
//             $productResponse = $this->walmartHelper->createProductOnWalmart($selection,$this->walmartHelper,MERCHANT_ID,$connection);
//             print_r($productResponse);

//             if(is_array($productResponse) && isset($productResponse['uploadIds'],$productResponse['feedId']) && count($productResponse['uploadIds']>0))
//             {
//                //save product status and data feed
//                $ids=implode(',',$productResponse['uploadIds']);
//                foreach($productResponse['uploadIds'] as $val)
//                {
//                    $query="UPDATE `walmart_product` SET status='Items Processing' where product_id='".$val."'";
//                    Data::sqlRecords($query,null,"update");
//                }
//                $query="INSERT INTO `walmart_product_feed`(`merchant_id`,`feed_id`,`product_ids`)VALUES('".MERCHANT_ID."','".$productResponse['feedId']."','".$ids."')";
//                Data::sqlRecords($query,null,"insert");
//                Yii::$app->session->setFlash('success',"product feed successfully submitted on walmart.");
//             }
//             elseif(isset($productResponse['errors']))
//             {

//                 Yii::$app->session->setFlash('error',json_encode($productResponse['errors']));
//             } 
//             return $this->redirect(['index']);
//         }
 }

    /**
     * Product bulk upload via Ajax
     * select all product, validate and upload on walmart
     */
    public function actionAjaxBulkUpload()
    {
        if (Yii::$app->user->isGuest)
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);

        $action = Yii::$app->request->post('action');
        $selection = (array)Yii::$app->request->post('selection');
        $Productcount = count($selection);
        if($Productcount == 0) {
            Yii::$app->session->setFlash('error', "No Product selected...");
            return $this->redirect(['index']);
        }
        $merchant_id = MERCHANT_ID;
        $session = Yii::$app->session;
        $session->set('merchant_id', $merchant_id);
        //We can only send 10 feeds per hour.
        $size_of_request = 1000;//Number of products to be uploaded at once(in single feed)
        $pages = (int)(ceil($Productcount/$size_of_request));

        $max_feed_allowed_per_hour = 10;
        if($pages > $max_feed_allowed_per_hour)
        {
            $size_of_request = (int)(ceil($Productcount/$max_feed_allowed_per_hour));
            if($size_of_request > 10000) {
                Yii::$app->session->setFlash('error', "MAX Limit Exceeded. Please Unselect Some Products.");
                return $this->redirect(['index']);
            }
            $pages = (int)(ceil($Productcount/$size_of_request));
        }

        if($action=='batch-upload')
        {
            $selectedProducts = array_chunk($selection, $size_of_request);
            $session->set('selected_products', $selectedProducts);
            return $this->render('ajaxbulkupload', [
                'totalcount' => $Productcount,
                'pages' => $pages
                ]);
        }
        elseif($action == 'batch-retire')
        {
            $pages = count($selection);
            $session->set('retire_product', $selection);
            return $this->render('retireproduct',[
                'totalcount' => $Productcount,
                'pages'=>$pages            
                ]);

        }
        elseif($action == 'batch-product-status')
        {
            $pages = count($selection);
            $session->set('product_status', $selection);
            return $this->render('bulkproductstatus',[
                'totalcount' => $Productcount,
                'pages' =>$pages
                ]);
        }

        elseif ($action == 'batch-update-inventory') 
        {
            $selectedProducts = array_chunk($selection, $size_of_request);
            $session->set('batch-update-inventory', $selectedProducts);
            return $this->render('batchupdateinventory', [
                'totalcount' => $Productcount,
                'pages' => $pages
                ]);

        }
        else if($action == 'start-batch-update')
        {
            $syncConfigJson = Data::getConfigValue($merchant_id,'sync-fields');
            if($syncConfigJson)
            {
                $checkConfig = true;
                $syncFields = json_decode($syncConfigJson,true);
            }
            else
            {
                $sync_fields = [
                'sku' => '1',
                'title' => '1',
                'image' => '1',
                'inventory' => '1',
                'parent_inventory'=>'1',
                'weight' => '1',
                'price' => '1',
                'upc' => '1',
                'description' => '1',
                'variant_options' => '1',
                ];
                $syncFields['sync-fields']=$sync_fields;
            }
            
            //$import_option = Data::getConfigValue($merchant_id, 'import_product_option');
            $session->set('sync-fields', serialize($syncFields));
            //$session->set('import_option', serialize($syncFields));
            $session->set('updateproductAll', serialize($selection));
            //$pages=0;
            //$pages=ceil(count($selection)/250);
            $pages = count($selection);
            $session->close();
            unset($productAll);
            return $this->render('startbatchupdate', [
                'totalcount' =>$Productcount,
                'pages' => $pages
                ]);
        }
        $session->close();
    }

    public function actionBulkproductstatus()
    {
        $session = Yii::$app->session;
        $selection = isset($session['product_status']) ? $session['product_status'] : [];
        print_r($selection);die();
        $index = Yii::$app->request->post('index');
        if (!empty($selection)) {
            //foreach ($selection as $id) {
            $id = $selection[$index];

            $query = "SELECT sku,type FROM (SELECT * FROM `jet_product` WHERE `merchant_id`='" . MERCHANT_ID . "' AND `bigproduct_id`='" . $id . "') as `jp` INNER JOIN (SELECT * FROM `wish_product` WHERE `merchant_id`='" . MERCHANT_ID . "' AND `product_id`='" . $id . "') as `wp` ON `jp`.`bigproduct_id`=`wp`.`product_id` WHERE `wp`.`merchant_id`='" . MERCHANT_ID . "' ";
            $result = Data::sqlRecords($query, 'one');

            if (isset($result['sku']) && !empty($result)) {
                if ($result['type'] == 'variants') {

                    $query = "SELECT option_sku,`jvp`.option_id FROM (SELECT * FROM `jet_product_variants` WHERE `merchant_id`='" . MERCHANT_ID . "' AND `product_id`='" . $id . "') as `jvp` INNER JOIN (SELECT * FROM `wishh_product_variants` WHERE `merchant_id`='" . MERCHANT_ID . "' AND `product_id`='" . $id . "') as `wvp` ON `jvp`.`option_id`=`wvp`.`option_id` WHERE `wvp`.`merchant_id`='" . MERCHANT_ID . "' ";
                    $skus = Data::sqlRecords($query, 'all');
                    if (is_array($skus) && count($skus)) {
                        $error = [];
                        $uploadCount = 0;
                        $notUploadCount = 0;

                        foreach ($skus as $sku) {

                            $productStatus = new Wishapi(API_USER, API_PASSWORD, AUTH_KEY);
                            $feed_data = $productStatus->getItemstatus($sku['option_sku']);

                            if (isset($feed_data['error'])) {
                                $notUploadCount++;
                                if ($feed_data['error'][0]['code'] == 'CONTENT_NOT_FOUND.GMP_ITEM_QUERY_API') {

                                    $error[] = 'Error : ' . $sku['option_sku'] . ' : Product not uploaded on Wishmarketplace';

                                } else {
                                    $error[] = 'Error : ' . $sku['option_sku'] . ' : ' . $feed_data['error'][0]['info'];
                                }

                                $query = "UPDATE wish_product_variants SET status='" . WishProduct::PRODUCT_STATUS_NOT_UPLOADED . "' WHERE option_id='" . $sku['option_id'] . "' AND `merchant_id`='" . MERCHANT_ID . "'";

                            } elseif (isset($feed_data['MPItemView'])) {
                                $uploadCount++;
                                if ($result['sku'] == $sku['option_sku']) {
                                    $mainProductStatus = $feed_data['MPItemView'][0]['publishedStatus'];
                                }

                                $status = $feed_data['MPItemView'][0]['publishedStatus'];
                                $query = "UPDATE wish_product_variants SET status='" . $status . "' WHERE option_id='" . $sku['option_id'] . "' AND `merchant_id`='" . MERCHANT_ID . "'";
                                Data::sqlRecords($query, null, 'update');

                            } else {
                                $notUploadCount++;
                                $error[] = 'Status Not Updated for variant sku : ' . $sku['option_sku'];
                            }
                        }

                        //update main product status
                        if ($uploadCount) {
                            if ($notUploadCount) {
                                $query = "UPDATE wish_product SET status='" . WishProduct::PRODUCT_STATUS_NOT_UPLOADED . "' WHERE product_id='" . $id . "' AND `merchant_id`='" . MERCHANT_ID . "'";
                            } else {
                                $query = "UPDATE wish_product SET status='" . WishProduct::PRODUCT_STATUS_UPLOADED . "' WHERE product_id='" . $id . "' AND `merchant_id`='" . MERCHANT_ID . "'";
                            }
                        } else {
                            $query = "UPDATE wish_product SET status='" . WishProduct::PRODUCT_STATUS_NOT_UPLOADED . "' WHERE product_id='" . $id . "' AND `merchant_id`='" . MERCHANT_ID . "'";
                        }
                        Data::sqlRecords($query, null, 'update');
                        //end

                        if (count($error)) {
                            $returnArr['error'] = implode('<br>', $error);
                        } else {
                            $returnArr = ['success' => ['count' => $uploadCount, 'message' => 'Status Successfully Updated']];
                        }
                    } else {
                        $returnArr['error'] = 'Product not found on wish';
                    }
                } else {
                    $productStatus = new Wishapi(API_USER, API_PASSWORD, AUTH_KEY);
                    $feed_data = $productStatus->getItemstatus($result['sku']);

                    if (isset($feed_data['MPItemView'])) {

                        $status = $feed_data['MPItemView'][0]['publishedStatus'];

                        //update main product status(s)
                        $query = "UPDATE wish_product SET status='" . $status . "' WHERE product_id='" . $id . "' AND `merchant_id`='" . MERCHANT_ID . "'";
                        Data::sqlRecords($query, null, 'update');

                        $returnArr = ['success' => ['count' => 1, 'message' => 'Status Successfully Updated']];

                    } elseif (isset($feed_data['error'])) {
                        $error[] = $feed_data['error'][0]['info'];

                        if ($feed_data['error'][0]['code'] == 'CONTENT_NOT_FOUND.GMP_ITEM_QUERY_API') {
                            $returnArr['error'] = $result['sku'] . ' : Product not uploaded on Wishmarketplace';
                        } else {
                            $returnArr['error'] = $result['sku'] . ' : ' . $feed_data['error'][0]['info'];
                        }
                        $query = "UPDATE wish_product SET status='" .WishProduct::PRODUCT_STATUS_NOT_UPLOADED . "' WHERE product_id='" . $id . "' AND `merchant_id`='" . MERCHANT_ID . "'";
                        Data::sqlRecords($query, null, 'update');

                    } else {
                        $returnArr['error'] = $result['sku'] . ' : ' . ' Status Not Updated';
                    }

                }
            } else {
                $returnArr['error'] = 'Product Id :' . $id . ' Not Found';
            }
        }


        return json_encode($returnArr);
    }
     public function actionStartbatchupload()
    {
        if (Yii::$app->user->isGuest){
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        }

        $session = Yii::$app->session;

        $returnArr = ['error' => true];

        $index = Yii::$app->request->post('index');
        $selectedProducts = isset($session['selected_products'][$index])?$session['selected_products'][$index]:[];
        $count = count($selectedProducts);

        if(!$count) {
            $returnArr = ['error'=>true, 'message'=>'No Products to Upload'];
        } 
        else 
        {
            $connection = Yii::$app->getDb();
            $merchant_id = "";
            if(isset($session['merchant_id']))
                $merchant_id = $session['merchant_id'];
            else
                $merchant_id = MERCHANT_ID;

            try {


                $productResponse = $this->wishConfig->createProductOnWish($selectedProducts, MERCHANT_ID);
               
                if(isset($productResponse['errors']) ||isset($productResponse['feederror']) ){
                    if(isset($productResponse['errors']))
                    $returneArr = ['error'=>true, 'message'=>$productResponse['errors']];
                    else
                      
                    $returneArr = ['error'=>true, 'message'=>$productResponse['feederror']];
                }
                if(isset($productResponse['success']) || isset($productResponse['successID']))
                {
                    if($productResponse['success']){
                    $success=count($productResponse['success']);
                    
                    $msg = "product feed successfully submitted on wish.";
                    $returnArr = ['success'=>true, 'message'=>$msg,'count'=>$success/*, 'id'=>$feedId*/];
                    }
                    else{
                    $success=count($productResponse['successID']);
                    
                    $msg = "product feed successfully submitted on wish.";
                    $returnArr = ['success'=>true, 'message'=>$msg,'count'=>$success/*, 'id'=>$feedId*/];
                    }
                    return json_encode($returnArr);
                }
                elseif(isset($productResponse['errors'])) {
                    $msg = json_encode($productResponse['errors']);
                    $returnArr = ['error'=>true, 'message'=>$msg];
                }
                elseif (isset($productResponse['feederror'])) {

                    $msg = json_encode($productResponse['feederror']);
                    $returnArr = ['error'=>true, 'message'=>$msg];
                    //print_r($returnArr);die;
                }


                //save errors in database for each erroed product

                if (count($returnArr['error'])>0) {

                    $_feedError = null;
                    if (isset($productResponse['error'])) {
                        $msg = $productResponse['error'];
                        $_feedError = $msg;
                        unset($productResponse['errors']);

                    }
                  
                //print_r($productResponse);die("wqrewr");
                if (isset($productResponse['errors'])) {
                        foreach ($productResponse['errors'] as $productSku => $error) {

                            if (is_array($error)) {
                                $error = implode(',', $error);
                            }
                    
                            $query = "UPDATE `wish_product` wp JOIN `jet_product` jp ON wp.product_id=jp.bigproduct_id AND jp.merchant_id = wp.merchant_id SET wp.`error`='" . addslashes($error) . "' where jp.sku='" . $productSku . "'";
                            Data::sqlRecords($query, null, "update");
                            
                        }
               // }
                        $returnArr['error'] = true;
                            //$returnArr['error_msg'] = $productResponse['errors'];

                            //$returnArr['originalmessage'] = $productResponse['originalmessage'];
                        
                        $returnArr['error_count'] = count($productResponse['errors']);

                        $returnArr['erroredSkus'] = implode(',', array_keys($productResponse['errors']));
                    }   
                if(isset($productResponse['feederror'])) {
                        //$returnArr[$productResponse['erroredSkus']] = $productResponse['feederror'];
               
                        $returnArr['error'] = true;
                           
                        $returnArr['error_count'] = count($productResponse['feederror']);
                        //print_r($productResponse);die("dfgdf");
                       // $returnArr['erroredSkus'] = $productResponse['erroredSkus'];
                  
              }
          }



            }catch (Exception $e) {

                $returnArr = ['error' => true, 'error_msg' => $e->getMessage()];
            }
            return json_encode($returnArr);
        }
    }
    
    public function actionStartbatchupload11()
    {
        if (Yii::$app->user->isGuest){
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        }

        $session = Yii::$app->session;

        $returnArr = ['error' => true];

        $index = Yii::$app->request->post('index');
        $selectedProducts = isset($session['selected_products'][$index])?$session['selected_products'][$index]:[];
        $count = count($selectedProducts);

        if(!$count) {
            $returnArr = ['error'=>true, 'message'=>'No Products to Upload'];
        } 
        else 
        {
            $connection = Yii::$app->getDb();
            $merchant_id = "";
            if(isset($session['merchant_id']))
                $merchant_id = $session['merchant_id'];
            else
                $merchant_id = MERCHANT_ID;

            try {


                $productResponse = $this->wishConfig->createProductOnWish($selectedProducts, MERCHANT_ID);
               
                if(isset($productResponse['errors'])){

                	//$productResponse=json_encode($productResponse);
                    $returneArr = ['error'=>true, 'message'=>$productResponse['errors']];
                    //print_r($returnerrorArr);die;
                    
                }
                if(isset($productResponse['success']) || isset($productResponse['successID']))
                {
                    if($productResponse['success']){
                    $success=count($productResponse['success']);
                    
                    $msg = "product feed successfully submitted on wish.";
                    $returnArr = ['success'=>true, 'message'=>$msg,'count'=>$success/*, 'id'=>$feedId*/];
                    }
                    else{
                    $success=count($productResponse['successID']);
                    
                    $msg = "product feed successfully submitted on wish.";
                    $returnArr = ['success'=>true, 'message'=>$msg,'count'=>$success/*, 'id'=>$feedId*/];
                    }
                    return json_encode($returnArr);
                }
               // if(isset($productResponse['successID']))
               //  {
               //      $ids = implode(',',$productResponse['successID']);
               //      $success=count($productResponse['successId']);
                   
               //      $feed_file = isset($productResponse['successId'])?$productResponse['successId']:'';
               //      $query="INSERT INTO `wish_product_feed`(`merchant_id`,`feed_id`,`product_ids`,`feed_file`)VALUES('".MERCHANT_ID."','".$productResponse['successId']."','".$ids."','".$feed_file."')";
               //      Data::sqlRecords($query,null,"insert");
                    
               //      $msg = "product feed successfully submitted on wish.";
               //      $returnArr = ['success'=>true, 'message'=>$msg,'count'=>$success/*, 'id'=>$feedId*/];
               //      return json_encode($returnArr);
               //  }
                elseif(isset($productResponse['errors'])) {
                    $msg = json_encode($productResponse['errors']);
                    $returnArr = ['error'=>true, 'message'=>$msg];
                }
              /*  elseif (isset($productResponse['feederror'])) {

                    $msg = json_encode($productResponse['feederror']);
                    $returnArr = ['error'=>true, 'message'=>$msg];
                    //print_r($returnArr);die;
                }*/


                //save errors in database for each erroed product

                if (count($returnArr['error'])>0) {

                	$_feedError = null;
                	if (isset($productResponse['error'])) {
                		$msg = $productResponse['error'];
                		$_feedError = $msg;
                		unset($productResponse['errors']);

                	}
                  
                	/*foreach ($productResponse['errors'] as $error) {

                        $sku=preg_match("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is",$error,$match);
                        
                        $productsku = str_replace("'", "", $match);
                       // print_r($produc);die("gfgh");
                        if($productsku){
                          $query = "UPDATE `wish_product` wp JOIN `jet_product` jp ON wp.product_id=jp.bigproduct_id AND jp.merchant_id = wp.merchant_id SET wp.`error`='" . addslashes($error) . "' where jp.sku='" . $productsku[0] . "'";
                          Data::sqlRecords($query, null, "update");
                      }
                   
                    else{
                        $query = "UPDATE `wish_product` wp JOIN `jet_product` jp ON wp.product_id=jp.bigproduct_id AND jp.merchant_id = wp.merchant_id SET wp.`error`='" . addslashes($error)."'" ;
                        Data::sqlRecords($query, null, "update");
                    }
                }   */
                    foreach ($productResponse['errors'] as $productSku => $error) {

                        if (is_array($error)) {
                            $error = implode(',', $error);
                        }
                
                        $query = "UPDATE `wish_product` wp JOIN `jet_product` jp ON wp.product_id=jp.bigproduct_id AND jp.merchant_id = wp.merchant_id SET wp.`error`='" . addslashes($error) . "' where jp.sku='" . $productSku . "'";
                        Data::sqlRecords($query, null, "update");
                        
                    }

                $returnArr['error'] = true;
                	//$returnArr['error_msg'] = $productResponse['errors'];

                	//$returnArr['originalmessage'] = $productResponse['originalmessage'];
                
                $returnArr['error_count'] = count($productResponse['errors']);
                
                $returnArr['erroredSkus'] = implode(',', array_keys($productResponse['errors']));
                
                if (isset($productResponse['feederror'])) {
                $returnArr[$productResponse['errsku']] = $productResponse['feederror'];
               
                  $returnArr['feederror'] = $_feedError;
              }
          }



      }catch (Exception $e) {

        $returnArr = ['error' => true, 'error_msg' => $e->getMessage()];
    }

            
    return json_encode($returnArr);
}
}

public function actionUpdateinventory()
{
   $session = Yii::$app->session;

        //==============
   if (Yii::$app->user->isGuest) {
      return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
  }

  $query = 'SELECT `jet`.`bigproduct_id`, `jet`.`merchant_id`, `sku`, `type`, `qty` FROM (SELECT * FROM `wish_product` WHERE `merchant_id`="' . MERCHANT_ID . '") as `wal` INNER JOIN (SELECT * FROM `jet_product` WHERE `merchant_id`="' . MERCHANT_ID . '") as `jet` ON `jet`.`bigproduct_id`=`wal`.`product_id` where`wal`.`merchant_id`="' . MERCHANT_ID . '"'; /* AND wal.id BETWEEN 1156390 AND 1160094 */           /*`wal`.`status`="PUBLISHED" and */ 
  $product = Data::sqlRecords($query, "all", "select");

    	/*$query='select jet.bigproduct_id,sku,type,qty,fulfillment_lag_time from `walmart_product` wal INNER JOIN `jet_product` jet ON jet.bigproduct_id=wal.product_id where wal.status="PUBLISHED" and wal.merchant_id=jet.merchant_id and wal.merchant_id="'.MERCHANT_ID.'"';
        $product = Data::sqlRecords($query,"all","select");
        */
        
        $Productcount = count($product);

        //echo $Productcount;die;
        //$Productcount=1000;

        if(is_array($product) && $Productcount)
        {
    		$size_of_request = 200;//Number of products to be uploaded at once(in single feed)
    		$pages = (int)(ceil($Productcount/$size_of_request));

    		$max_feed_allowed_per_hour = 10;
    		if($pages > $max_feed_allowed_per_hour)
    		{

    			$size_of_request = (int)(ceil($Productcount/$max_feed_allowed_per_hour));
    			if($size_of_request > 10000) {
    				Yii::$app->session->setFlash('error', "MAX Feed Limit Exceeded.");
    				return $this->redirect(['index']);
    			}
    			$pages = (int)(ceil($Productcount/$size_of_request));
    		}

    		$selectedProducts = array_chunk($product, $size_of_request);
    		$session->set('products_for_inventory_update', $selectedProducts);
    		$session->close();

    		return $this->render('updateinventory', [
                'totalcount' => $Productcount,
                'pages'=>$pages
                ]);
    	}
    	else
    	{
    		Yii::$app->session->setFlash('error', "No Products Found..");
    		return $this->redirect(['index']);
    	}
    }
    
    public function actionInventorypost()
    {
    	$session = Yii::$app->session;

    	$returnArr = ['error' => true];

    	$index = Yii::$app->request->post('index');
    	$selectedProducts = isset($session['products_for_inventory_update'][$index])?$session['products_for_inventory_update'][$index]:[];

        //$selectedProducts=$session['products_for_inventory_update'][4];

        //print_r($session['products_for_inventory_update'][1]);
       //print_r($selectedProducts);die;
    	$count = count($selectedProducts);

    	$errors = [];

    	if($count) {
    		$response = $this->wishConfig->updateInventoryOnWish($selectedProducts, "product",MERCHANT_ID);

            // print_r($response);
    		if(isset($response['errors']))
    			$returnArr = ['error' => "Inventory Feed Error : Inventory not updated on Wishmarketplace",'message'=>'Inventory for some Products is not updated due to '.json_encode($response['errors'])];
    		else
    			$returnArr = ['success' => true, 'count'=>$count];
    	}
    	return json_encode($returnArr);
    }
    
    public function actionUpdateprice()
    {
        $session = Yii::$app->session;
        if (Yii::$app->user->isGuest) {
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        }
        
        $query='select jet.bigproduct_id,sku,type,wal.product_price,comparision_price from `wish_product` wal INNER JOIN `jet_product` jet ON jet.bigproduct_id=wal.product_id where wal.merchant_id=jet.merchant_id and wal.merchant_id="'.MERCHANT_ID.'"';
        $product = Data::sqlRecords($query,"all","select");
        // print_r($product);die();
        $Productcount = count($product);
        if(is_array($product) && $Productcount)
        {
            $size_of_request = 50;//Number of products to be uploaded at once(in single feed)
            $pages = (int)(ceil($Productcount/$size_of_request));
            $max_feed_allowed_per_hour = 10;
            if($pages > $max_feed_allowed_per_hour)
            {
                $size_of_request = (int)(ceil($Productcount/$max_feed_allowed_per_hour));
                if($size_of_request > 10000) {
                    Yii::$app->session->setFlash('error', "MAX Feed Limit Exceeded.");
                    return $this->redirect(['index']);
                }
                $pages = (int)(ceil($Productcount/$size_of_request));
            }
            $selectedProducts = array_chunk($product, $size_of_request);
            $session->set('products_for_price_update', $selectedProducts);
            $session->close();

            return $this->render('updateprice', [
                'totalcount' => $Productcount,
                'pages'=>$pages
                ]);
        }else
        {
            Yii::$app->session->setFlash('error', "No Products Found..");
            return $this->redirect(['index']);
        }
    }
    
    public function actionPricepost()
    {
        $session = Yii::$app->session;
        $returnArr = ['error' => true];
        $index = Yii::$app->request->post('index');
        $selectedProducts = isset($session['products_for_price_update'][$index])?$session['products_for_price_update'][$index]:[];
        $count = count($selectedProducts);
        foreach($selectedProducts as $product)
        {
            $errors = [];
            if($count) {
                $response = $this->wishConfig->updatePriceOnWish($product, "product");
                if(isset($response['errors']))
                    $returnArr = ['error' => "Price Feed Error : Price not updated on wish",'message'=>'Price for some Products is not updated due to '.json_encode($response['errors'])];
                else
                    $returnArr = ['success' => true, 'count'=>$count];
            }
        }
        return json_encode($returnArr);
    }
    
    public function actionBatchretire()
    {
    	$session = Yii::$app->session;
    	$selection = isset($session['retire_product']) ? $session['retire_product'] : [];
    	$index = Yii::$app->request->post('index');
    	if (!empty($selection)) {
    		$id = $selection[$index];
    		$query = Data::sqlRecords('SELECT sku,type FROM `jet_product` WHERE bigproduct_id="' . $id . '" AND merchant_id="' . MERCHANT_ID . '" ', 'one');
    		if (isset($query) && !empty($query)) {
    			if ($query['type'] == 'variants') {
    				$skus = Data::sqlRecords('SELECT option_sku FROM `jet_product_variants` WHERE product_id="' . $id . '" AND merchant_id="' . MERCHANT_ID . '" ', null, 'all');
    				if (!is_array($skus) || (is_array($skus) && !count($skus)))
    					$skus = [];

    			} else {
    				$skus[0]['option_sku'] = $query['sku'];
    			}
    			$errors = [];
    			$success = [];
    			foreach ($skus as $sku) {
    				$feed_data = $this->wishConfig->retireProduct($sku['option_sku']);
    				if (isset($feed_data['response'])){
    					$success[] = $sku['option_sku'] . ' : '.$feed_data['response'];
    				}elseif (isset($feed_data['errors'])) {
    						$errors[] = $sku['option_sku'] . ' : '.$feed_data['errors'];
    					}
    				else{  
                        $returnArr = ['error' => 'Product Not Uploaded to Wish panel!!'];
                    }
                }
                if (count($errors)) {
                    $returnArr['error'] = true;
                    $returnArr['error_msg'] = implode('<br/>', $errors);
                }
                if (count($success)) {
                    $returnArr['success'] = true;
                    $returnArr['success_count'] = count($success);
                    $returnArr['success_msg'] = implode('<br/>', $success);
                }
            }
        } else {
          $returnArr = ['error' => 'Product Id :Not Found'];
      }
      return json_encode($returnArr);
  }

   /* public function actionBatchretire()
    {
        
        $session = Yii::$app->session;

        $selection  = isset($session['retire_product'])?$session['retire_product']:[];

        $index = Yii::$app->request->post('index');

        $connection = Yii::$app->getDb();

        $retire=0;
        $delete=0;
        if($index==''){
            $index=0;
        }
        
        if(!empty($selection)){
            //foreach ($selection as $id ) {
            $id = $selection[$index];

            $data = Data::sqlRecords('SELECT status FROM `walmart_product` WHERE product_id="'.$id.'" AND merchant_id="'.MERCHANT_ID.'" ','one'); 

            $query = Data::sqlRecords('SELECT sku,type,variant_id,w.status FROM `jet_product`j LEFT JOIN `walmart_product` as w ON j.bigproduct_id=w.product_id WHERE bigproduct_id="'.$id.'" AND w.merchant_id="'.MERCHANT_ID.'" AND j.merchant_id="'.MERCHANT_ID.'" ','one');            


            //if(isset($query) && !empty($query) && $query['status']!='Not Uploaded'){
            if($query['type']=='variants')
            {
                $skus = Data::sqlRecords('SELECT option_sku,option_id FROM `jet_product_variants` WHERE product_id="'.$id.'" AND merchant_id="'.MERCHANT_ID.'" ',null,'all');
               
                if(!is_array($skus) || (is_array($skus) && !count($skus)))
                    $skus = [];

            }else {
                $skus[0]['option_sku'] = $query['sku'];
                $skus[0]['option_id'] = $query['variant_id'];
            }

            if($skus){
                foreach ($skus as $sku) {

                    if($query['status']!='Not Uploaded'){
                        $retireProduct = new Walmartapi(API_USER, API_PASSWORD, CONSUMER_CHANNEL_TYPE_ID);
                        $feed_data = $retireProduct->retireProduct($sku['option_sku']);

                        if($query['type']=='variants'){
                            if($sku['option_id']==$query['variant_id']){
                                $delprod="DELETE FROM `jet_product` WHERE sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                                $model = $connection->createCommand($delprod)->execute();  
                            }
                            $delprod="DELETE FROM `jet_product_variants` WHERE option_sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                            $model = $connection->createCommand($delprod)->execute();  
                        }
                        else{
                            $delprod="DELETE FROM `jet_product` WHERE sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                            $model = $connection->createCommand($delprod)->execute(); 
                        }
                        $retire=1;
                    }

                    else
                    {

                        print_r($sku);die;
                        if($query['type']=='variants'){
                            if($sku['option_id']==$query['variant_id']){
                                $delprod="DELETE FROM `jet_product` WHERE sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                                $model = $connection->createCommand($delprod)->execute();  
                            }
                            $delprod="DELETE FROM `jet_product_variants` WHERE option_sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                            $model = $connection->createCommand($delprod)->execute();  
                        }
                        else{
                            $delprod="DELETE FROM `jet_product` WHERE sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                            $model = $connection->createCommand($delprod)->execute(); 
                        }

                        $delete=1;
                    }

                }

                if(isset($feed_data['error']))
                {
                    $returnArr = ['error'=>true, 'message'=>$sku['option_sku'].' : '.$feed_data['error'][0]['description']];
                } 
                if($retire) {
                    $returnArr = ['success'=>['count'=>1,'message'=>'Product Retire']];
                }
                if($delete){
                    $returnArr = ['success'=>['count'=>1,'message'=>'Product Deleted from the app']];
                }
            }

            else{
                $returnArr = ['error'=>'Product Id :'.$id.' Not Found'];
            }
            //}
        }else{
            $returnArr = ['error'=>'Product Id :'.$id.' Not Found'];
        }
        
        return json_encode($returnArr);
    }*/
    
    


    public function actionBatchimport()
    { 
        $connection = Yii::$app->getDb();
        $merchant_id = MERCHANT_ID;
        $shopname=SHOP;
        $token=TOKEN;
        $store_hash=STOREHASH;
        $countProducts=0;$pages=0;

        $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);

        $resource='catalog/products';
        
        $countUpload=$bigcom->get($resource);

        $countProducts=$countUpload['meta']['pagination']['total'];
        $pages=$countUpload['meta']['pagination']['total_pages'];

        $session ="";
        $session = Yii::$app->session;

        if(!is_object($session)){
            Yii::$app->session->setFlash('error', "Can't initialize Session.Product(s) upload cancelled.");
            return $this->redirect(['index']);
        }

        $session->set('product_page',$pages);
        $session->set('merchant_id',$merchant_id);
        $session->close();
        unset($jetConfigarray);
        return $this->render('batchimport', [
            'totalcount' => $countProducts,
            'pages'=>$pages
            ]);
    }

    public function actionCustomerdata(){

        $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);
        $resource='stores';

        $customers= $bigcom->get1($resource); 

        // print_r($customers);
        // die("dfgg");

    }
    
    /**ACTION TO IMPORT PRODUCT*/
    public function actionBatchimportproduct()
    {
        $index=Yii::$app->request->post('index');
        $countUpload=Yii::$app->request->post('count');
        $session = Yii::$app->session;
        $connection = Yii::$app->getDb();
        
        try
        {
        	$readyCount = 0;
        	$notSku = 0;
        	$jProductTotal=0;
        	$notType = 0;
        	$merchant_id=MERCHANT_ID;
            $pages=$session->get('product_page');
            
            
            $products = $this->bigcom->call('GET', 'catalog/products?include_fields=name,description,sale_price,price,sku,upc,categories,inventory_level,brand_id&include=variants,images&limit=50&page='.$index.'');

            if($products){
                foreach ($products['data'] as $prod){
                    $noSkuFlag = 0;
                    if($prod['categories'][0]==''){
                        $notType ++;
                        continue;
                    }
                    
                    if($prod['sku']=="") {
                        if($prod['variants'][0]['sku']==""){
                            $noSkuFlag = 1;
                            $notSku ++;
                            continue;
                        }
                    }
                    
                    if(!$noSkuFlag){
                        $readyCount ++;
                        $jProductTotal++;
                        Jetproductinfo::saveNewRecords($prod, $merchant_id, $connection,false,$this->bigcom);
                    }
                }
            }
            
            if($index==$pages-1){
                $inserted="";
                $result="";
                $inserted=Data::sqlRecords("SELECT `merchant_id` FROM `insert_product` WHERE merchant_id='".$merchant_id."'");

                $count=Data::sqlRecords("SELECT count(*) as 'count' FROM `jet_product` where merchant_id='".$merchant_id."'");
                
                /**insert data into insert products*/
                if(!$result){
                 $queryObj="";
                 $query='INSERT INTO `insert_product`
                 (
                 `merchant_id`,
                 `product_count`,
                 `total_product`,
                 `not_sku`,
                 `status`
                 )
                 VALUES(
                 "'.$merchant_id.'",
                 "'.$jProductTotal.'",
                 "'.$count.'",
                 "'.$notSku.'",
                 "inserted"  
                 )';
                 $queryObj = $connection->createCommand($query)->execute();
             }else{
                $updateQuery="UPDATE `insert_product` SET `product_count`='".$jProductTotal."' ,`total_product`='".$count['count']."', `not_sku`='".$notSku."' WHERE merchant_id='".$merchant_id."'";
                $updated = $connection->createCommand($updateQuery)->execute();
            }   
        }
    }
    catch (BigcomApiException $e){
        return $returnArr['error'] = $e->getMessage();
    }
    catch (BigcomApiException $e){
        return $returnArr['error'] = $e->getMessage();
    }
    $returnArr['success']['count'] = $readyCount;
    $returnArr['success']['not_sku'] = $notSku;
    $returnArr['success']['not_type'] = $notType;
    $connection->close();
    return json_encode($returnArr);
}

public function actionLoad()
{
        //echo Yii::$app->homeUrl.'var/MPProduct.xml';die;
    $str = file_get_contents('/opt/lampp/htdocs/walmart/var/MPProduct.xml');
    $response = Wishapi::xmlToArray($str);
    echo addslashes($response['MPItemFeed']['_value']['MPItem']['Product']['longDescription']);
}

public function actionCategoryadd()
{
    $query="select category_id,attribute_values,parent_id from wish_category where level=1";
    $response = Data::sqlRecords($query, "all", "select");
    $parentcategory=[];
    $count=0;
    foreach($response as $value){
        if(!in_array($value['parent_id'],$parentcategory))
        {
            $count++;
            $parentcategory[]=$value['parent_id'];
        }        
    }
    foreach($parentcategory as $val){
        $query="insert into wish_category(merchant_id,category_id,title,parent_id,level)values(1,'Other','Other','".$val."',1)";
        $response = Data::sqlRecords($query, null, "insert");
    }

}
/* Coded by Vishal */
public function actionBatchproductstatus()
{
    $session = Yii::$app->session;
    $producttoget = "Not Uploaded";
    /*$query = 'select `jet.sku`,`product_id` from (SELECT * FROM `wish_product` WHERE `merchant_id`= "' . MERCHANT_ID . '" AND WHERE `status` <> "'.$producttoget.'") as wish INNER JOIN (SELECT * FROM `jet_product` WHERE `merchant_id`= "' . MERCHANT_ID . '") as jet ON jet.bigproduct_id=wish.product_id where wish.merchant_id="' . MERCHANT_ID . '"';*/
    $query='select jet.sku,jet.type,wish.product_id from `wish_product` wish INNER JOIN `jet_product` jet ON jet.bigproduct_id=wish.product_id where wish.merchant_id=jet.merchant_id and wish.merchant_id="'.MERCHANT_ID.'" and wish.status <> "Not Uploaded"';
    $product = Data::sqlRecords($query, "all", "select");
    $Productcount = count($product);
   // print_r($product);die;
    if(is_array($product) && $Productcount){
            $size_of_request = 50;//Number of products to be uploaded at once(in single feed)
            $pages = (int)(ceil($Productcount / $size_of_request));
            $max_feed_allowed_per_hour = 10;
            if($pages > $max_feed_allowed_per_hour){
                $size_of_request = (int)(ceil($Productcount / $max_feed_allowed_per_hour));
                if($size_of_request > 10000){
                    Yii::$app->session->setFlash('error', "MAX Feed Limit Exceeded.");
                    return $this->redirect(['index']);
                }
                $pages = (int)(ceil($Productcount / $size_of_request));
            }
            $selectedProducts = array_chunk($product, $size_of_request);
            $session->set('products_for_status_update', $selectedProducts);
            $session->close();
            return $this->render('batchstatus', [
                'totalcount' => $Productcount,
                'pages' => $pages
                ]);
        }else{
            Yii::$app->session->setFlash('error', "No Products Found..");
            return $this->redirect(['index']);
        }
    }
    /* end by Vishal */
    public function actionBatchproductretire()
    {
        $product['products']=22000;
        if (is_array($product) && isset($product['products']) && intval($product['products']) > 0) {
            $pages = ceil(22000/ 20);
            $session = Yii::$app->session;
            $session->set('wishConfig', serialize($this->wishConfig));
            $session->set('product_page', $pages);

            return $this->render('bulkretire',
                [
                'totalcount' => $product['products'],
                'pages' => $pages
                ]
                );
        } else {
            echo "No Products Found.";
        } 
    }

    /**
    *Get product From Walmart panel
    */

    public function actionGetproduct()
    {
        $sku=$_GET['sku'];
        $getItemsCount = 20;
        $finish = false;
        $finishWithError = false;

        $session = Yii::$app->session;
        $wishConfig = unserialize($session->get('wishConfig'));
        $merchant_id = MERCHANT_ID;
        if (!is_object($wishConfig)) {
            $wishConfig = $this->wishConfig;
        }
        $offset = $index * $getItemsCount;
        // Get $getItemsCount products status(s) from walmart
        $productArray = $wishConfig->getItems(['limit' => $getItemsCount, 'offset' => $offset,'sku'=>$sku,'publishedStatus'=>'']);

        // print_r($productArray);die("dgfdg");

    }

    public function actionProductretire()
    {
    	$getItemsCount = 20;
    	$finish = false;
    	$finishWithError = false;
    	$index = Yii::$app->request->post('index');

    	try {
    		$session = Yii::$app->session;
    		$wishConfig = unserialize($session->get('wishConfig'));
    		$merchant_id = MERCHANT_ID;

    		if (!is_object($wishConfig)) {
    			$wishConfig = $this->wishConfig;
    		}

    		$offset = $index * $getItemsCount;

    		// Get $getItemsCount products status(s) from walmart
    		$productArray = $wishConfig->getItems(['limit' => $getItemsCount,'offset' => $offset,'publishedStatus'=>'PUBLISHED']);

    		$count = 0;
    		if(isset($productArray['error']))
    		{
    			if(is_array($productArray['error'])) {
    				//[description] => No item found
    				foreach ($productArray['error'] as $error) {
    					if(isset($error['code']) && $error['code']=='CONTENT_NOT_FOUND.GMP_ITEM_QUERY_API') {
    						$finish = true;
    					} else {
    						$returnArr['error'] = $error['description'];
    					}
    				}
    			}
    		}

    		elseif(isset($productArray['errors']))
    		{
    			if(isset($productArray['errors']['error']))
    			{
    				if(isset($productArray['errors']['error']['code']) && $productArray['errors']['error']['code']=='UNAUTHORIZED.GMP_GATEWAY_API')
    				{
    					$finishWithError = true;
    					$returnArr['error'] = "Wish API Credentials are invalid.";
    				}
    			}
    			$returnArr['api_error'] = true;
    		}

    		elseif (isset($productArray['MPItemView']))
    		{
    			foreach ($productArray['MPItemView'] as $key => $value) {

    				//$retireProduct = new Walmartapi(API_USER, API_PASSWORD, CONSUMER_CHANNEL_TYPE_ID);
    				//$feed_data = $retireProduct->retireProduct($value['sku']);
                    $sku=$value['sku'];
                    $products = $this->bigcom->call('GET', 'catalog/products?include_fields=name,is_visible,description,sale_price,price,sku,upc,categories,inventory_level,brand_id&include=variants,images&sku='.$sku.'');
                    
                    if($products){
                        foreach ($products['data'] as $prod){
                            $noSkuFlag = 0;
                            if($prod['categories'][0]==''){
                                $notType ++;
                                continue;
                            }
                            
                            if($prod['sku']=="") {
                                if($prod['variants'][0]['sku']==""){
                                    $noSkuFlag = 1;
                                    $notSku ++;
                                    continue;
                                }
                            }
                            
                            if(!$noSkuFlag){
                                $readyCount ++;
                                $jProductTotal++;
                                Jetproductinfo::saveNewRecords($prod, $merchant_id, $connection,false,$this->bigcom);
                            }
                        }
                    }


                    $count++;

                    //$returnArr['success']['ItemRetireResponse']=$feed_data['ItemRetireResponse']['message'];
    				//print_r($feed_data);die("gfd");
                }
            }

            //$returnArr['success']['count'] = $count;
            if ($finish || $finishWithError) {
             if ($finish) {

                $query1 = "UPDATE `wish_product` SET `status`='" . WishProduct::PRODUCT_STATUS_NOT_UPLOADED . "' WHERE `status`='" . WishProduct::PRODUCT_STATUS_PROCESSING . "' AND `merchant_id`='" . $merchant_id . "'";
                Data::sqlRecords($query1, null, 'update');

                $query = "UPDATE `wish_product_variants` SET `status`='" . WishProduct::PRODUCT_STATUS_NOT_UPLOADED . "' WHERE `status`='" . WishProduct::PRODUCT_STATUS_PROCESSING . "' AND `merchant_id`='" . $merchant_id . "'";
                Data::sqlRecords($query, null, 'update');
            }
            $returnArr['finish'] = true;
        } else {
         $returnArr['success']['count'] = $count;
     }
     return json_encode($returnArr);
 } catch (Exception $e) {
  $returnArr['error'] = $e->getMessage();
  return json_encode($returnArr);
}

}

public function actionProductstatus()
{
    $session = Yii::$app->session;
    $tempproduct = array();
    $tempproduct = $_SESSION['products_for_status_update'];
    $product = $tempproduct['0'];
    $returnArr = ['error' => true];
    $count = count($product);
    $errors = [];

    foreach($product as $pro)
    {
        if($count) {
            $response = $this->wishConfig->getProductstatus($pro);

            if (isset($response['errors'])) {
                $returnArr = ['error' => " Feed Error : Status not updated on wish", 'message' => 'Status for some Products is not Get due to ' . json_encode($response['errors'])];
            } else {
                if (isset($response['success'])) {
                    $query = "UPDATE `wish_product` SET `status` = '" . $response['success'] . "' WHERE `merchant_id`= '" . MERCHANT_ID . "' AND `product_id`='" . $pro['product_id'] . "' ";
                    Data::sqlRecords($query, null, 'update');
                        if($product['type']='variants'){
                             $query = "UPDATE `wish_product_variants` SET `status` = '" . $response['success'] . "' WHERE `merchant_id`= '" . MERCHANT_ID . "' AND `product_id`='" . $pro['product_id'] . "' ";
                            Data::sqlRecords($query, null, 'update');
                        }
                    $returnArr = ['success' => true, 'count' => $count];
                        // return json_encode($returnArr);
                }
            }
        }
    }
    return json_encode($returnArr);
}

public function actionGetwishdata()
{
 $this->layout='main2';
 $html='';

 $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH); 

 $sku=trim(Yii::$app->request->post('id'));
 $merchant_id=Yii::$app->request->post('merchant_id');

 $products = $bigcom->call('GET', 'catalog/products/'.$sku.'?include=variants,images');


        /*if(count($products['data'])>0){

             $html=$this->render('store',array('data'=>$products['data']),true);
         }*/
        //else{
         $resultItems=$this->wishConfig->getItem($sku);
         $resultInventory=$this->wishConfig->getInventory($sku);
         $result_array=[];
         if(is_array($resultItems) && isset($resultItems['MPItemView'][0]) && count($resultItems)>0)
         {
            $result_array=array_merge($resultItems['MPItemView'][0], (array)$resultInventory);


            $html=$this->render('view',array('data'=>$result_array),true);
        }
        //}
        return $html;
    }
    
    public function actionErrorwalmart()
    {
        $this->layout="main2";
        $id = trim(Yii::$app->request->post('id'));
        $merchant_id = Yii::$app->request->post('merchant_id');
        
        $errorData=array();
        $connection=Yii::$app->getDb();
        $errorData=$connection->createCommand('SELECT `error` from `wish_product` where merchant_id="'.$merchant_id.'" AND `id`="'.$id.' LIMIT 0, 1"')->queryOne();
        
        $html = $this->render('errors',array('data'=>$errorData),true);
        $connection->close();
        return $html;
    }
    
    public function actionChangevariantimage(){
    	$merchant_id=$shopname = \Yii::$app->user->identity->id;
    	$this->layout="main2";
    	$product_id='';
    	$connection=Yii::$app->getDb();
    	$product_id=Yii::$app->request->post('product_id');

    	$collection=array();
    	//echo $product_id;
    	//$sql="SELECT product_id,option_id, option_image,option_sku from jet_product_variants  where product_id=".$product_id;
    	$sql="SELECT merchant_id,bigproduct_id, image,sku,additional_images from jet_product  where bigproduct_id='".$product_id."' and merchant_id='".$merchant_id."'";
    	$collection=$connection->createCommand($sql)->queryAll();
    	
    	//print_r($collection);die;
    	$html=$this->render('changevariantimage',array('collection'=>$collection),true);
    	return $html;
    	unset($connection);
    }
    
    public function actionSavevariantimage()
    {
   // die("Cvbbcvbcbc");

    	$merchant_id=MERCHANT_ID;

    	//print_r(Yii::$app->request->isPost());die("Fdgdf");
    	if (Yii::$app->request->isPost) {
    		$files=[];
    		$id=Yii::$app->request->post('id');
    		$images=[];
    		$images=Yii::$app->request->post('image');

    		$model_variant="";$model_simple='';
    		
    		$arrImage=[];
    		$finalimges=[];
    		$imageNameArr=[];
    		if(!file_exists(\Yii::getAlias('@webroot').'/upload/images/'.$merchant_id.'/'.$id)){
    			mkdir(\Yii::getAlias('@webroot').'/upload/images/'.$merchant_id.'/'.$id,0775, true);
    		}
    		$basPath=Yii::getAlias('@webroot').'/upload/images/'.$merchant_id.'/'.$id;
    		if(is_array($images) && count($images)>0){
                foreach ($images as $k=>$img){

                   $finalimges[]=$img;
               }
           }
           $updateImg="UPDATE  `jet_product` SET `additional_images`='".implode(",",$finalimges)."'  where  bigproduct_id='".$id."' and merchant_id='".$merchant_id."'";
           $collection=Yii::$app->getDb()->createCommand($updateImg)->execute();
    			//}
           return "image updated successfully";
       }

   }

   public function actionAddcustomproduct($id){

    $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);

    $resource='catalog/products/'.$id.'?include=variants,images';

    $products= $bigcom->get($resource);
    	// print_r($products);die;
    $images=$products['data']['images'];
    return $images;

}

public function actionBuybox(){
    $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);
    $resource='catalog/products/'.$id.'?include=variants,images';
    $products= $bigcom->getbuybox();
    $images=$products['data']['images'];
    return $images;
}


public function actionPromotions(){
    $this->layout="main2";

    $session = Yii::$app->session;
    $post = Yii::$app->request->post();
    $query = "SELECT * FROM `wish_promotional_price` WHERE `merchant_id`='{$post['merchant_id']}' AND `product_id`='{$post['product_id']}' AND `option_id`='{$post['option_id']}'";
    $promotions = Data::sqlRecords($query,"all","select");

    echo $this->render('promotions',['promotions'=>$promotions,'post'=>$post]);
        //print_r($post);
}

    /**
     * Update Inventory in Bulk.
     * @return mixed
    */
    public function actionBatchUpdateInventory()
    {
        $session = Yii::$app->session;
        $data = [];
        $index = Yii::$app->request->post('index');
        $selection = isset($session['batch-update-inventory'][$index]) ? $session['batch-update-inventory'][$index] : [];
        $count = count($selection);
        if (!$count) {
            $returnArr = ['error' => true, 'message' => 'No Products to Upload'];
        } else {
            $index = Yii::$app->request->post('index');
            $error = [];
            if (!empty($selection)) {
                $response = $this->wishConfig->batchupdateInventoryOnWalmartupd($selection, "product");
                if(isset($response['feedId'])){
                    if(isset($response['erroredSkus'])){
                        $returnArr = ['success' => true, 'count' => $count-$response['error_count'],'erroredSkus' => json_encode($response['erroredSkus']),'error_count'=>$response['error_count']];
                    }
                    else{
                        $returnArr = ['success' => true, 'count' => $count];
                    }

                }
                elseif(isset($response['erroredSkus']) && !empty($response['erroredSkus'])){
                    $returnArr = ['erroredSkus' => json_encode($response['erroredSkus']),'error_count'=>$response['error_count']];
                }
                elseif (isset($response['errors'])){
                    if(isset($response['erroredSkus']) && !empty($response['erroredSkus'])){
                        $returnArr = ['erroredSkus' => json_encode($response['erroredSkus']),'error_count'=>$response['error_count'],'error'=>$response['errors']['error']];
                    }
                    else{
                        $returnArr = ['error_count'=>$response['error_count'],'error'=>$response['errors']['error']];
                    }
                }
             /*   else{
                   $returnArr = ['success' => true, 'count' => $count];
               }*/

           }
           return json_encode($returnArr);
       }
   }

   public function actionStartBatchUpdate()
   {
       $session = Yii::$app->session;
       $syncFields = unserialize($session->get('sync-fields'));

       $updateproductAll = unserialize($session->get('updateproductAll'));
       $index=Yii::$app->request->post('index');
       $returnArr=[];

       $connection = Yii::$app->getDb();

       $id = $updateproductAll[$index];
       if((count($updateproductAll)-1)==$index){
        $session->remove('updateproductAll');
        $session->remove('sync-fields');
    }

    if($id)
    {
        $prod = $this->bigcom->call('GET', 'catalog/products/'.$id.'?include_fields=name,is_visible,description,sale_price,price,sku,upc,categories,inventory_level,brand_id&include=variants,images');


        if(isset($prod['errors']))
        {
            $returnArr['error'] = $prod['errors'];
        }
        if(isset($prod['data']) && count($prod['data'])>0)
        {
            $returnArr = ['success'=>['count'=>1,'message'=> 'Product Successfully Updated']];
        }

        /*    if($prod['status']==404){

                $data = Data::sqlRecords('SELECT status FROM `wish_product` WHERE product_id="'.$id.'" AND merchant_id="'.MERCHANT_ID.'" ','one'); 

                $query = Data::sqlRecords('SELECT sku,type,variant_id,w.status FROM `jet_product`j LEFT JOIN `wish_product` as w ON j.bigproduct_id=w.product_id WHERE bigproduct_id="'.$id.'" AND w.merchant_id="'.MERCHANT_ID.'" AND j.merchant_id="'.MERCHANT_ID.'" ','one');            

                //if(isset($query) && !empty($query) && $query['status']!='Not Uploaded'){
                if($query['type']=='variants')
                {
                    $skus = Data::sqlRecords('SELECT option_sku,option_id FROM `jet_product_variants` WHERE product_id="'.$id.'" AND merchant_id="'.MERCHANT_ID.'" ',null,'all');
                   
                    if(!is_array($skus) || (is_array($skus) && !count($skus)))
                        $skus = [];

                }else {
                    $skus[0]['option_sku'] = $query['sku'];
                    $skus[0]['option_id'] = $query['variant_id'];
                }

                foreach ($skus as $sku) {

                    if($query['status']!='Not Uploaded'){
                        $retireProduct = new Wishapi(API_USER, API_PASSWORD, AUTH_KEY);
                        $feed_data = $retireProduct->retireProduct($sku['option_sku']);

                        if($query['type']=='variants'){
                            if($sku['option_id']==$query['variant_id']){
                                $delprod="DELETE FROM `jet_product` WHERE sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                                $model = $connection->createCommand($delprod)->execute();  
                            }
                            $delprod="DELETE FROM `jet_product_variants` WHERE option_sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                            $model = $connection->createCommand($delprod)->execute();  
                        }
                        else{
                            $delprod="DELETE FROM `jet_product` WHERE sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                            $model = $connection->createCommand($delprod)->execute(); 
                        }

                        $retire=1;

                    }

                    else
                    {
                        if($query['type']=='variants'){
                            if($sku['option_id']==$query['variant_id']){
                                $delprod="DELETE FROM `jet_product` WHERE sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                                $model = $connection->createCommand($delprod)->execute();  
                            }
                            $delprod="DELETE FROM `jet_product_variants` WHERE option_sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                            $model = $connection->createCommand($delprod)->execute();  
                        }
                        else{
                            $delprod="DELETE FROM `jet_product` WHERE sku='".$sku['option_sku']."'AND merchant_id='".MERCHANT_ID."'";
                            $model = $connection->createCommand($delprod)->execute(); 
                        }

                        $delete=1;
                    }

                }
                //$returnArr = ['success'=>['count'=>1,'message'=>$prod['title']]];
                $returnArr = ['error' => true, 'message' => "Product doesn't exist on BigCommerce."];
                //$returnArr['error'] = $prod['title'];
            }*/
            Jetproductinfo::updateDetails($prod['data'],$syncFields,MERCHANT_ID,$this->bigcom,false);
        }
        
        return json_encode($returnArr);
    	/*if(!empty($selection))
    	{
    		//foreach ($selection as $id) {
    		$id = $selection[$index];
    
    		$query = Data::sqlRecords('SELECT sku,type,status FROM `jet_product` WHERE bigproduct_id="'.$id.'" AND merchant_id="'.MERCHANT_ID.'" LIMIT 0,1 ','one');

    		if(isset($query['sku']) && !empty($query))
    		{
    			
    			$bigcom = new BigcommerceClientHelper(WALMART_APP_KEY,TOKEN,STOREHASH);
    			
    			$resource='catalog/products/'.$id.'?include=variants,images';
    			 
    			$product= $bigcom->get($resource);
    			
    			if($product['status']==404)
    			{
    				$retireProduct = new Walmartapi(API_USER, API_PASSWORD, CONSUMER_CHANNEL_TYPE_ID);
    				$feed_data = $retireProduct->retireProduct($query['sku']);
    				
    				$delprod="DELETE FROM `jet_product` WHERE bigproduct_id='".$id."'AND merchant_id='".MERCHANT_ID."'";
    				$model = $connection->createCommand($delprod)->execute();
    				$return_msg['error']="Product sku : "."<a href='".Yii::$app->request->baseUrl.'/jetproduct/update?id='.$pid."' target='_blank'>".$product_sku."</a>"." is not available on bigcommerce.";
    				return json_encode($return_msg);
    			}
    			$product_qty=0;
    			$product_sku="";
    			
    			
    			if($product['data'])
    			{
    				foreach ($product as $prod){
    					$noSkuFlag = 0;
    					if($prod['sku']=="") {
    						if($prod['variants'][0]['sku']==""){
    							$noSkuFlag = 1;
    						}
    						//break;
    					}
    					if(!$noSkuFlag){
    						
    						Jetproductinfo::saveNewRecords($prod, MERCHANT_ID, $connection,TOKEN,STOREHASH, true);
    					}
    					$retr['add']='true';
    				}
    				
    			}

                if($query['status']!='Not Uploaded'){
        			if($query['type']=='variants')
        			{
        				$skus = Data::sqlRecords('SELECT option_id,option_sku FROM `jet_product_variants` WHERE product_id="'.$id.'" AND merchant_id="'.MERCHANT_ID.'" ',null,'all');
        
        				if(is_array($skus) && count($skus))
        				{
        					$error = [];
        					foreach ($skus as $sku) {
        
        						$productStatus = new Walmartapi(API_USER, API_PASSWORD, CONSUMER_CHANNEL_TYPE_ID);
        						$feed_data = $productStatus->getItemstatus($sku['option_sku']);
        						if (isset($feed_data['MPItemView'])) {
        
        							$status = $feed_data['MPItemView'][0]['publishedStatus'];
        
        							$query="update walmart_product_variants set status='".$status."' where option_id='".$sku['option_id']."'";
        							Data::sqlRecords($query,null,'update');
        
        						} else {
        
        							$error[] = 'Status Not Updated for variant sku : '.$sku['option_sku'].' of product sku : '.$query['sku'];
        						}
        					}
        
        					if(count($error)) {
        						$returnArr['error'] = implode('<br>', $error);
        					} else {
        						$returnArr = ['success'=>['count'=>1,'message'=> 'Status Successfully Updated']];
        					}
        				}
        				else
        				{
        					$returnArr['error'] = $query['sku'].' Status Not Updated';
        				}
        			} 
                    else{
        
                        //if($query['status']!='Not Uploaded'){
            				$productStatus = new Walmartapi(API_USER, API_PASSWORD, CONSUMER_CHANNEL_TYPE_ID);
            				$feed_data = $productStatus->getItemstatus($query['sku']);
            
            				if (isset($feed_data['MPItemView'])) {
            
            					$status = $feed_data['MPItemView'][0]['publishedStatus'];
            
            					//update main product status(s)
            					$query="update walmart_product set status='".$status."' where product_id='".$id."'";
            					Data::sqlRecords($query,null,'update');
            
            					$returnArr = ['success'=>['count'=>1,'message'=> 'Status Successfully Updated']];
            
            				} else {
            					
            					$returnArr['error'] = $query['sku'].' Status Not Updated';
            				}
                        //}
        			}
                }

                else{
                	
                	//$query="update walmart_product set status='Not Uploaded' where product_id='".$id."'";
                	//Data::sqlRecords($query,null,'update');
                	
                    $returnArr['error'] = $query['sku'].'Not Uploaded to Walmart panel!!'; 
                }
    		}
    		else
    		{
    			$returnArr['error'] = 'Product Id :'.$id.' Not Found';
    		}
    
    		//}
    	}
    */
    	

    }
    
     /**
    * Check Product repricing enable or not.
    * frontend\modules\walmart\components\WalmartRepricing 
    * @return array json
    */
     public function actionCheckrepricing(){
        $return_array=[];
        $product = Yii::$app->request->post();
        unset($product['option_id']);
        $check = WishRepricing::isRepricingEnabled($product);
        if($check){
            $return_array['success']=true;
        }
        else{
            $return_array['success']=false;
        }
        return json_encode($return_array);
        
    }
    //need to modify according bigcom  start=====================================================================

    public function actionSyncproductstore()
    {

        $session = "";
        $session = Yii::$app->session;
        $connection = "";
        $merchant_id = MERCHANT_ID;
        $shopname = SHOP;
        $token = TOKEN;
        $countProducts = 0;
        $pages = 0;
        if($this->bigcom)
            $this->bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);

        $import_option = Data::getConfigValue($merchant_id, 'import_product_option');
        $resource="";

        if($import_option)
        {
            $resource="catalog/products?include_fields=''&is_visible=1&limit=250";

        }
        else
        {
            $resource="catalog/products?include_fields=''&limit=250";
        }
        $productdata = $this->bigcom->call('GET', $resource);

        if(isset($productdata['errors'])){
            $result['err'] = $productdata['errors'];
            return json_encode($result);
        }
        $countProducts=$productdata['meta']['pagination']['total'];
        $pages=$productdata['meta']['pagination']['total_pages'];
        if (!is_object($session)) {
            Yii::$app->session->setFlash('error', "Can't initialize Session.Product(s) Sync cancelled.");
            return $this->redirect(['index']);
        }

        $session->set('product_page', $pages);
        $session->set('bigcom_object', serialize($this->bigcom));
        $session->set('select_value', serialize(Yii::$app->request->post()));
        return $this->render('syncprod', [
            'totalcount' => $countProducts,
            'pages' => $pages
            ]);
    }
    public function actionShopifyproductsync()
    {

        $session = Yii::$app->session;
        $index = Yii::$app->request->post('index');
        $countUpload = Yii::$app->request->post('count');
        $returnArr = $products = array();
        $jProduct = 0;
        try 
        {
            $pages = 0;
            $count=0;
            $pages = $session->get('product_page');
            //$this->bigcom = unserialize($session->get('bigcom_object'));
            $sync = unserialize($session->get('select_value'));
            //parse_str($select_value,$sync);
            $merchant_id = MERCHANT_ID;
            $shopname = SHOP;
            $token = TOKEN;
            if($this->bigcom)
                $this->bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);

            // Get all products
            $limit = 250;
            $import_option = Data::getConfigValue($merchant_id, 'import_product_option');
            if($import_option){
                $resource='catalog/products?include_fields=name,categories,brand_id,sku,price,description,sale_price,upc,inventory_level&include=variants,images&is_visible=1&limit=250&page='.$index;
            }
            else{
                $resource='catalog/products?include_fields=name,categories,brand_id,sku,price,description,sale_price,upc,inventory_level&include=variants,images&limit=250&page='.$index;
            }
            $products = $this->bigcom->call('GET', $resource);


            if(isset($products['errors']))
            {
                $returnArr['error'] = $products['errors'];
                return json_encode($returnArr);
            }
            if (isset($products['data'])) 
            {
                foreach ($products['data'] as $value) 
                {
                    $response = Jetproductinfo::updateDetails($value,$sync,$merchant_id,$this->bigcom,false);

                    $count++;
                    $jProduct+= $response; 
                }
            } 
            else 
            {
                /*return json_encode(['error'=>true, 'message'=>"Product doesn't exist on Shopify."]);*/
                
                $returnArr = ['error' => true, 'message' => "Product doesn't exist on BigCommerce."];
            }
        } catch (Exception $e) {
            /*return json_encode(['error'=>true, 'message'=>"Error : ".$e->getMessage()]);*/
            $returnArr = ['error' => true, 'message' => "Error : " . $e->getMessage()];
        }
        if ($jProduct)
            $returnArr['success']['count'] = $count;

        return json_encode($returnArr);

    }


    public static function getImplodedImages($images)
    {
        if(count($images)>0){
            foreach ($images as $key => $image) {
                if($image['is_thumbnail']==1){
                    $product_images=$image['url_zoom'];
                }
            }
        }
        return $product_images;
    }



    //need to modify according bigcom  end=====================================================================
    public function actionGetpromostatus(){
        $query = "SELECT `product_id` FROM `wish_product` WHERE merchant_id='".MERCHANT_ID."'";
        $productIds = Data::sqlRecords($query, null,'all');
        if(!is_array($productIds) || (is_array($productIds) && !count($productIds)))
            $productIds = [];
        $productIds = array_column($productIds, 'product_id');
        $result = WishPromoStatus::getPromoStatus($productIds);
        if(!$result){
            Yii::$app->session->setFlash('error', "No Product(s) available...");
        }
        if(!is_array($result) && is_string($result)){
            Yii::$app->session->setFlash('error', "No Product(s) promo price available...");
        }
        if(is_array($result) && count($result)==0){
            Yii::$app->session->setFlash('error', "Error Occured. Please try again..");
        }
        if(is_array($result) && count($result)>0 && isset($result['exception']) && strlen($result['exception'])>0){
            Yii::$app->session->setFlash('error', "Error Occured in Processing. Please try again..");
        }elseif(is_array($result) && count($result)>0){
            Yii::$app->session->setFlash('success', "Successfully fetched product(s) promo price status.");
        }
        return $this->redirect(['index']);
    }

    public function actionInsertproduct(){
        $connection = Yii::$app->getDb();
        $merchant_id = MERCHANT_ID;
        $shopname=SHOP;
        $token=TOKEN;
        $store_hash=STOREHASH;
        $countProducts=0;$pages=0;
        $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);
        $resource='catalog/products';
        $countUpload=$bigcom->get($resource);
        $countProducts=$countUpload['meta']['pagination']['total'];
        $pages=$countUpload['meta']['pagination']['total_pages'];
        $resource='catalog/products?include=variants,images&limit=50&page='.$pages.'';
        $products= $bigcom->get($resource); 
        $readyCount = 0;
        $notSku = 0;
        $notType = 0;
        if($products){
            foreach ($products['data'] as $prod){
                $noSkuFlag = 0;
                if($prod['categories'][0]==''){
                    $notType ++;
                    continue;
                }
                if($prod['sku']=="") {
                    $noSkuFlag = 1;
                    $notSku ++;
                    continue;
                        //break;
                }

                if(!$noSkuFlag){
                    $readyCount ++;
                    Jetproductinfo::saveNewRecords($prod, $merchant_id, $connection,TOKEN,STOREHASH, true);
                }

            }
        }
    }

    public function actionRenderCategoryTab()
    {
        $this->layout = "main2";
        $session = Yii::$app->session;
        $html = '';
        $id = Yii::$app->request->post('id');
        if ($id) {
            $session_key = 'product' . $id;
            $product = $session[$session_key];
            $model = $product['model'];
            $category_path = $product['category_path'];
            $attributes = $product['attributes'];
            $optional_attr = $product['optional_attr'];
            $requiredAttrValues = $product['requiredAttrValues'];
            $optionalAttrValues = $product['optionalAttrValues'];
            $common_required_attributes = $product['common_required_attributes'];
            $required = $product['required'];
            $unit_attributes = $product['unit_attributes'];
            $Category = $product['category_data'];

            $html = $this->render('category_tab', [
                'model' => $model,
                'category_path' => $category_path,
                'attributes' => $attributes,
                'optional_attr' => $optional_attr,
                'requiredAttrValues' => $requiredAttrValues,
                'optionalAttrValues' => $optionalAttrValues,
                'common_required_attributes' => $common_required_attributes,
                'required' => $required,
                'unit_attributes' => $unit_attributes,
                'category_data' => $Category
                ]);
        }
        return json_encode(['html' => $html]);
    }
    
    public function actionUpdatewishprice()
    {
    	$product = Yii::$app->request->post();
    	$returnArr = ['error' => true];
    	$count = count($product);
    	$errors = [];

    	if ($count) {
    		$response = $this->wishConfig->updateWishprice($product);
    		if (isset($response['errors'])) {
    			$returnArr = ['error' => "Price Feed Error : Price not updated on wish", 'message' => 'Price for some Products is not updated due to ' . json_encode($response['errors'])];
    		} else {
    			if ($product['type'] == 'simple') {
    				$query = "UPDATE `wish_product` SET `product_price` = '" . $product['price'] . "' WHERE `merchant_id`= '" . MERCHANT_ID . "' AND `id`='" . $product['id'] . "' ";
    			} else {
    				$query = "UPDATE `wish_product_variants` SET `option_prices` = '" . $product['price'] . "' WHERE `merchant_id`= '" . MERCHANT_ID . "' AND `product_id`='" . $product['id'] . "' AND `option_id`='" . $product['option_id'] . "'";
    			}
    			Data::sqlRecords($query, null, 'update');
    			$returnArr = ['success' => true, 'count' => $count];
    		}
    	}
    	return json_encode($returnArr);

    }

    
    public function actionUpdatewishinventory()
    {
        $product = Yii::$app->request->post();
        $returnArr = ['error' => true];
        $count = count($product);
        $errors = [];
        if ($count) {
            $response = $this->wishConfig->updateWishinventory($product);
            if (isset($response['errors'])) {
                $returnArr = ['error' => "Inventory Feed Error : Inventory not updated on wish", 'message' => 'Inventory for some Products is not updated due to ' . json_encode($response['errors'])];
            } else {
                if ($product['type'] == 'simple') {
                    $query = "UPDATE `jet_product` SET `qty` = '" . $product['qty'] . "' WHERE `merchant_id`= '" . MERCHANT_ID . "' AND `bigproduct_id`='" . $product['id'] . "' ";
                } else {
                    $query = "UPDATE `jet_product_variants` SET `option_qty` = '" . $product['qty'] . "' WHERE `merchant_id`= '" . MERCHANT_ID . "' AND `product_id`='" . $product['id'] . "' AND `option_id`='" . $product['option_id'] . "'";
                }
                Data::sqlRecords($query, null, 'update');
                $returnArr = ['success' => true, 'count' => $count];
                return json_encode($returnArr);
            }
        }
        return json_encode($returnArr);
    }

    public function actionAddproductbysku(){
        $sku1=Yii::$app->request->post('sku'); 
        $skuarr=explode(",",$sku1);   
        $connection = Yii::$app->getDb(); 
        foreach($skuarr as $key)           
        {                
            $resource='catalog/products?include=variants,images&sku='.$key;                
            $products= $this->bigcom->call('GET',$resource);  
            $readyCount = $notSku = $count= $notType = 0;                   
            if(count($products['data'])>0)                        
            {                           
                foreach ($products['data'] as $prod){                           
                    $noSkuFlag = 0;
                    if($prod['variants'][0]['sku']==""){                                   
                        $noSkuFlag = 1;                                                                  
                    }                                
                    if(!$noSkuFlag){          
                        $count++; 
                        Jetproductinfo::saveNewRecords($prod, MERCHANT_ID, $connection,false,$this->bigcom);                        
                    }                        
                }                      
            }  
            else{
                $notSku++;            
            }                   
        } 
        if($count)
            $returnArr = ['success'=>['count'=>$count,'message'=>'Product Added successfully']];  
        else
            $returnArr = ['error'=>'Product sku Not Found'];

        return json_encode($returnArr);      
    }
    
    public function actionCustomproductadd()
    {          
        $sku1=Yii::$app->request->post('sku');
        $tmp=Yii::$app->request->post('tmp');        
        $skuarr=explode(",",$sku1);        
        //$id1=Yii::$app->request->post('id');        

        $connection = Yii::$app->getDb();        
        $bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH); 

        if($tmp){
         $tmpdata = Data::sqlRecords("SELECT * FROM `jet_product_tmp` WHERE `merchant_id`='".MERCHANT_ID ."'", 'all', 'select');
			//print_r($tmpdata);die;
         if($tmpdata){
            foreach($tmpdata as $tmp){
               $resource='catalog/products/'.$tmp['product_id'].'?include=variants,images';
               $products= $bigcom->get($resource);  
               if($products['status']!=404){  
                   foreach ($products as $prod){                
                      $noSkuFlag = 0;                                         
                      if($prod['sku']=="") {                        
                         if($prod['variants'][0]['sku']==""){         
                            $noSkuFlag = 1;                                                 
                        }                          
    								//break;                    
                    }                                        
                    if(!$noSkuFlag){                      
                     Jetproductinfo::saveNewRecords($prod, MERCHANT_ID, $connection,TOKEN,STOREHASH, true);        
                 }  
                 $retr['add']='true';
             }
             $delprod="DELETE FROM `jet_product_tmp` WHERE product_id='".$tmp['product_id']."'AND merchant_id='".MERCHANT_ID."'";
             $model = $connection->createCommand($delprod)->execute();   
         }  
         else{
           $retr['add']='false';
       }                                   
   }
}

$tmpdataupdate = Data::sqlRecords("SELECT * FROM `jet_product_tmp` WHERE `merchant_id`='".MERCHANT_ID ."'", 'all', 'select');
$retr=[];
if($tmpdataupdate){
    foreach($tmpdataupdate as $tmpupdate){
        $resource='catalog/products/'.$tmpupdate['product_id'].'?include=variants,images';
        $products= $bigcom->get($resource); 

        if($products['status']!=404){
            foreach ($products as $prod){                
                $noSkuFlag = 0;                                         
                if($prod['sku']=="") {                        
                    if($prod['variants'][0]['sku']==""){         
                        $noSkuFlag = 1;                                                 
                    }                          
                                    //break;                    
                }                                        
                if(!$noSkuFlag){                      
                    Jetproductinfo::saveNewRecords($prod, MERCHANT_ID, $connection,TOKEN,STOREHASH, true);        
                }  
                $retr['add1']='true';
            } 

            $delprod="DELETE FROM `jet_product_tmp` WHERE product_id='".$tmpupdate['product_id']."'AND merchant_id='".MERCHANT_ID."'";
            $model = $connection->createCommand($delprod)->execute();
        } 
        else{
           $retr['add1']='false';
       }
   }
}

return json_encode($retr);
}

if(!$sku1){
			// $sku=$_GET['sku'];        
 $id=$_GET['id'];  
             //$resource='catalog/products?include=variants,images&sku='.$sku; 
 $resource='catalog/products/'.$id.'?include=variants,images';       
			/*if($sku)        
			{           
				$resource='catalog/products?include=variants,images&sku='.$sku;        
			}        
			else{           
				$resource='catalog/products/'.$id.'?include=variants,images'; 
				
			}*/
		}        
		else        
        {         
            foreach($skuarr as $key)           
            {                
                $resource='catalog/products?include=variants,images&sku='.$key;                
                $products= $this->bigcom->call('GET',$resource);  

                $readyCount = 0;                   
                $notSku = 0;                   
                $issku=0;                   
                $notType = 0;  
                
                if(count($products['data'])>0)                        
                {                           
                    foreach ($products['data'] as $prod){                           
                        $noSkuFlag = 0;                                                       

                        if($prod['variants'][0]['sku']==""){                                   
                            $noSkuFlag = 1;                                                                  
                        }                                

                        if(!$noSkuFlag){                                                                                            
                            $listsku['exist'][]=$key;                                
                            Jetproductinfo::saveNewRecords($prod, MERCHANT_ID, $connection,TOKEN,STOREHASH, true);                           
                        }                        
                    }                      
                    
                }  

                else{
                   $listsku['exist'][]='invalid';                
               }                   
           } 

           return json_encode($listsku);      
       }  

       $resource='catalog/products?include=variants,images&limit=50&page=19';
       $products= $this->bigcom->call('GET',$resource);     

       $readyCount = 0;         
       $notSku = 0;        
       $notType = 0;         
       if($products){                  
            //if($id){             
        foreach ($products as $prod){                
            $noSkuFlag = 0;                                         
            if($prod['sku']=="") {                        
                if($prod['variants'][0]['sku']==""){         
                    $noSkuFlag = 1;                                                 
                }                          
                            //break;                    
            }                                        
            if(!$noSkuFlag){                      
                Jetproductinfo::saveNewRecords($prod, MERCHANT_ID, $connection,TOKEN,STOREHASH, true);        
            }              
        }            
           // }   
    }        
    die("done");
    echo "Product has been Added !!!";   
}

public function actionDuplicateproductsdelete()
{
   $connection=Yii::$app->getDb();
   $duplicateProducts=Data::sqlRecords("SELECT bigproduct_id FROM jet_product where merchant_id='".MERCHANT_ID."' GROUP BY bigproduct_id HAVING COUNT(*) > 1
    ",null,'all');

   $duplicateProducts1=Data::sqlRecords("SELECT product_id FROM wish_product where merchant_id='".MERCHANT_ID."' GROUP BY product_id HAVING COUNT(*) > 1
    ",null,'all');

   $duplicateProducts2=Data::sqlRecords("SELECT product_id,option_id FROM wish_product_variants where merchant_id='".MERCHANT_ID."' GROUP BY option_id HAVING COUNT(*) > 1
    ",null,'all');

   foreach($duplicateProducts as $dp){

      $dpcount=Data::sqlRecords("SELECT count(bigproduct_id) as count FROM jet_product where merchant_id='".MERCHANT_ID."' and bigproduct_id='".$dp['bigproduct_id']."'",null,'one');
      $c=$dpcount[0]['count']-1;

      $deldupprod="DELETE FROM `jet_product` WHERE merchant_id='".MERCHANT_ID."' and bigproduct_id='".$dp['bigproduct_id']."' LIMIT ".$c;
      $model = $connection->createCommand($deldupprod)->execute();
  }

  foreach($duplicateProducts1 as $dp){

      $dpcount=Data::sqlRecords("SELECT count(product_id) as count FROM wish_product where merchant_id='".MERCHANT_ID."' and product_id='".$dp['product_id']."'",null,'one');
      $c=$dpcount[0]['count']-1;

      $deldupprod="DELETE FROM `wish_product` WHERE merchant_id='".MERCHANT_ID."' and product_id='".$dp['product_id']."' LIMIT ".$c;
      $model = $connection->createCommand($deldupprod)->execute();

  }

  foreach($duplicateProducts2 as $dp){

    $dpcount=Data::sqlRecords("SELECT count(product_id) as count FROM wish_product_variants where merchant_id='".MERCHANT_ID."' and option_id='".$dp['option_id']."'",null,'one');
    $c=$dpcount[0]['count']-1;

    $deldupprod="DELETE FROM `wish_product_variants` WHERE merchant_id='".MERCHANT_ID."' and option_id='".$dp['option_id']."' LIMIT ".$c;
    $model = $connection->createCommand($deldupprod)->execute();

}
}
}
