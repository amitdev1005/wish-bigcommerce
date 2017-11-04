<?php

namespace frontend\modules\wishmarketplace\controllers;
use frontend\modules\wishmarketplace\models\WalmartConfig;
use frontend\modules\wishmarketplace\models\WishConfiguration;
use Yii;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\controllers\WishmainController;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use frontend\modules\wishmarketplace\components\Wishapi;
use frontend\modules\wishmarketplace\components\Walmartappdetails;

/**
 * WalmartconfigurationController implements the CRUD actions for WalmartConfiguration model.
 */
class WishconfigurationController extends WishmainController
{
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

    /**
     * Lists all WalmartConfiguration models.
     * @return mixed
     */
    public function actionIndex()
    {
        $clientData=array();
        $isConfigurationExist=array();
        $api_url='';
        $fullfillment_node='';
        $consumer_channel_type_id='';
        $first_address='';
        $second_address='';
        $city='';
        $state='';
        $zipcode='';
        $auth_key='';
        

        if ($postData = Yii::$app->request->post())
        {
            


            $query="SELECT * FROM `wish_email_template`";
            $email = Data::sqlRecords($query,"all");
            
            foreach ($email as $key => $value) {
                $emailConfiguration['email/'.$value['template_title']] = isset($_POST['email/'.$value["template_title"]])  ? 1 : 0;
            }

            $client_id = trim($_POST['client_id']);
            $client_secret_key = trim($_POST['client_secret_key']);
            $wish_code=trim($_POST['code']);

            $wish_token=$this->getToken($client_id,$client_secret_key,$wish_code);

            if(!$wish_token['success']){

                Yii::$app->session->setFlash('error',$wish_token['message']);

            }
            else{
                $token=$wish_token['token'];

            }
            /*$access_token=trim($_POST['access_token']);

            if(!$access_token){
                 $access_token='';
            }*/


          /*  $sale_price=$_POST['sale_price'];
            $upload_product_without_quantity=$_POST['upload_product_without_quantity'];
            $inc_dcr=$_POST['custom_price_type'];

            $import_product_option=$_POST['import_product_option'];

           // $productTax_code=$_POST['tax_code'];
            //$consumer_channel_type_id = trim($_POST['consumer_channel_type_id']);
            $auth_key = trim($_POST['auth_key']);

            $skype_id=trim($_POST['skype_id']);*/

             $isConfigurationExist = Data::sqlRecords("SELECT * FROM  wish_configuration WHERE `merchant_id`='".MERCHANT_ID."' ", "one", "select");
            if (!empty($isConfigurationExist)){
                Data::sqlRecords("UPDATE `wish_configuration` SET `client_id`='".$client_id."',`client_secret_key`='".$client_secret_key."',`code`='".$wish_code."', `access_token`='".$token."' where `merchant_id`='".MERCHANT_ID."'", null, "update");
            } else{                
                //save api credentials
                Data::sqlRecords("INSERT INTO `wish_configuration` (`merchant_id`, `client_id`,`client_secret_key`,`code`,`access_token`) values(".MERCHANT_ID.",'".$client_id."','".$client_secret_key."','".$wish_code."','".$token."') ", null, "insert");
            }

                        
            //Check if Credentials are valid
            /*if(!Walmartappdetails::validateApiCredentials($api_url, $fullfillment_node))
            {
                Yii::$app->session->setFlash('error', "Api credentials are invalid. Please enter valid api credentials");
                return $this->render('index', ['clientData' => $postData]);
            }
            else
            {
                $isConfigurationExist = Data::sqlRecords("SELECT `api_url` FROM  wish_configuration WHERE `merchant_id`='".MERCHANT_ID."' ", "one", "select");
                if (!empty($isConfigurationExist)){
                    Data::sqlRecords("UPDATE `wish_configuration` SET `api_url`='".$api_url."',`fullfillment_node`='".$fullfillment_node."',`auth_key`='".$auth_key."' where `merchant_id`='".MERCHANT_ID."'", null, "update");
                } else{                
                    //save api credentials
                    Data::sqlRecords("INSERT INTO `wish_configuration` (`merchant_id`, `api_url`,`fullfillment_node`,`auth_key`) values(".MERCHANT_ID.",'".$api_url."','".$fullfillment_node."','".$auth_key."') ", null, "insert");
                }
            }*/

           /* $isCustomPrice=false;
            if(isset($postData['updateprice']))
            { 
                $isCustomPrice=true;
                if($postData['updateprice']=="yes" && isset($postData['custom_price'],$postData['updateprice_value']) && $postData['updateprice_value'] && is_numeric($postData['updateprice_value']))
                    $postData['custom_price']=$inc_dcr.'-'.$postData['custom_price'].'-'.$postData['updateprice_value'];
                else
                    $postData['custom_price']="";
            }

            if(isset($postData['sync_product_enable']) && $postData['sync_product_enable']=='enable' && isset($postData['sync-fields']))
            {
                $sync_values = json_encode($postData['sync-fields']);
                $postData['sync-fields'] = $sync_values;
            }else{
                $postData['sync-fields'] = '';
            }*/

            /*$postData['sale_price']=$sale_price;
            $postData['upload_product_without_quantity']=$upload_product_without_quantity;
            $postData['import_product_option']=$import_product_option;
*/
            /*if($isCustomPrice)
                $configFields = $configFields = ['first_address', 'second_address', 'city', 'state', 'zipcode','tax_code','remove_free_shipping','custom_price','ordersync','inventory','sale_price','sync_product_enable','sync-fields','import_product_option','upload_product_without_quantity'];
           
            else
                $configFields = ['first_address', 'second_address', 'city', 'state', 'zipcode','tax_code','ordersync','remove_free_shipping','inventory','sale_price','sync_product_enable','sync-fields','import_product_option','upload_product_without_quantity'];
            */
            /* Save Email Subscription Setting */
           /* if(!empty($emailConfiguration)){
                 foreach ($emailConfiguration as $key => $value) 
                    {
                       $emaildata=Data::sqlRecords("Select * from wish_config where data='".$key."' and merchant_id='".MERCHANT_ID."'",null,"select");

                       if($emaildata)
                            Data::sqlRecords("UPDATE `wish_config` SET `value`='".$value."' where `merchant_id`='".MERCHANT_ID."' AND `data`='".$key."'", null, "update");
                       else
                            Data::sqlRecords("INSERT into `wish_config` (`merchant_id`,`data`,`value`) values('".MERCHANT_ID."','".$key."','".$value."')", null, "insert");

                    }
            }
*/
            /* End */
            //print_r($postData);die();
            /*foreach ($postData as $key => $value) 
            { 
                if(in_array($key, $configFields)) 
                {   
                    Data::saveConfigValue(MERCHANT_ID, $key, $value);
                }
            }*/
            $clientData = $postData;

             // Data::sqlRecords("UPDATE `wish_category_map` SET `tax_code`='".$productTax_code."' where `merchant_id`='".MERCHANT_ID."'", null, "update");

            Yii::$app->session->setFlash('success','Wish Configurations has been Saved Successfully!');
        } 
        else 
        {
            $walmart_configuration_data = Data::sqlRecords("SELECT `client_id`,`client_secret_key`,`code`,`access_token` FROM  wish_configuration WHERE `merchant_id`='".MERCHANT_ID."' ","one");
            $walmart_config_data = Data::sqlRecords("SELECT `data`,`value` FROM  walmart_config WHERE `merchant_id`='".MERCHANT_ID."' ","all","select");
                            
            $clientData['client_id']=$walmart_configuration_data['client_id'];
            $clientData['client_secret_key']=$walmart_configuration_data['client_secret_key'];
            $clientData['code']=$walmart_configuration_data['code'];
            $clientData['access_token']=$walmart_configuration_data['access_token'];
            $clientData['first_address']=$first_address;
            $clientData['second_address']=$second_address;
            $clientData['city']=$city;
            $clientData['state']=$state;
            $clientData['zipcode']=$zipcode;
           // $clientData['skype_id']=$skype_id;
            if (!empty($walmart_config_data))
            {
                foreach ($walmart_config_data as $val)
                {

                     $clientData[$val['data']] = $val['value'];
                }

            }
        }
       
        
        return $this->render('index', ['clientData' => $clientData]);
    }

