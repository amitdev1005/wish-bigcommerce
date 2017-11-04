<?php
use yii\helpers\Html;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\models\WishAttributeMap;
use frontend\modules\wishmarketplace\components\AttributeMap;

$this->title = 'BigCommerce-Wish Attribute Mapping';
$this->params['breadcrumbs'][] = $this->title;

$attributes = [];
$bigcom_product_types = AttributeMap::getBigcomProductTypes();
// print_r($bigcom_product_types);die();
foreach ($bigcom_product_types as $type_arr) 
{
    $product_type = $type_arr['product_type'];
    $WishCategoryId = $type_arr['category_id'];
    $parent_id = explode(",",$type_arr['category_path']);
    $category_name = $type_arr['category_name'];
    $wishAttributes = [];
    if(!is_null($category_name)) {
        $wishAttributes = AttributeMap::getWishCategoryAttributes($category_name,$parent_id[0])?:[];
    }
    $bigcomAttributes = AttributeMap::getBigcomProductAttributes($product_type);
    $mapped_values = AttributeMap::getAttributeMapValues($product_type);
    $attributes[$product_type] = [
                                    'product_type' => $product_type,
                                    'wish_attributes' => $wishAttributes,
                                    'bigcom_attributes' => $bigcomAttributes,
                                    'mapped_values' => $mapped_values
                                ];
}
?>
<div class="attribute-map-step attribute-map-index content-section">
	<div class="">
	     <p class="note">
	     <b class="note-text">Note:</b> If you got variations for your products, map wish attributes with your product variant options, or Click <b>Next</b> </p>
    </div>
	<form id="attribute_map" method="post" action="<?= Data::getUrl('wish-attributemap/save') ?>">
		<div style="display: none;border-radius: 4px;margin-bottom: 10px;padding: 10px;" class="help-block help-block-error top_error alert-danger"></div>
		<input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
		<div class="grid-view">
			<table class="table table-striped table-bordered ced-walmart-custome-tbl">
				<thead>
					<tr>
						<th>Product Type(Bigcommerce)</th>
						<!-- <th>Wish Category</th> -->
						<th>Wish-Bigcommerce Attributes</th>
						<!-- <th>Walmart Attribute Value</th> -->
					</tr>
				</thead>
				<tbody>
					<?php 
					if(count($attributes)) {
					  	$noAttrFlag = false;
					  	foreach ($attributes as $key=>$attribute) {
							$options = [];
							if(isset($attribute['bigcom_attributes']) && count($attribute['bigcom_attributes'])){
								foreach ($attribute['bigcom_attributes'] as $bigcom_attr) {
									$options[] = $bigcom_attr;
								}
							}
							if(count($attribute['wish_attributes']['attributes']))
							{
								// $required_attributes = $attribute['walmart_attributes']['required_attrs'];
								//$attribute['walmart_attributes']['attribute_values']['unit'] = AttributeMap::getUnitAttributeValues();
					?>
								<tr>
									<td><?= $attribute['product_type'] ?></td>

									<!-- <td colspan="2"> -->
									<td>
										<table class="attrmap-inner-table">
											<tr>
												<th>Wish</th>
												<th>Bigcommerce</th>
											</tr>
											<?php
											foreach($attribute['wish_attributes']['attributes'] as $wal_attr=>$val) {
												$mapped_value = '';
												$valueType = '';
												if(isset($attribute['mapped_values'][$wal_attr])) {
													$valueType = $attribute['mapped_values'][$wal_attr]['type'];
													$mapped_value = $attribute['mapped_values'][$wal_attr]['value'];
												}
											?>
												<tr>
													<td>
													<?php
														$selectTypeFlag = false;
														$walmart_attribute_values = "";
														if(isset($attribute['wish_attributes']['attribute_values'][$wal_attr])) {
															$selectTypeFlag = true;
															$walmart_attribute_values[] = $attribute['wish_attributes']['attributes'][$wal_attr];
														}

														// if(!$selectTypeFlag && strpos($wal_attr, AttributeMap::ATTRIBUTE_PATH_SEPERATOR) !== false)
														// {
														// 	$wal_attrs = explode(AttributeMap::ATTRIBUTE_PATH_SEPERATOR, $wal_attr);
														// 	if(is_array($wal_attrs)) {
														// 		foreach ($wal_attrs as $_wal_attr) {
														// 			if(isset($attribute['wish_attributes']['attribute_values'][$_wal_attr])) {
														// 				$selectTypeFlag = true;
														// 				$walmart_attribute = $attribute['wish_attributes']['attributes'][$_wal_attr];
														// 			}
														// 		}
														// 	}
														// }


														if($selectTypeFlag) {
													?>
															<select name="walmart[<?= $attribute['product_type']?>][<?= $wal_attr ?>]">
																<option value=""></option>
													<?php   
															$walAttrValue = explode(',', json_encode($walmart_attribute_values));
										
															foreach($walmart_attribute_values as $value) {
													?>
																<option value="<?= $value ?>" 
																<?php if(in_array($value,explode(',', $mapped_value)) && $valueType==WishAttributeMap::VALUE_TYPE_WISH)
																	  { echo 'selected="selected"'; } ?>>
																<?= $value ?>
																 </option> 

													<?php
															}
													?>
															</select>
															<!-- <span class="text-validator">Select Value of '<?= $wal_attr ?>' attribute which will be used for all products under '<?= $attribute['product_type']; ?>' Product type.</span> -->
														<?php
														} else {
																if(count($options)) {
														?>
											<!-- Comment the Multi Select and Convert it into Checkboxes -->
																<select multiple name="walmart[<?= $attribute['product_type']?>][<?= $wal_attr ?>][]">
											<?php
																  foreach ($options as $option) {
											?>
																	<option value="<?= $option ?>" 
																	<?php if(in_array($option,explode(',', $mapped_value)) && $valueType==WishAttributeMap::VALUE_TYPE_BIGCOM)
																			{ echo 'selected="selected"'; } ?>
																	><?= $option ?></option>
											<?php
																	
																  }
											?>
																</select> 

															<!-- Code for Checkbox Start -->
																	<?php
																	foreach ($options as $option) {
																	?>							
																		<div class="checkbox_options">
																			<input type="checkbox" value="<?= $option ?>" name="walmart[<?= $attribute['product_type']?>][<?= $wal_attr ?>][]" <?php if(in_array($option,explode(',', $mapped_value)) && $valueType==WishAttributeMap::VALUE_TYPE_BIGCOM)
																					{ echo 'checked="checked"'; } ?>>
																			<label><?= $option ?></label>
																		</div>
																	<?php		
																	}
																	?>
																	<div class="clear"></div>
															<!-- End -->
																
																	<!-- <span class="text-validator">Select the appropriate shopify options for '<?= $wal_attr ?>' attribute from the above list of Bigcommerce options.</span> -->
															   		<div class="signle-line-1"><span>OR</span>
															   		</div>
																<?php 
																}
																?>
															   <p>
																<input type="text" value="<?php if($valueType==WishAttributeMap::VALUE_TYPE_TEXT){echo $mapped_value;} ?>" 
																name="walmart[<?= $attribute['product_type']?>][<?= $wal_attr ?>][text]" />
															   	<!-- <span class="text-validator">Enter any value for '<?= $wal_attr ?>' attribute if there is no similar option in Bigcommerce.</span> -->
															   </p>
														<?php
														}
														?>
													</td>
													<td>
													<?php
														foreach ($options as $option) {
													?>							
															<div class="checkbox_options">
																<input type="checkbox" value="<?= $option ?>" name="walmart[<?= $attribute['product_type']?>][<?= $wal_attr ?>][]" <?php if(in_array($option,explode(',', $mapped_value)) && $valueType==WishAttributeMap::VALUE_TYPE_BIGCOM)
																		{ echo 'checked="checked"'; } ?>>
																<label><?= $option ?></label>
															</div>
													<?php		
														}
													?>
													</td>
												</tr>	
											<?php
											}
						?>
										</table>
									</td>
								</tr>
						<?php
							}
							elseif(!$noAttrFlag)
							{
								$noAttrFlag = true;
						?>
								<tr>
									<td colspan="3">No Mapped Wish Category has Required Attributes to Map.</td>
								</tr>
						<?php
							}
					 	 }
					}
					else
					{
					?>
							<tr>
								<td colspan="3">Bigcommerce Product Types are not Mapped With Wish Categories.</td>
							</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<input type="button" class="btn btn-primary next" value="Next">
		</div>	
	</form>
</div>
<style>
	.center,.cat_root{
		text-align: center;
	}
	.cat_root .form-control{
		display: inline-block;
	}

	.attrmap-inner-table .checkbox_options {
	    float: left;
	    margin-right: 8px;
	    min-width: 30%;
	}
	.attrmap-inner-table tr td {
    	width: auto;
	}
	.attrmap-inner-table tr td:first-child {
    	width: auto;
	}
	.signle-line-1 {
	  border-bottom: 1px solid #d4d4d4;
	  margin-bottom: 25px;
	  text-align: center;
	}
	.signle-line-1 span {
	  background: #fff none repeat scroll 0 0;
	  display: inline-block;
	  position: relative;
	  top: 8px;
	  width: 32px;
	  font-weight: bold;
	}
</style>

<script type="text/javascript">
$(document).ready(function(){
	var url = '<?= Data::getUrl("wish-install/save-attribute-map"); ?>';
	UnbindNextClick();
	$('.next').on('click', function(event){	
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
				nextStep();
	        } else {
				$('.top_error').html(response.message);
				$('.top_error').show();
	        }
		});
	});
});
</script>