<?php
use yii\helpers\Html;
use frontend\modules\wishmarketplace\models\WishCategory;
use frontend\modules\wishmarketplace\models\WishCategoryMap;
use frontend\modules\wishmarketplace\components\Jetcategorytree;
use frontend\modules\wishmarketplace\components\Data;
use yii\web\Session;

$this->title = 'BigCommerce-Wish Category Mapping';
$this->params['breadcrumbs'][] = $this->title;

$merchant_id = Yii::$app->user->identity->id;
$model = WishCategoryMap::find()->where(['merchant_id'=>$merchant_id])->andWhere(['!=','product_type',''])->all();
// $data = WishCategory::find()->select('id,category_id,title,parent_id,level')->all();

$maincategoryroute = Yii::getAlias('@webroot').'/WISHTAXONOMY/Wish_Taxonomy.csv';
$str = file_get_contents($maincategoryroute);
$maincategory = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $str));
$session = new Session();
$session->open();
$session['main_category']=$maincategory;
$category=array();
$category_tree=array();
$category_tree_data=array();
$head_data=array();
$category=$maincategory;
$length=count($category);
for($i=1;$i<=$length;$i=$i+1)
{
    if($i==0 && $i<=$length)
    {
        $head_data=$category[$i];
    }
    else
    {   if($i<=$length)
        {
            if(isset($category[$i]))
            {
                $category_tree_middle[]=$category[$i];
            }
        }
        
    }
}
$category_tree_data=$category_tree_middle;
$subid=array();
$indname=array();

//Get All SubCategory_Id From CSV File

foreach($category_tree_data as $key=>$value)
{

    if(isset($category_tree_data[$key]['4']));
    {

        $subid[]=$value['1'];

    }
}
print_r(count($subid));die;
$subid_len= count($subid);
$extra_array = array();
$category_array = array_fill(0, $subid_len, '');
 // print_r($category_array);die("sdfsg");
$category_tree_array= array_combine($subid,$category_array);
print_r($category_tree_array);die();
// Add an Empty SubArray
foreach($category_tree_array as $k=>$v)
{
    $category_tree_array[$v]=$extra_array;
}

// Get all Industry Name From CSV File
$checkindname=array();
foreach($category_tree_data as $key=>$value)
{
	if(isset($value['1']))
	{
		if(in_array($value['1'],$checkindname))
		{
		    continue;
		}
		else
		{
		    $indname[]=$value['1'];
		    $checkindname[]=$value['1'];
		}
	}
}
$indname_len=count($indname);
$checkdata = array();
$category_array1 = array_fill(0, $indname_len, $category_tree_array);

$category_indname= array_combine($indname,$category_array1);
$subname=array();
$checksubname=array();

// Getting All SubCategory_Name
foreach($indname as $k=>$v)
{
    foreach($category_tree_data as $key=>$value)
    {
        if(isset($value['4']))
        {
        	if(isset($value['1']))
        	{
		        if($value['1']==$v)
		        {
		            if(in_array($v,$checksubname))
		            {
		                $subname[$value['4']]=$value['3'];
		                $checksubname[]=$v;
		            }
		            else
		            {
		                $subname[]=$v;
		                $subname[$value['4']]=$value['3'];
		                $checksubname[]=$v;
		            }
		        }
		    }
        }

    }
}
// $category_tree = array();
// $category_detail = array();
// list($category_tree,$category_detail) = Jetcategorytree::createCategoryTreeArray($data);
?>

