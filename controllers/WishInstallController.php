<?php 
namespace frontend\modules\wishmarketplace\controllers;

use Yii;
use yii\web\Controller;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\components\Installation;
use frontend\modules\wishmarketplace\components\WishCategory;
use frontend\modules\wishmarketplace\components\AttributeMap;
use frontend\modules\wishmarketplace\components\Jetproductinfo;
use frontend\modules\wishmarketplace\models\WishInstallation;
use frontend\modules\wishmarketplace\models\WishAttributeMap;

class WishInstallController extends WishmainController
{
	protected $bigcom, $wishConfig;
	public function beforeAction($action)
    {
    	$this->enableCsrfValidation = false;
    	return parent::beforeAction($action);
	}

	public function actionIndex()
	{
		$this->layout = 'blank';

		$step = Yii::$app->request->get('step',false);
		if(!$step) {
			$installation = Installation::isInstallationComplete(MERCHANT_ID);
            if($installation) {
                if($installation['status'] == 'pending') {
                    $step = (int)$installation['step'];
                    $step = $step+1;
                } else {
                	$this->redirect(Data::getUrl('site/index'),302);
                    return false;
                }
            } else {
                $step = Installation::getFirstStep();
            }
		}

		return $this->render('installation', ['currentStep'=>$step]);
	}

	public function actionRenderstep()
	{
		$this->layout = 'main2';
		$category='';
		$brand='';
		// print_r($this->bigcom->call('GET', 'catalog/categories/tree'));die("category");
   		Jetproductinfo::saveBigcomcategory($category,MERCHANT_ID);
   		Jetproductinfo::savebigcombrand(MERCHANT_ID,$this->bigcom);

		$stepId = Yii::$app->request->post('step',false);
		if($stepId)
		{
			$stepInfo = Installation::getStepInfo($stepId);
			if(!isset($stepInfo['error'])) {
				$templateFile = $stepInfo['template'];
				$html = $this->renderAjax($templateFile,[],true);
				return json_encode(['success'=>true,'content'=>$html,'steptitle'=>$stepInfo['name']]);
			} else {
				return json_encode(['error'=>true,'message'=>'Invalid Step Id.']);
			}
		}
		else
		{
			return json_encode(['error'=>true,'message'=>'Invalid Step Id.']);
		}
	}

	public function actionSavestep()
	{
		$stepId = Yii::$app->request->post('step',false);
		if($stepId)
		{
			try
			{
				$model = WishInstallation::find()->where(['merchant_id'=>MERCHANT_ID])->one();
		        if(is_null($model)) {
		            $model = new WishInstallation();
		            $model->merchant_id = MERCHANT_ID;
		        }
		        
		        if($stepId == Installation::getFinalStep())
		            $model->status = Installation::INSTALLATION_STATUS_COMPLETE;
		        else 
		            $model->status = Installation::INSTALLATION_STATUS_PENDING;

		        $model->step = $stepId;
		        $model->save();

		        return json_encode(['success'=>true,'message'=>'Saved Successfully!!']);
	    	}
	    	catch(Exception $e) {
	    		return json_encode(['error'=>true,'message'=>$e->getMessage()]);
	    	}
		}
		else
		{
			return json_encode(['error'=>true,'message'=>'Invalid Step Id.']);
		}
	}
	
	public function actionHelp()
	{
		$this->layout = 'blank';
		if(isset($_GET['step'])){
			return $this->render('help/step_'.$_GET['step'], ['step'=>$_GET['step']]);
		}
	}

	public function actionCheckProgressStatus()
	{
		$userData=Data::sqlRecords("SELECT id FROM user","all","select");
		if(is_array($userData) && count($userData)>0)
		{
			foreach ($userData as $value) 
			{
				$step=Installation::getCompletedStepId($value['id']);
				//check & save progress steps of each merchant
				$installedCollection=Data::sqlRecords("SELECT `id` FROM `jet_installation` WHERE merchant_id=".$value['id']." limit 0,1","one","select");
				if(!$installedCollection){
					echo "merchant_id:".$value['id']." step:".$step."<br>";
				}
			}
		}
	}

	public function actionSaveCategoryMap()
	{
		if (Yii::$app->user->isGuest) {
    		return json_encode(['error'=>true, 'message'=>'Please Login to Continue']);
    	}
    	$merchant_id = MERCHANT_ID;
    	$data = Yii::$app->request->post();
        print_t($data);die("wishinstallcontroller");
    	if($data && isset($data['type']))
    	{
    		foreach($data['type'] as $key=>$value)
    		{
    			$category_path="";
    			$category_id="";
    			$key=stripslashes($key);
    			if(is_array($value) && count($value)>0 && $value[0]!="")
    			{
    				// $taxcode = isset($value['taxcode'])?$value['taxcode']:'';
    				// unset($value['taxcode']);
					// $category_path = implode(',',$value);
					// $category_path = rtrim($category_path,',');
					// $category_id = end($value);
					// if($category_id == "Other")
					//     $category_id = $value[0];
                    $category_path = $value['0'].",".$value['1'];
                    $category_id = $value['1'];
                    $category_name = $value['2'];
                    $parent_category = $value['0'];
                    // $category_name = WishCategory::getCategoryName($category_id);
                    $query1 = 'UPDATE `wish_category_map` SET category_name="'.$category_name.'", category_id="'.trim($category_id).'",category_path="'.trim($category_path).'" where merchant_id="'.$merchant_id.'" and product_type="'.$key.'"';
		            $model = Data::sqlRecords($query1, null, 'update');
		            
		            $query2 = 'UPDATE `wish_product` SET  category="'.$category_id.'",`parent_category`="'.$parent_category.'" where merchant_id="'.$merchant_id.'" and product_type="'.$key.'"';
		            $product = Data::sqlRecords($query2, null, 'update');
    			}
    			else
    			{
                    // $taxcode = isset($value['taxcode'])?$value['taxcode']:'';
                    $query1 = 'UPDATE `wish_category_map` SET  category_name="", category_id="",category_path="" where merchant_id="'.$merchant_id.'" and product_type="'.$key.'"';
                    $model = Data::sqlRecords($query1, null, 'update');
                    
                    $query2 = 'UPDATE `wish_product` SET  category="" where merchant_id="'.$merchant_id.'" and product_type="'.$key.'"';
                    $product = Data::sqlRecords($query2, null, 'update');
  				    continue;
 			    }
    		}
            unset($data);
    		return json_encode(['success'=>true, "message"=>"Wish Categories are mapped successfully with Product Type"]);
    	}
    	else
    	{
    		return json_encode(['error'=>true, 'message'=>'Cannot Save Data.']);
    	}
	}

	public function actionSaveAttributeMap()
	{
		if (Yii::$app->user->isGuest) {
    		return json_encode(['error'=>true, 'message'=>'Please Login to Continue']);
    	}
    	$data = Yii::$app->request->post();
    	// print_r($data);die();
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

                return json_encode(['success'=>true, 'message'=>"Attributes Have been Mapped Successfully!!"]);
            } else {
            	return json_encode(['success'=>true, 'message'=>"No Attributes to Save."]);
            }
        }
        else
        {
        	return json_encode(['success'=>true, 'message'=>"Attributes not Mapped."]);
        }
    }
}	