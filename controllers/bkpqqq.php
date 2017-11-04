 if($response['success']==1 && $response['id']){

                        return ['success'=>'Product Uploaded on Wish'];
                    }
                    elseif($response['message']){
                        
                        $uploadErrors=$response['message'];
                       return ['errors' => $uploadErrors];
                    }
          

 .......................................
   if($productResponse['errors']){
                    $productResponse['errors']
                	$productResponse=json_encode($productResponse);
                    $returnerrorArr = ['error'=>true, 'message'=>$productResponse];
                    //print_r($returnerrorArr);die;
                    
                }

                if(is_array($productResponse) && isset($productResponse['uploadIds'],$productResponse['feedId']) && count($productResponse['uploadIds'])>0)
                {
                     
                    //save product status and data feed
                    $ids = implode(',',$productResponse['uploadIds']);
                    foreach($productResponse['uploadIds'] as $val)
                    {
                        $query="UPDATE `wish_product` SET status='Items Processing', error='' where product_id='".$val."'";
                        Data::sqlRecords($query,null,"update");
                    }

                    $feed_file = isset($productResponse['feed_file'])?$productResponse['feed_file']:'';
                    $query="INSERT INTO `wish_product_feed`(`merchant_id`,`feed_id`,`product_ids`,`feed_file`)VALUES('".MERCHANT_ID."','".$productResponse['feedId']."','".$ids."','".$feed_file."')";
                    Data::sqlRecords($query,null,"insert");
                    //echo $query;die;

                    $msg = "product feed successfully submitted on wish.";
                    $feed_count = count($productResponse['uploadIds']);
                    $feedId = $productResponse['feedId'];
                    $returnArr = ['success'=>true, 'message'=>$msg, 'count'=>$feed_count, 'feed_id'=>$feedId];
                }
                elseif(isset($productResponse['errors'])) {
                    $msg = json_encode($productResponse['errors']);
                    $returnArr = ['error'=>true, 'message'=>$msg];
                }
                elseif (isset($productResponse['feedError'])) {
                    $msg = json_encode($productResponse['feedError']);
                    $returnArr = ['error'=>true, 'message'=>$msg];
                    //print_r($returnArr);die;
                }


                //save errors in database for each erroed product
               
                if (count($returnerrorArr['error'])>0) {

                	$_feedError = null;
                	if (isset($productResponse['error'])) {
                		$msg = $productResponse['error'];
                		$_feedError = $msg;
                		unset($productResponse['errors']);
                
                	}
                    print_r($productResponse);die;
                	/*foreach ($productResponse['error'] as $productSku => $error) {
                        print_r($error);die("dffg");
                        $sku=preg_match("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is",$error[0],$match);
                      
                        $productsku = str_replace("'", "", $match);
                       
                		$query = "UPDATE `wish_product` wp JOIN `jet_product` jp ON wp.product_id=jp.bigproduct_id AND jp.merchant_id = wp.merchant_id SET wp.`error`='" . addslashes($error) . "' where jp.sku='" . $productsku[0] . "'";
                		Data::sqlRecords($query, null, "update");
                        
                	}*/
                   
                	$returnArr['error'] = true;
                	//$returnArr['error_msg'] = $productResponse['errors'];

                	//$returnArr['originalmessage'] = $productResponse['originalmessage'];
                
                	$returnArr['error_count'] = count($productResponse['errors']);

                	$returnArr['erroredSkus'] = $productsku;
                
                	if (!is_null($_feedError)) {
                		$returnArr['feedError'] = $_feedError;
                	}
                }




                $productUpload=$product;
            
            if($productUpload){

                $response=$this->CPostRequest(self::GET_FEEDS_ITEMS_SUB_URL,$productUpload);
                $errorresponse[] = $response['message'];
               

            }
        }
       
        if($response['success']==1 && $response['id']){

                        return ['success'=>'Product Uploaded on Wish'];
                    }
           elseif($errorresponse){

                    //$uploadErrors = explode("'", $response['message']);
                    return ['errors' => $errorresponse];
                      

                
                }