<?php
namespace frontend\modules\wishmarketplace\controllers;

use frontend\modules\wishmarketplace\components\Data;
use yii\helpers\Url;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
/**
 * FaqController
 */
class FaqController extends WishmainController
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
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest) {
            return \Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl);
        }

        try{
            $resultdata=array();
            $query="SELECT * FROM `wish_faq` ";        
            $resultdata = Data::sqlRecords($query,"all","select");
            
            return $this->render('index', [
                'data'=>$resultdata 
            ]);  
        }
        catch(Exception $e)
        {
            echo $e->getMessage();die;
        }     
    }

}    