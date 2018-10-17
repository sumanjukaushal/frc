<?php
        $items_per_page = $perPage = 200;	//No of Items to be shown on page
        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;	//Page No
        $offset = ( $page * $items_per_page ) - $items_per_page;	//Page offset
        $successArr = array();
        $iniFormHTML = <<< FORM_HTML
            <form method='post'>
            <div class="table-responsive">
            <table class="table" style='width:80%'>
                <tr><td colspan='2'><h3>Step 1</h3></td></tr>
                <tr>
                    <td>Discount</td>
                    <td><input type='checkbox' name="discount" value='1' /></td>
                </tr>
                <tr>
                    <td>Discount Detail</td>
                    <td><input type='text' name="discount_detail" value='' style='width:300px;'/></td>
                </tr>
                <tr>
                    <td>Offer</td>
                    <td><input type='checkbox' name="offer" value='1' /></td>
                </tr>
                <tr>
                    <td>Offer Detail</td>
                    <td><input type='text' name="offer_detail" value='' style='width:300px;' /></td>
                </tr>
                    <td>Search ITEM's</td>
                    <td><input type='text' name='search' value='' placeholder='Search by matching Item title' style='width:300px;' />
                <tr>
                </tr>
                    <td>&nbsp;</td>
                    <td><input type='submit' name='submit' value='submit' />
                <tr>
            </table>
            </div>
            <input type='hidden' name='step_1' value='1' />
            </form>
FORM_HTML;

        if(isset($_REQUEST['step_2']) && !empty($_REQUEST['step_2'])){
            $metaType = isOldTheme() ? '_ait-dir-item' : '_ait-item_item-data';
            //echo "<pre>";print_r($_REQUEST);echo "</pre>";
            $itemIDs = $_REQUEST['itemIDs'];
            foreach($itemIDs as $itemID){
                $query = "SELECT `meta_value` FROM $optionTbl WHERE `meta_key` = '$metaType' AND `post_id` = $itemID";
                $metaVal = $wpdb->get_results($query, OBJECT);
                if(count($metaVal)){
                    $metaValue = unserialize($metaVal[0]->meta_value);
                }
                $metaValue['discount_detail'] = $_REQUEST['discount_detail'];
                $metaValue['offer_detail'] = $_REQUEST['offer_detail'];
                
                if( array_key_exists('discount', $_REQUEST) && ($_REQUEST['discount'] == 1)){
                    $metaValue['discount'] = array('enable' => 'enable');
                }elseif(array_key_exists('discount', $metaValue)){
                    unset($metaValue['discount']);
                }
                
                if( array_key_exists('offer', $_REQUEST) && ($_REQUEST['offer'] == 1)){
                    $metaValue['offer'] = array('enable' => 'enable');
                }elseif(array_key_exists('offer', $metaValue)){
                    unset($metaValue['offer']);
                }
                
                //echo "<pre>";print_r($metaValue);echo "</pre>";die;
                $updateRes = $wpdb->update($optionTbl, array('meta_value' => serialize($metaValue)), array('post_id' => $itemID, 'meta_key' => $metaType));
                $dbGMTNow = gmdate("Y-m-d H:i:s", time());
                $currentDBTime = Date("Y-m-d H:i:s");
                if($updateRes){
                    $successArr[] = $itemID;
                    $updateRes = $wpdb->update($postTbl, array('post_modified_gmt' => $dbGMTNow), array('ID' => $itemID));
                    $wpdb->query("UPDATE `$postTbl` SET `post_modified_gmt` = '{$dbGMTNow}', post_modified = '{$currentDBTime}'  WHERE ID = {$itemID}" );
                }
            }
            if(count($successArr)){
                $message = "Following Items are updated successfully: ".implode(", ", $successArr);
                echo $messageHTML = <<<MSG_HTML
                <div class="table-responsive">
                    <table class="table" style='width:80%'>
                        <tr class='success'><td>$message</td></tr>
                    </table>
                </div>
MSG_HTML;
            }
            echo $iniFormHTML;
        }elseif(isset($_REQUEST['step_1']) && !empty($_REQUEST['step_1'])){
            $postType = isOldTheme() ? 'ait-dir-item' : 'ait-item';
            $args = array(
					'post_type' => $postType,
					'post_status' => array( 'publish', 'pending', 'draft', 'future' ),
					'posts_per_page' => $perPage,
					'offset' => $offset,
					'orderby' => array( 'ID' => 'DESC')
				);
            $args['s'] = $_REQUEST['search'];
            $wpQueryRes = new WP_Query($args);
            $discountApplied = "";
            if( array_key_exists('discount', $_REQUEST) && isset($_REQUEST['discount']) )$discount = 1;
            if( array_key_exists('offer', $_REQUEST) && isset($_REQUEST['offer']) )$offer = 1;
            
            $discntChkStr = $discount == 1 ? 'Enabled' : 'Not Enabled';
            $offerChkStr = $offer == 1 ? 'Enabled' : 'Not Enabled';
            
            $disDet = $_REQUEST['discount_detail'];
            $offerDet = $_REQUEST['offer_detail'];
            $rowStr = "<tr><td>Sr No.</td><td>Item ID</td><td>Item Title</td><td>Item Status</td><td>Apply To</td></tr>";
            $counter = 0;
            foreach($wpQueryRes->posts as $key => $postObj){
                $counter++;
                $site_id = $postObj->ID;
                $siteLink = get_permalink($site_id);
                $site_name = $postObj->post_title;
                $postStatus = $postObj->post_status;
                switch($postStatus){
                    case 'info': $class='Info'; break;
                    case 'publish': $class='active'; break;
                    case 'future': $class='warning'; break;
                    case 'draft': $class='danger'; break;
                    default: $class='success';
                }
                
                $rowStr.=<<< ROW_STR
                <tr class='$class'>
                    <td>$counter</td>
                    <td><a href='$siteLink' target='_blank'>$site_id</a></td>
                    <td><a href='$siteLink' target='_blank'>$site_name</a></td>
                    <td>$postStatus</td>
                    <td><input type='checkbox' name='itemIDs[]' value='$site_id' /></td>
                </tr>
ROW_STR;
            }
            echo $formHTML = <<< FORM_HTML
            <form method='post'>
                <div class="table-responsive">
                    <table class="table" style='width:80%'>
                        <tr><td colspan='2'><h3>Step 2</h3></td></tr>
                        <tr><td>Discount</td><td>$discntChkStr</td></tr>
                        <tr><td>Discount Detail</td><td>$disDet</td></tr>
                        <tr><td>Offer</td><td>$offerChkStr</td></tr>
                        <tr><td>Offer Detail</td><td>$offerDet</td></tr>
                    </table>
                    <table class="table" style='width:80%'>
                        $rowStr
                        <tr><td colspan='3' align='center'><input type='reset' name='Reset' /> | <input type='submit' name='submit' value='submit' /></td></tr>
                    </table>
                </div>
                
                <input type='hidden' name='discount' value='$discount' />
                <input type='hidden' name='discount_detail' value='$disDet' />
                <input type='hidden' name='offer' value='$offer' />
                <input type='hidden' name='offer_detail' value='$offerDet' />
                <input type='hidden' name='step_2' value='1' />
            </form>
FORM_HTML;
            //echo "<pre>";print_r($wpQueryRes);echo "</pre>";DIE;
        }else{
            echo $iniFormHTML;
        }
?>