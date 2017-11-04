<?php
namespace frontend\modules\wishmarketplace\controllers;

use Yii;
use yii\web\Controller;
use frontend\modules\wishmarketplace\models\WishConfiguration;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\components\Wishappdetails;

class WishApiController extends Controller
{
    public function actionSave()
    {   

        if ($postData = Yii::$app->request->post())
        {
             
            $merchant_id = Yii::$app->user->identity->id;
            $client_id = trim($postData['client_id']);

            $client_secret_key = trim($postData['client_secret_key']);
            $redirect_url = trim($postData['redirect_url']);
            // print_r($c);die();
            // $consumer_channel_type_id = "7b2c8dab-c79c-4cee-97fb-0ac399e17ade";
            // $code = trim($postData['code']);
            
            if($client_id == "" || $client_secret_key == "" || $redirect_url == "") {
                return json_encode(['error'=>true, "message"=>"Api credentials are invalid. Please enter valid api credentials"]);
            }
            
            if(!Wishappdetails::validateApiCredentials($client_id, $client_secret_key,$redirect_url)) {
                return json_encode(['error'=>true, "message"=>"Api credentials are invalid. Please enter valid api credentials"]);
            }

            //Check if Details are already used by some other merchant
            $data = Data::sqlRecords("SELECT `merchant_id` FROM `wish_configuration` WHERE `client_id`='".$client_id."' AND `client_secret_key`='".$client_secret_key."'", 'one');
            if($data && isset($data['merchant_id']) && $data['merchant_id'] != $merchant_id) {
                return json_encode(['error'=>true, "message"=>"Api Credentials are already in use."]);
            }

            $result = WishConfiguration::find()->where(['merchant_id'=>$merchant_id])->one();

            $model = new WishConfiguration();
            if(is_null($result)) {
                $model->merchant_id = $merchant_id;
                $model->client_id = $client_id;
                $model->client_secret_key = $client_secret_key;
                $model->redirect_url = $redirect_url;
                $model->save(false);
            } else {
                $model->client_id = $client_id;
                $model->client_secret_key = $client_secret_key;
                $model->redirect_url =$redirect_url;
                // $model->code = $code;
                $model->save(false);
            }
            // print_r($model->save); echo "anj";
            return json_encode(['success'=>true, "message"=>"Wish Configurations has been Saved Successfully!"]);
        }
        return json_encode(['error'=>true, "message"=>"Api credentials are invalid. Please enter valid api credentials"]); 
    }
}

