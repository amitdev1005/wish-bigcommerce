<?php
use frontend\integration\models\WishConfiguration;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\modules\wishmarketplace\components\Data;

$this->title = 'Wish Marketplace Configurations';
$this->params['breadcrumbs'][] = $this->title;
$isPrice=false;
$priceType = "";
$priceValue = "";
$priceValueType = "";
/*$query = "SELECT * FROM `wish_email_template` where show_on_admin_setting=1";
$email = Data::sqlRecords($query,"all");
*/
if (isset($clientData['ordersync']) && $clientData['ordersync'] == 'no') {
    $ordersync = true;
}

if(isset($clientData['custom_price']) && $clientData['custom_price'])
{
	$pricData=explode('-',$clientData['custom_price']);
	if(is_array($pricData) && count($pricData)>0)
	{

		if($pricData[0]=="Increase")
			$$priceValueType  = "Increase";
		else
			$$priceValueType  = "Decrease";

		if($pricData[1]=="fixed")
			$priceType = "fixed";
		else
			$priceType = "percent";

		$priceValue = $pricData[2];

		$isPrice=true;
	}
}
?>
<script>
</script>
<div class="jet-configuration-index content-section">
    <div class="jet_configuration form new-section">
    
   <?php $form = ActiveForm::begin([
	    'id' => 'wish_config',
	    'action' => \yii\helpers\Url::toRoute(['wishconfiguration/index']),
		'method'=>'post',
	    'options' => ['name'=>'wish_configupdate'],
	])?>
		<input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
               value="<?= Yii::$app->request->csrfToken; ?>"/>
      <div class="jet-pages-heading">
        <div class="title-need-help">
            <h1 class="Jet_Products_style"><?= Html::encode($this->title) ?></h1>
            <a class="help_jet" target="_blank" href="https://bigcommerce.cedcommerce.com/integration/walmart-marketplace/sell-on-walmart"
               title="Need Help"></a>
        </div>
        <div class="product-upload-menu">
        	<button type="button" id="instant-help" class="btn btn-primary">Help</button>
            <input type="submit" name="submit" value="save" class="btn btn-primary">
        </div>
            <div class="clear"></div>
        </div>
    	<div class="ced-entry-heading-wrapper">
    		<div class="entry-edit-head">
				<h4 class="fieldset-legend">Wish Setting</h4>
			</div>
			<div class="fieldset enable_api" id="api-section">
				
				<table class="table table-striped table-bordered" cellspacing="0">
	                <tbody>
		                <tr>
		                    <td class="value_label">
		                        <span>Wish Client Id</span>
		                    </td>
		                    <td>
		                    	<span><input id="consumer_id" type="text" name="client_id" value="<?=$clientData['client_id'];?>" class="form-control"></span>
		                    </td>
	                    </tr>
	                    <tr>
		                    <td class="value_label">
		                        <span>Wish Secret key</span>
		                    </td>
		                    <td>
                                <span><input id="secret_key" type="text" name="client_secret_key" value="<?=$clientData['client_secret_key'];?>" class="form-control"></span>
		                    	
		                    </td>
	                    </tr>

                        <tr>
                            <td class="value_label">
                                <a href="https://merchant.wish.com/oauth/authorize?client_id=<?=$clientData['client_id']?>" target="_blank">Generate code</a>
                            </td>
                            <td>
                               <span><input id="code" type="text" name="code" value="<?=$clientData['code'];?>" class="form-control"></span>

                            </td>
                        </tr>
	                  
	                </tbody>
                </table>
			</div>
    	</div>
    	<div class="ced-entry-heading-wrapper">
    		<div class="entry-edit-head">
	    		<h4 class="fieldset-legend">Wish Return Location</h4>
	    	</div>
	    	<div class="fieldset walmart-configuration-index">
	    		<table class="table table-striped table-bordered" id="return-location-section" cellspacing="0">
	                <tbody>
		                <tr>
		                    <td class="value_label">
		                        <span>First Address</span>
		                    </td>
		                    <td>
		                    	<span><input type="text" id="first_address" name="first_address" value="<?= $clientData['first_address'];?>" class="form-control"></span>
		                    </td>
	                    </tr>
	                    <tr>
		                    <td class="value_label">
		                        <span>Second Address</span>
		                    </td>
		                    <td>
		                    	<span><input type="text" id="second_address" name="second_address" value="<?= $clientData['second_address'] ?>" class="form-control"></span>
		                    </td>
	                    </tr>
	                    <tr>
		                    <td class="value_label">
		                        <span>City</span>
		                    </td>
		                    <td>
		                    	<span><input type="text" id="city" name="city" value="<?= $clientData['city'] ?>" class="form-control" ></span>
		                    </td>
	                    </tr>
	                    <tr>
		                    <td class="value_label">
		                        <span>State </span>
		                    </td>
		                    <td>
		                    	<span><input type="text" id="state" name="state" value="<?=  $clientData['state'] ?>" class="form-control"></span>
		                    </td>
	                    </tr>
	                    <tr>
		                    <td class="value_label">
		                        <span>Zip Code</span>
		                    </td>
		                    <td>
		                    	<span><input type="text" id="zipcode" name="zipcode" value="<?= $clientData['zipcode'] ?>" class="form-control"></span>
		                    </td>
	                    </tr>
	                    <tr>
		                    <td class="value_label">
		                        <span>Skype Id (Optional)</span>
		                    </td>
		                    <td>
		                    	<span><input type="text" id="skype_id" name="skype_id" value="" class="form-control"></span>
		                    </td>
	                    </tr>
	                </tbody>
                </table>
			</div>
    	</div>
    	  
		<?php ActiveForm::end(); ?>
	</div>
