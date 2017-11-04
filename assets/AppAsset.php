<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\modules\wishmarketplace\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
  
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'web/css/site.css',
        //'css/jquery.treeview.css',
        //'css/jquery-checktree.css',
        'frontend/modules/wishmarketplace/assets/css/creative.css',
        'frontend/modules/wishmarketplace/assets/css/jquery.datetimepicker.css',
        'frontend/modules/wishmarketplace/assets/css/site.css',
        'frontend/modules/wishmarketplace/assets/css/font-awesome.min.css',
        'frontend/modules/wishmarketplace/assets/css/jQuery-plugin-progressbar.css',
        'frontend/modules/wishmarketplace/assets/css/bootstrap-material-design.css',
        'frontend/modules/wishmarketplace/assets/css/bootstrap.css',
        'frontend/modules/wishmarketplace/assets/css/pie-chart.css',
        'frontend/modules/wishmarketplace/assets/css/owl.carousel.css',
        //'css/slick.css',
        //'css/slick-theme.css',
        'frontend/modules/wishmarketplace/assets/css/style.css',
        'frontend/modules/wishmarketplace/assets/css/introjs.css',
        'frontend/modules/wishmarketplace/assets/css/intro-themes/introjs-nazanin.css',
        'frontend/modules/wishmarketplace/assets/css/litebox.css',
        'frontend/modules/wishmarketplace/assets/css/jquery-ui.css',
        'frontend/modules/wishmarketplace/assets/css/jquery-ui-timepicker-addon.css',
        'frontend/modules/wishmarketplace/assets/css/alertify/alertify.css'
    ];
    public $js = [
        //'js/jquery.touchSwipe.min.js',
        //'js/jquery-1.10.2.min.js',
        //['js/jquery.js', ['position'=>1]],
        'frontend/modules/wishmarketplace/assets/js/bootstrap.min.js',
        'frontend/modules/wishmarketplace/assets/js/owl.carousel.js',
        'frontend/modules/wishmarketplace/assets/js/owl.carousel.min.js',
        //'js/jQuery-plugin-progressbar.js',
        'frontend/modules/wishmarketplace/assets/js/custom.js',
        //'js/slick.js',
        'frontend/modules/wishmarketplace/assets/js/intro.js',
        'frontend/modules/wishmarketplace/assets/js/pie-chart.js',
        //'js/images-loaded.min.js',
        //'js/litebox.min.js',
        //'js/alertify/alertify.js',
        'frontend/modules/wishmarketplace/assets/js/jquery-ui.js',
        'frontend/modules/wishmarketplace/assets/js/alertify/alertify.js',
        'frontend/modules/wishmarketplace/assets/js/jquery-ui-timepicker-addon.js',
        'frontend/modules/wishmarketplace/assets/js/moment.min.js',
        'frontend/modules/wishmarketplace/assets/js/combodate.js',
        'frontend/modules/wishmarketplace/assets/js/nicEdit.js'

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