    public function getToken($client_id,$client_secret,$code){

       // https://merchant.wish.com/api/v2/oauth/access_token?client_id=59957f5835e73b36f9a308da&client_secret=a21fa03d71c1426b9d33c76892f0ffd7&code=f8552350ab5f4f909d77756819ce2564&grant_type=authorization_code&redirect_uri=https://www.cedcommerce.com
        $path = 'oauth/access_token';
        $redirect_uri='https://www.cedcommerce.com';
        $params = array(
          'client_id'=> trim($client_id),
          'client_secret'=> trim($client_secret),
          'code'=> trim($code),
          'grant_type'=>'authorization_code',
          'redirect_uri'=> trim($redirect_uri));

        $url = 'https://merchant.wish.com/api/v2/oauth/access_token'; 
        $curl = curl_init();
        $options = array(
          CURLOPT_CONNECTTIMEOUT => 10,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
        );
        $url = $url."?".http_build_query($params);

         echo $url;die;

        /*$options = array(CURLOPT_URL => $url,
                 CURLOPT_HEADER => false
                );*/
        $options[CURLOPT_URL] = $url;
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);


        $error = curl_errno($curl);
        $request = json_decode($response,true);


        if (is_array($request)) {
            if($request['code']==4000){
              return array('success'=>false ,'message'=> 'Unauthorized access.');
            }
            if($request['code']==1016){
              return array('success'=>false ,'message'=> 'Access code expired.');
            }
            if($request['code']==1018){
              return array('success'=>false ,'message'=> $request['message']);
            }
            if($request['code']==0){
               $data = $request['data'];
               return array('success'=>true ,'token'=>$data['access_token']);
            }
            return array('success'=>true ,$request);
        }
    }

    /**
     * Displays a single WalmartConfiguration model.
     * @param integer $id
     * @return mixed
     */
    public function actionGetcode(){
     die("dgfgf");
     }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new WalmartConfiguration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WishConfiguration();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WalmartConfiguration model.
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
     * Deletes an existing WalmartConfiguration model.
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
     * Finds the WalmartConfiguration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WalmartConfiguration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WishConfiguration::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