</div>
<style>
    .jet-configuration-index .value_label {
        width: 50%;
    }
.jet-configuration-index .table-striped select,.jet-configuration-index .form-control{
    width: 100%;
    display: inline-block;
    padding-left: 10px;
}
    .jet-configuration-index .value {
    border: medium none !important;
    display: inline-block;
    width: 100%;
}
#custom_price_csv span, #custom_title_csv span{
    width: 85%;
    display: inline-block;
}
   .jet-configuration-index .help_jet{
        display: inline-block;
   }
   .help_jet {
    width: 50px!important;
}
.jet-configuration-index .value_label {
    width: 50%;
}

</style>
<script>
	function priceChange(node)
	{
		var val=j$(node).val();
		if(val=='yes')
		{
			$('#update_price_val').children('select').prop('disabled', false);
			$('#update_price_val').children('input').prop('disabled', false);
			$('#update_price_val').show();
            $('#dynamicpricing').show();
		}
		else
		{
			$('#update_price_val').children('select').prop('disabled', true);
			$('#update_price_val').children('input').prop('disabled', true);
			$('#update_price_val').hide();
            $('#dynamicpricing').hide();
		}
	}
	$('#walmart_config').submit(function( event ) 
	{
		//alert($('#update_price_val').children('input').val());
		if($('#update_price_val').children('select').is(":not(:disabled)") && ($('#update_price_val').children('input').val()=="" || ($('#update_price_val').children('input').val()!="" && !$.isNumeric($('#update_price_val').children('input').val()))))
		{
			event.preventDefault();
			alert("Please fill valid price value, otherwise set 'No' Custom Pricing");
		}
	});


     function productsync(node) {
        var val = j$(node).val();
        if (val == 'enable') {
            $('#sync_product_options').show();
            $('#all-sync-fields-checkbox').prop('checked', true);
            $('.sync-fields-checkbox').prop('checked', true);

            /*$('#all-sync-fields-checkbox').prop('checked', true);
                if (!$('#all-sync-fields-checkbox').is(':checked')) {
                    $('.sync-fields-checkbox').prop('checked', false);
                }
                else{
                    $('.sync-fields-checkbox').prop('checked', true);
                }*/
        }
        else {
            $('#sync_product_options').hide();
        }
    }

</script>

<script type="text/javascript">
       $(document).ready(function () {
        var prod_sync = "<?php if(isset($clientData['sync_product_enable']) && $clientData['sync_product_enable'] == 'enable'){
            echo 'enable';
        } elseif(isset($clientData['sync_product_enable']) && $clientData['sync_product_enable']=='disable'){
            echo 'disable';
        }else{
            echo '';
        }?>";
        var flag = true;
        if(prod_sync=='')
        {
            $('#all-sync-fields-checkbox').prop('checked', true);
            $('.sync-fields-checkbox').prop('checked', true);
            flag=false;
        }

        if(flag){
            if(prod_sync=='enable')
            {
                var sync_field = '<?= isset($clientData['sync-fields']) ? $clientData['sync-fields'] : ''?>';
                if(sync_field != ''){
                    var fields = JSON.parse(sync_field);
                    var counter = 0 ;
                    $.each(fields['sync-fields'], function (index,value){
                        counter++;
                        $('#'+index+'').prop('checked', true);
                    });
                    if(counter=='11'){
                        $('#all-sync-fields-checkbox').prop('checked', true);
                    }
                }
            }else{
                $('#sync_product_options').hide();
            }
        }

        $('#all-sync-fields-checkbox').click(function() {
            if (!$(this).is(':checked')) {
                $('.sync-fields-checkbox').prop('checked', false);
            }
            else{
                $('.sync-fields-checkbox').prop('checked', true);
            }
        });
        
        $('.checkbox_options').click(function() {
            $("input:checkbox[class=sync-fields-checkbox]").each(function () {
                if (!$(this).is(':checked')) {
                    $('#all-sync-fields-checkbox').prop('checked', false);
                    return false;
                }
                else{
                    $('#all-sync-fields-checkbox').prop('checked', true);
                }
            });

        });
    });

