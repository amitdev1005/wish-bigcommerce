<?php
namespace frontend\modules\wishmarketplace\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->redirect('site/index');
    }
}
