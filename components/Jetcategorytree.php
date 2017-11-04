<?php 
namespace frontend\modules\wishmarketplace\components;
use Yii;
use yii\base\Component;
use frontend\modules\wishmarketplace\models\WishCategory;

class Jetcategorytree extends component
{
    
	public static function createCategoryTreeArray($data="")
    {
		if($data =="" || (is_array($data) && count($data)==0))
		{
		// 	/*$data=WishCategory::find()->all();*/
            $maincategoryroute = Yii::getAlias('@webroot').'/WISHTAXONOMY/Wish_Taxonomy.csv';
            $str = file_get_contents($maincategoryroute);
            $model = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $str));
            foreach($model as $key=>$val){
            if($key==0){
                    foreach($val as $k=>$v)
                    {
                        if($k==0)
                        {
                            $category_id=$v;
                        }
                        if($k==2)
                        {
                            $subcategory_id=$v;
                        }
                        if($k==4)
                        {
                            $minicategory_id=$v;
                        }
                    }
                    continue;
                }
                else
                {  
                    if(isset($val['0']))
                        $catedata=$val['2']; //Category_name
                    if(isset($val['2']))
                        $subdata=$val['2']; // SubCategory_name
                    if(isset($val['4']))
                        $minidata=$val['4']; // MiniCategory_name

                    // $h=preg_replace('/[^A-Za-z0-9\-]/', '', $subname); 
                    // $subname=preg_replace('/[-]/','', $h);

                    if(isset($val['1']))
                        $indname =$val['1']; // Industry_name
                    
                    $maincategoryroute = Yii::getAlias('@webroot').'/NeweggCategoryXSD/XSD/'.$subdata.'_'.$subname.'.xsd';
                    $get = file_get_contents($maincategoryroute);
                    $parseObj = str_replace(':',"",$get);
                    $arr = simplexml_load_string($parseObj);
                    $category_tree=$arr;
                }
            } 
            $return_arr[] = $category_tree;
            return $return_arr;
            // $session->set('newegg_subcategory_'.addslashes($key), $model);
		}
	// 	$category_tree=array();
 //        $category_detail=array();
 //        $return_arr=array();
 //        foreach($data as $val)
 //        {
 //            if($val->level==0){
 //                $category_tree[trim($val->category_id)]=array();
 //            }
 //            $category_detail[trim($val->category_id)]=trim($val->title);
 //        }
 //        foreach($data as $val)
 //        {
 //            if($val->level==1){
 //                $parent_id="";
 //                $parent_id=trim($val->parent_id);
 //                if(array_key_exists($parent_id, $category_tree)){
 //                        $category_tree[$parent_id][trim($val->category_id)]=array();
 //                }

 //            }
 //        }
 //        foreach($data as $val){
 //            if($val->level==2){
 //                $parent_id="";
 //                $parent_id=trim($val->parent_id);
 //                foreach($category_tree as $par_key=>$sub_child_arr){
 //                    if(array_key_exists($parent_id, $sub_child_arr)){
 //                        $category_tree[$par_key][$parent_id][trim($val->category_id)]='';
 //                    }
 //                }
 //            }
 //        } 
 //        $return_arr[]=$category_tree;
 //        $return_arr[]=$category_detail;
 //        return $return_arr;
	// }
    /*public static function createCategoryPath($product_type="",$category_id="",$category_tree=array(),$merchant_id="")
    {
        $product_collection=array();
        if(trim($category_id)==""|| trim($category_id)==0)
        {
            $product_collection=JetProduct::find()->select('fulfillment_node')->where(['merchant_id'=>$merchant_id,'product_type'=>$product_type])->all();
            if(count($product_collection)>0)
            {
                $first_value="";
                $first_model="";
                $first_model=$product_collection[0];
                $first_value=$first_model->fulfillment_node;
                $count=0;
                foreach($product_collection as $model){
                    if(trim($model->fulfillment_node) == trim($first_value)){
                        $count++;
                    }
                     
                }
                if($count==count($product_collection)){
                    $category_id=trim($first_value);
                }
            }
        }
        if(trim($category_id)==0 || trim($category_id)=="")
        {
            return "";
        }
        $path_array=array();
        $new_array=array();
        $path_array=self::getkeypath($category_tree, trim($category_id));
        if(is_array($path_array) && count($path_array)>0)
        {
            $new_array=array_reverse($path_array);
            return $new_array;
        }
        else
        {
            return "";
        }
    }*/
    }
    public static function getkeypath($arr, $lookup)
    {
        if (array_key_exists($lookup, $arr))
        {
            return array($lookup);
        }
        else
        {
            foreach ($arr as $key => $subarr)
            {
                if (is_array($subarr))
                {
                    $ret = getkeypath($subarr, $lookup);
                    if ($ret)
                    {
                        $ret[] = $key;
                        return $ret;
                    }
                }
            }
        }
        return null;
    }
}
?>