</script>

  <script type="text/javascript">
    $('#instant-help').click(function(){
        var configQuicktour = introJs().setOptions({
                doneLabel: 'Finish',
                showStepNumbers: false,
                exitOnOverlayClick: false,
                steps: [
                  {
                    element: '#api-section',
                    intro: 'Edit the Walmart API details.',
                    position: 'bottom'
                  },
                 /* {
                    element: '#fba-integration-section',
                    intro: 'Check FBA Fulfillment settings.',
                    position: 'bottom'
                  },*/
                  {
                    element: '#return-location-section',
                    intro: 'Update Walmart Return Location Address.',
                    position: 'bottom'
                  },
                  {
                    element: '#product-setting',
                    intro: 'Globally apply product tax code,set custom product price and remove free shipping from all product',
                    position: 'bottom'
                  },
              /*    {
                    element: '#cancel-order-section',
                    intro: 'Manage Cancel Order Setting.',
                    position: 'bottom'
                  },*/
             /*     {
                    element: '#custom-pricing-section',
                    intro: 'Manage Product custom/dynamic pricing.',
                    position: 'bottom'
                  },*/
             /*     {
                    element: '#custom_price_csv_field',
                    intro: 'Select "Yes" to update price of each product.',
                    position: 'left'
                  },
                  {
                    element: '#custom_price_csv_label',
                    intro: "Get CSV file by clicking 'CLICK HERE'.",
                    position: 'bottom'
                  },
                  {
                    element: '#custom-title-section',
                    intro: 'Manage Product custom/dynamic Title Setting.',
                    position: 'bottom'
                  },
                  {
                    element: '#custom_title_csv_field',
                    intro: 'Select "Yes" to update title.',
                    position: 'left'
                  },
                  {
                    element: '#custom_title_csv_label',
                    intro: "Get CSV file by clicking 'CLICK HERE'.",
                    position: 'bottom'
                  },*/
                  {
                    element: '#email-subscription-section',
                    intro: 'Update Email Subscription Setting from here. Check the corresponding Checkbox to receive Mails and Uncheck to Not Receive Mails.',
                    position: 'bottom'
                  }
                ]
            });

            configQuicktour.start().oncomplete(function() {
              window.location.href = '<?= Data::getUrl("site/index") ?>';
          });
      });
  </script>

  <?php $get = Yii::$app->request->get();
  if(isset($get['tour'])) : 
?>
  <script type="text/javascript">
    $(document).ready(function(){
        var configQuicktour = introJs().setOptions({
                doneLabel: 'Finish',
                showStepNumbers: false,
                exitOnOverlayClick: false,
                    steps: [
                  {
                    element: '#api-section',
                    intro: 'Edit the Walmart API details.',
                    position: 'bottom'
                  },
                 /* {
                    element: '#fba-integration-section',
                    intro: 'Check FBA Fulfillment settings.',
                    position: 'bottom'
                  },*/
                  {
                    element: '#return-location-section',
                    intro: 'Update Walmart Return Location Address.',
                    position: 'bottom'
                  },
                  {
                    element: '#product-setting',
                    intro: 'Globally apply product tax code,set custom product price and remove free shipping from all product',
                    position: 'bottom'
                  },
              /*    {
                    element: '#cancel-order-section',
                    intro: 'Manage Cancel Order Setting.',
                    position: 'bottom'
                  },*/
             /*     {
                    element: '#custom-pricing-section',
                    intro: 'Manage Product custom/dynamic pricing.',
                    position: 'bottom'
                  },*/
             /*     {
                    element: '#custom_price_csv_field',
                    intro: 'Select "Yes" to update price of each product.',
                    position: 'left'
                  },
                  {
                    element: '#custom_price_csv_label',
                    intro: "Get CSV file by clicking 'CLICK HERE'.",
                    position: 'bottom'
                  },
                  {
                    element: '#custom-title-section',
                    intro: 'Manage Product custom/dynamic Title Setting.',
                    position: 'bottom'
                  },
                  {
                    element: '#custom_title_csv_field',
                    intro: 'Select "Yes" to update title.',
                    position: 'left'
                  },
                  {
                    element: '#custom_title_csv_label',
                    intro: "Get CSV file by clicking 'CLICK HERE'.",
                    position: 'bottom'
                  },*/
                  {
                    element: '#email-subscription-section',
                    intro: 'Update Email Subscription Setting from here. Check the corresponding Checkbox to receive Mails and Uncheck to Not Receive Mails.',
                    position: 'bottom'
                  }
                ]
            });

            configQuicktour.start().oncomplete(function() {
              window.location.href = '<?= Data::getUrl("site/index") ?>';
          });
      });
  </script>
<?php endif; ?>