<script type="text/javascript">
	var category_tree=<?=json_encode($category_indname)?>;
	var category_detail=<?=json_encode($subname)?>;
	function selectChild(node,level,path_str,type) {
		alert("hey baby");
		var global_level="";
		var global_type="";
		var global_path_str="";
		var global_type_str="";
		var select="";
		var options="";
		var path_arr=[];
		var node_val=$(node).val();
		var option_name="";
		if(level==1){
				if(node_val==""){
						$(node).parent('td').parent('tr').find('td.cat_child').css('display','none');
						$(node).parent('td').parent('tr').find('td.cat_sub_child').css('display','none');
						$(node).parent('td').parent('tr').find('td.cat_sub_child').html("");
						$(node).parent('td').parent('tr').find('td.cat_child').html("");
						return true;
				}
				if(path_str !=""){
						path_arr=path_str.split(',');
				}
				global_level=level+1;
				global_path_str="'"+path_str+"'";
				global_type_str="'"+type.replace(/'/g, "\\'")+"'";
				
				options="";
				select="";
				$.each(category_tree, function(first_key, first_arr) {
                if(first_key==node_val && ($.type(first_arr) === "object")){
                	// alert(first_arr);
                    select='<select name="type['+type+'][]" class="form-control"  onchange="selectChild(this,'+global_level+','+global_path_str+','+global_type_str+')">';
                    $.each(first_arr, function(sec_key, sec_arr) {
                    	alert(sec_key);
                    	alert("hello");
                        option_name="";
                        $.each(category_detail, function(cat_id, cat_name) {
                        	alert(cat_name);
                        	alert("hello1");
                            if(cat_id==sec_key){

                                option_name=cat_name;
                                return false;
                            }
                        });
                        if(option_name !=""){
                            if($.type(path_arr)==="array" && path_arr.length>level){
                                if(sec_key==path_arr['0']){
                                	// alert(path_arr[level]);
                                    options+="<option selected='selected' value='"+path_arr['0']+"'>"+path_arr['1']+"</option>";   /*sec_key,option_name*/
                                }else{
                                    options+="<option value='"+sec_key+"'>"+option_name+"</option>";
                                }
                            }else{
                                options+="<option value='"+sec_key+"'>"+option_name+"</option>";
                            }
                        }
                    });
                    if(options !=""){
                        select+=options;
                        select+="</select>";
                    }else{
                        select="";
                    }
                    return false;
                }
            });
				// $.each(category_tree, function(first_key, first_arr) {
				// 	if(first_key==node_val && ($.type(first_arr) === "object")){
				// 		select='<select name="type['+type+'][]" class="form-control"  onchange="selectChild(this,'+global_level+','+global_path_str+','+global_type_str+')">';
				// 		//select="<select name='type["+type+"][]' class='form-control'  onchange='selectChild(this,"+global_level+","+global_path_str+","+global_type_str+")'>";
				// 			$.each(first_arr, function(sec_key, sec_arr) {
				// 					option_name="";
				// 					$.each(category_detail, function(cat_id, cat_name) {
				// 							if(cat_id==sec_key){
				// 								option_name=cat_name;
				// 								return false;
				// 							}
				// 					});
				// 					if(option_name !=""){
				// 						if($.type(path_arr)==="array" && path_arr.length>level){
				// 							if(sec_key==path_arr[level]){
				// 								options+="<option selected='selected' value='"+sec_key+"'>"+option_name+"</option>";
				// 							}else{
				// 								options+="<option value='"+sec_key+"'>"+option_name+"</option>";
				// 							}
				// 						}else{
				// 							options+="<option value='"+sec_key+"'>"+option_name+"</option>";
				// 						}
											
				// 					}
				// 			});
				// 		if(options !=""){
				// 			select+=options;
				// 			select+="</select>";
				// 		}else{
				// 			select="";
				// 		}
				// 		return false;
				//     }
				// });
			
			$(node).parent('td').parent('tr').find('td.cat_child').html("");
		    $(node).parent('td').parent('tr').find('td.cat_sub_child').html("");
		    $(node).parent('td').parent('tr').find('td.cat_child').html(select);
		    if(select ==""){
		    	$(node).parent('td').parent('tr').find('td.cat_child').css('display','none');
		    }else{
		    	$(node).parent('td').parent('tr').find('td.cat_child').css('display','table-cell');
			    if($(node).parent('td').parent('tr').find('td.cat_child').children('select').length){
			          $(node).parent('td').parent('tr').find('td.cat_child').children('select').trigger('change');
			    }
		    }
		    //kshitij
		    if(node_val!="" && $(node).hasClass('select_error')){
		    	$(node).removeClass('select_error');
		    	$(node).next('div').children('.error_category_map').css('display','none');
			}
		    	
		}
		/*else if(level==2){
				
				var check_complete=false;
				options="";
				select="";
				if(path_str !=""){
						path_arr=path_str.split(',');
				}
				$.each(category_tree, function(first_key, first_arr) {
					$.each(first_arr, function(sec_key, sec_arr) {
							if(sec_key==node_val && ($.type(sec_arr) === "object")){
								select='<select name="type['+type+'][]" class="form-control">';
								//select="<select name='type["+type+"][]' class='form-control'>";
								$.each(sec_arr, function(third_key, third_value) {
										option_name="";
										$.each(category_detail, function(cat_id, cat_name) {
													if(cat_id==third_key){
														option_name=cat_name;
														return false;
													}
										});
										if(option_name !=""){
											if($.type(path_arr)==="array" && path_arr.length>level){
												if(third_key==path_arr[level]){
													options+="<option selected='selected' value='"+third_key+"'>"+option_name+"</option>";
												}else{
													options+="<option value='"+third_key+"'>"+option_name+"</option>";
												}
											}else{
												options+="<option value='"+third_key+"'>"+option_name+"</option>";
											}
										}
								});
								if(options !=""){
									select+=options;
									select+="</select>";
								}else{
									select="";
								}
								check_complete=true;
								return false;	
							}
				    });
				    if(check_complete){
				    	return false;	
				    }
				});
				
				$(node).parent('td').parent('tr').find('td.cat_sub_child').html("");
		        $(node).parent('td').parent('tr').find('td.cat_sub_child').html(select);
		        if(select == ""){
		        	$(node).parent('td').parent('tr').find('td.cat_sub_child').css('display','none');
		        }else{
		        	$(node).parent('td').parent('tr').find('td.cat_sub_child').css('display','table-cell');
		        }
		        
		} */

}
</script>


<div class="category-map-step category-map-index content-section">
	
    <!-- <div class="clear"></div>  -->
    <form id="category_map" method="post" action="<?php echo \yii\helpers\Url::toRoute(['categorymap/save']) ?>">
		<input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
		<div style="display: none;border-radius: 4px;margin-bottom: 10px;padding: 10px;" class="help-block help-block-error top_error alert-danger">Please map atleast one product type with Wish category to list products</div>
		<div class="responsive-table-wrap">
			<table id="map_producttype" class="table table-striped table-bordered">
				<tr>
					<th>Id</th>
					<th>Product Category(bigcommerce)</th>
					<!-- <th style="width:25%;">Wish Tax Code <a href="<?= Data::getUrl('wishtaxcodes/index');?>" target="_blank">(click here to get taxcode)</a></th> -->
					<th class="center" colspan="3"><label class="label-text-center">Wish Category Name</label></th>
				</tr>
				<?php 
				$i=0;
				foreach($model as $value){
					$i++;?>
					<?php $is_selected=false;?>
					<tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $value->product_type; ?></td>
						<!-- <td>
							<input type="text" name="type[<?=trim($value->product_type)?>][taxcode]" value="<?= $value->tax_code ?>" />
						</td> -->
						<?php $category_path=array();?>
						<?php $category_path_str="";?>
						<?php $category_path_str=$value->category_path;?>
						<?php if(trim($value->category_path)!=""){?>
							<?php $category_path=explode(',',$value->category_path);?>
						<?php }?>
			  			<td class="cat_root">
							<select id="select_<?=$i?>" name="type[<?=trim($value->product_type)?>][]" style="width:auto" class="form-control root" onchange="selectChild(this,1,<?php echo "'".trim($category_path_str)."'";?>,<?php echo "'".trim(addslashes($value->product_type))."'";?>)">
								<option value="">Please Select Category</option>
								<?php
								$allcategories=array();
								foreach($maincategory as $key=>$val)
                                {  
	                                if($key==0)
	                                {
	                                    continue;
	                                }
	                                else{  
	                                        if(isset($val['0']))
	                                        {
	                                            if(in_array($val['0'],$allcategories))
	                                                continue;
	                                            else
	                                            {
	                                               if($value->category_id==$val['1']){
	                                                ?>
	                                                <?php $is_selected=true;?>
	                                                <option value="<?=$val['0'];?>" selected="selected"><?=$val['1'];?></option>
	                                            <?php }else{
	                                                ?>
	                                                <option onselect="selectChild('','',<?php echo "'".$val['1']."'"?>,'','','')" value="<?=$val['1'];?>"><?=$val['1'];?></option>
	                                            <?php } 
	                                            }
	                                            $allcategories[]=$val['0'];
	                                        }
	                                    }
	                                }
								/*foreach($data as $val)
								{
									if($val->level==0){
										if(count($category_path)>0 && $category_path[0]==$val->category_id){?>	
											<?php $is_selected=true;?>						
											<option value="<?=$val->category_id;?>" selected="selected"><?=$val->title;?></option>
									<?php }else{?>	
											<option value="<?=$val->category_id;?>"><?=$val->title;?></option>
									<?php }
									}
								}*/
								?>
							</select>
							<?php if($is_selected){?>
									<script type="text/javascript">
										$(document).ready(function(){
											$("#select_<?=$i?>").trigger('change');
										});
									</script>
							<?php }?>
						</td>	
						<td style="display:none;" class="cat_child"></td>
						<td style="display:none;" class="cat_sub_child"></td>
					</tr>
					<?php 
				}
				?>
			</table>
			<input type="button" data-position="left" data-intro="save mapped categories" data-step="5" class="btn btn-primary next" value="Next">
		</div>	
	</form>
</div>
<script type="text/javascript">
var url = '<?= Data::getUrl("wish-install/save-category-map") ?>';
var csrfToken = $('meta[name="csrf-token"]').attr("content");

UnbindNextClick();

	$('.next').on('click', function(event){	
		var flag = false;
		$('.cat_root .root').each(function(){
			if($(this).val() == "") {
				$(this).addClass("select_error");
				$('.top_error').html("Please map atleast one product type with Wish category to list products");
				$('.top_error').show();
			} else {
				flag = true;
				$(this).removeClass("select_error");
				$('.top_error').hide();
				return false;
			}
	  	});
	  	if(flag)
	  	{
	  		$('#LoadingMSG').show(); 
		    $.ajax({
	            method: "POST",
	            url: url,
	            data: $("form").serialize(),
	            dataType : "json"
		    })
		    .done(function(response)
		    {
		        $('#LoadingMSG').hide();
		        if(response.success) {
					$('.top_error').hide();
					nextStep();
		        } else {
					$('.top_error').html(response.message);
					$('.top_error').show();
		        }
			});
	  	}
	});

function checkBeforeSave(){
	var selector_arr=[];
	var rows_value_arr=[];
	var value_str="";
	var first_select="";
	var second_select="";
	var third_select="";
	var stop_form=false;
	<?php $i=1;?>
	<?php foreach($model as $value){?>
			selector_arr.push("#select_<?=$i?>");
	<?php $i++;?>
	<?php }?>
		for(j=0;j<selector_arr.length;j++){
				first_select="";
				second_select="";
				third_select="";
				first_select=$(selector_arr[j]);
				value_str=first_select.val();
				second_select=$(selector_arr[j]).parent().parent('tr').find('td.cat_child').children('select');
				third_select=$(selector_arr[j]).parent().parent('tr').find('td.cat_sub_child').children('select');
				if(second_select.length){
					value_str=value_str+","+second_select.val();
				}
				if(third_select.length){
					value_str=value_str+","+third_select.val();
				}
				rows_value_arr.push(value_str);
		}
		for(j=0;j<rows_value_arr.length;j++){
			for(u=j+1;u<rows_value_arr.length;u++){
					if(rows_value_arr[j]==rows_value_arr[u] && rows_value_arr[u]!=""){
						stop_form=true;
						return false;
					}
			}
			if(stop_form){
				return false;
			}
		}
		if(stop_form){
			return false;
		}
		return true;
}
$('#category_map').submit(function( event ) {
	  var flag=false;
	  $('.cat_root .root').each(function(){
		 if($(this).val()==""){
		  	flag=true;
		  	$(this).addClass("select_error");
		  	//$('.error_category_map').css('display','block');
		 }
		 else{
			 flag=false;
			 $(this).removeClass("select_error");
			 //$('.error_category_map').css('display','none');
			 return false;
		 }
	  });
	  if(flag){
		  return false;
	  }
});
</script>
<style>
	.center,.cat_root{
		text-align: center;
	}
	.cat_root .form-control{
		display: inline-block;
	}
	
</style>