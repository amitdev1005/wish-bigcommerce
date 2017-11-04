<?php 
namespace frontend\modules\wishmarketplace\controllers;

use frontend\modules\wishmarketplace\components\Wishappdetails;
use Yii;
use yii\web\Controller;
use frontend\modules\wishmarketplace\components\Wishapi;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\components\Installation;
use frontend\modules\wishmarketplace\components\BigcommerceClientHelper;


class WishmainController extends Controller
{

    protected $bigcom;
    protected $wishConfig;
    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        $this->layout = 'main';
        if(!Yii::$app->user->isGuest)
        {
            if(!defined('MERCHANT_ID') || Yii::$app->user->identity->id!=MERCHANT_ID)
            {
                $merchant_id = Yii::$app->user->identity->id;
                $shopDetails = Data::getWishShopDetails($merchant_id);
               // print_r($shopDetails);die("fghfg");
                $token = isset($shopDetails['token'])?$shopDetails['token']:'';
                $email = isset($shopDetails['email'])?$shopDetails['email']:'';
                $currency= isset($shopDetails['currency'])?$shopDetails['currency']:'USD';
                $shop = Yii::$app->user->identity->username;
                $store_hash=isset($shopDetails['store_hash'])?$shopDetails['store_hash']:Yii::$app->user->identity->store_hash;

                define("MERCHANT_ID", $merchant_id);
                define("SHOP", $shop);
                define("TOKEN", $token);
                define("CURRENCY", $currency);
                define("STOREHASH",$store_hash);
                $wishConfig=[];
                $wishConfig = Data::sqlRecords("SELECT `client_id`,`client_secret_key`,`code`,`access_token` FROM `wish_configuration` WHERE merchant_id='".MERCHANT_ID."'", 'one');
            
                    if(isset($wishConfig))
                    {
                        define("CLIENT_ID",$wishConfig['client_id']);
                        define("CLIENT_SECRET_KEY",$wishConfig['client_secret_key']);
                        define("CODE",$wishConfig['code']);
                        define("ACCESS_TOKEN",$wishConfig['access_token']);
                       
                        $this->wishConfig = new Wishapi(CLIENT_ID,CLIENT_SECRET_KEY,CODE,ACCESS_TOKEN);
                    }
            }    
            $this->bigcom = new BigcommerceClientHelper(WISH_APP_KEY,TOKEN,STOREHASH);


            $auth = Wishappdetails::authoriseAppDetails($merchant_id, $shop);

            if(isset($auth['status']) && !$auth['status'])
            {
                if(!Wishappdetails::appstatus($shop))
                    $this->redirect('https://www.bigcommerce.com/apps/wish-marketplace-integration/');
                elseif(isset($auth['purchase_status']) && 
                    ($auth['purchase_status']=='license_expired' || $auth['purchase_status']=='trial_expired')) {
                    $url = yii::$app->request->baseUrl.'/wish-marketplace/paymentplan';
                    return $this->redirect($url);
                }
                else {
                    Yii::$app->session->setFlash('error', $auth['message']);
                    $this->redirect(Data::getUrl('site/logout'));
                    return false;
                }
            }
            //Code By Himanshu Start
            if(Yii::$app->controller->id != 'wish-install' && Yii::$app->controller->id != 'wishtaxcodes')
            {
                Installation::completeInstallationForOldMerchants(MERCHANT_ID);
                
                $installation = Installation::isInstallationComplete(MERCHANT_ID);
                if($installation) {
                    if($installation['status'] == Installation::INSTALLATION_STATUS_PENDING) {
                        $step = $installation['step'];
                        //$this->redirect(Yii::$app->getUrlManager()->getBaseUrl().'/jet-install/index?step='.$step,302);
                        $this->redirect(Data::getUrl('wish-install/index'),302);
                        return false;
                    }
                } else {
                    $step = Installation::getFirstStep();
                    //$this->redirect(Yii::$app->getUrlManager()->getBaseUrl().'/jet-install/index?step='.$step,302);
                    $this->redirect(Data::getUrl('wish-install/index'),302);
                    return false;
                }

                if(!Wishappdetails::isAppConfigured($merchant_id) &&
                Yii::$app->controller->id != 'wishconfiguration')
                {
                    $msg='Please activate wish api(s) to start integration with Wish';
                    Yii::$app->session->setFlash('error', $msg);
                    $this->redirect(Data::getUrl('site/index'));
                    return false;
                }
            }
            //Code By Himanshu End

            return true;
        }
        else
        {
            if($_SERVER['SERVER_NAME'] =='bigcommerce.cedcommerce.com'){
                $unsuscribe = $_SERVER['QUERY_STRING'];
                Yii::$app->session->set('redirect_url', $unsuscribe);
                $this->redirect(Data::getUrl('site/index')); 
                return false;
            }
            $this->redirect(Data::getUrl('site/index')); 
            return false;
        }
    }
}
?>
