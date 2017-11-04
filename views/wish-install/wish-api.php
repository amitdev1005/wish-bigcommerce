<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\modules\wishmarketplace\components\Data;
use frontend\modules\wishmarketplace\components\Installation;
$session = Yii::$app->session;
$url = Data::getUrl('wish-api/save');
$codeUrl = \frontend\modules\wishmarketplace\components\Data::getUrl('wishconfiguration/getcode');        $merchant_id = MERCHANT_ID;

$showApiForm = Installation::showApiStep($merchant_id );

$client_id = isset($session['wishConfig']['client_id'])?$session['wishConfig']['client_id']:'';
$client_secret_key = isset($session['wishConfig']['client_secret_key'])?$session['wishConfig']['client_secret_key']:'';
$redirect_url = isset($session['wishConfig']['redirect_url'])?$session['wishConfig']['redirect_url']:'';

?>

<?php if($showApiForm) : ?>
<div class="api_enable jet_config content-section test-api-step">
	<div class="api_field fieldset enable_api">
    	<div class="help-block help-block-error top_error alert-danger" style="display: none;border-radius: 4px;margin-bottom: 10px;padding: 10px;"></div>
		<?php
		  
		$form = ActiveForm::begin([
	    'id' => 'wish_config_api',
	    'action' => $url,
        'method' => 'post',
        'options' => ['name' => 'wish_config'],
    	]) ?>
	<!-- 	<a href="<?= $codeUrl;?>">
					<button class="btn btn-primary" type="button" title="Get Token" id="Code">
						<span>Get Token</span>
					</button>
		</a> -->
		<!--   <button class="btn btn-primary" onclick="clickCode()">Get Code</button> -->
		
		<ul class="table table-sliiped" cellspacing="0">
			<li>
				<div>
					
					<div class="value_label">
						<span class="control-label">Client Id</span>
					</div>
					<div class="form-group required">
						<input placeholder="Please enter Client ID" autofocus="autofocus" id="api-api_url" class="form-control" type="text" name="client_id" maxlength="255" value=<? echo $client_id ?>>
						<div class="has-error">
							<p class="help-block help-block-error error_category_map" style="display: none;">'Wish Client Id' is Required</p>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</li>
			<li>
				<div>
					<div class="value_label">
						<span class="control-label">Client Secret key</span>
					</div>
					<div class="form-group required">
						<!-- <textarea placeholder="Please enter wish Fulfillment Node Id" autofocus="autofocus" id="api-secret_key" class="form-control" name="secret_key"></textarea> -->
						<input placeholder="Please enter Secret Key" autofocus="autofocus" id="api-fullfillment_node" class="form-control" type="text"  name="client_secret_key" value=<? echo $client_secret_key?>>
						<div class="has-error">
							<p class="help-block help-block-error error_category_map" style="display: none;">'Client Secret key' is Required</p>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</li>
			<li>
				<div>
					<div class="value_label">
						<span class="control-label">Redirect Url</span>
					</div>
					<div class="form-group required">
					
						<input placeholder="Please enter Redirect Url" autofocus="autofocus" id="api-consumer_channel_type_id" class="form-control" type="text" name="redirect_url" value=<? echo $redirect_url?>>
						<div class="has-error">
							<p class="help-block help-block-error error_category_map" style="display: none;">'Wish Redirect Url' is Required</p>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			</li>
			<!-- <li>
				<div>
					<div class="value_label">
						<span class="">Code</span>
					</div>
					<div class="form-group">
					
						<input placeholder="Please enter Your Code" autofocus="autofocus" id="api-auth_key" class="form-control" type="text" value="" name="auth_key">
					</div>
					<div class="clear"></div>
				</div>
			</li> -->
			<!-- <li>
				<div>
					<div class="value_label">
						<span class="">Token</span>
					</div>
					<div class="form-group">
					
						<input placeholder="Please enter Your Code" autofocus="autofocus" id="api-auth_key" class="form-control" type="text" value="" name="auth_key">
					</div>
					<div class="clear"></div>
				</div>
			</li> -->
			<li>
				<div>
					<div class="clearfix">
						<input type="button" class="btn btn-primary next" value="Next" id="test_button">
					</div>
				</div>
			</li>

		</ul>
		<?php 
			ActiveForm::end();
		?>
	</div>	
</div>

<script type="text/javascript">
$(document).ready(function(){
  var url = '<?php echo $url;?>';
  UnbindNextClick()
	$('.next').on('click', function(event){	
		//check validation
    	event.preventDefault();
		var flag=false;
		$('form .required .form-control').each(function(){
			var value = $(this).val().trim();
			if(value == "")
			{
				flag=true;
				$(this).addClass("select_error");
				$(this).next('div').children('.error_category_map').show();
			}
			else
			{
				$(this).removeClass("select_error");
				$(this).next('div').children('.error_category_map').hide();
			}
		});
	    if(!flag){
			$('#LoadingMSG').show(); 
			$.ajax({
			    method: "POST",
			    url: url,
			    dataType : "json",
			    data: $("form").serialize(),
			})
			.done(function(response){
				$('#LoadingMSG').hide();
				if(response.success)
				{
					$('.top_error').hide();
					nextStep();
				} else {
					$('.top_error').html(response.message);
					$('.top_error').show();
				}
			});
	    } 
	});
});
 
</script>
<?php else : ?>
<div class="api_enable jet_config content-section test-api-step">
	<div>
		<h3>Congratulations!!</h3>
		<p>Thank you for sharing your details with us. We have got your request. Cedcommerce team will get back to you very soon.</p>
		<p>Thank you.</p>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.next').hide();
		$('.next').attr('disabled',true);

		$('.next').on('click', function(event){
    		event.preventDefault();
    		alert("Can't Proceed.");
    	});
	});
</script>
<?php endif; ?>
