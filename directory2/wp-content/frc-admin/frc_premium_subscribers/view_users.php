<!DOCTYPE html>
<html class="ie" lang="en">
    <!-- URL: https://www.freerangecamping.com.au/directory/wp-content/frc_lvl_n_roles/wlm_get_user_levels.php -->
	<head>
		<title>FRC Premium Users</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="js/colorbox-master/jquery.colorbox.js"></script>
        <script src="js/jquery.blockUI.js"></script>
	</head>
<?php
    /*Records to table 'frc_premium_users' is getting added from here: 
        /home/frcconz/public_html/directory/wp-content/frc_premium_subscribers/subscriber.php
        And updating admin by this script 
        https://www.freerangecamping.co.nz/directory/wp-content/frc_premium_subscribers/daily-prem-subscribers.php
        which is running by scheduler after 4 hours
    */
    //Calling URL: https://www.freerangecamping.co.nz/directory/wp-content/frc_premium_subscribers/view_users.php
     
    require_once("../../../wp-config.php");
    require_once('../../../wp-load.php');
    global $wpdb;
    defined('DS') or define('DS', DIRECTORY_SEPARATOR);
    require_once(WP_CONTENT_DIR.DS.'frc-admin/frc_includes/header.php');
    require("inner-navigation.php");
    $missingRoleArr = array();
	//echo'<pre>';print_r($_REQUEST);die;
	if(isset($_REQUEST['searched_data'])){
		$dataSearched = $_REQUEST['searched_data'];
	}else{
		$dataSearched = "";
	}
	
	if(isset($_REQUEST['start_date'])){
		$dateStart = $_REQUEST['start_date'];
	}else{
		$dateStart = "";
	}
	
	if(isset($_REQUEST['end_date'])){
		$dateEnd = $_REQUEST['end_date'];
	}else{
		$dateEnd = "";
	}
	
	if(isset($_REQUEST['pmt_counts'])){
		$countPmt = $_REQUEST['pmt_counts'];
	}else{
		$countPmt = "";
	}
	
	if(isset($_REQUEST['user_id'])){
		$idUser = $_REQUEST['user_id'];
	}else{
		$idUser = "";
	}
    ?>
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script-->
    
</head>
<body>
    <fieldset>
        <!--<legend style="background-color:#C6D5FD">Search</legend>-->
        <h3 style='padding-left:20px;'>&raquo;&nbsp;Premium Users</h3>
        <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
            <form action="view_users.php" method="get">
				<table class="table-bordered table-hover" style="width:80%; padding-top:5px;">
					<tr bgcolor="#C6D5FD">
						<th colspan="4" style="line-height: 35px;min-height: 35px;margin-top:5px;">&nbsp;»&nbsp;Search:</th>
					</tr>
					<tr>
						<td style="width: 84px;">Search:</td><td style="width: 10px;"><input type="text" style="width:300px" name="searched_data" id="searched_data" placeholder="first name,last name,email,address,city,post code" value="<?php echo $dataSearched; ?>"></td>
						<td style="width: 84px;">Date:</td><td><input type="text" name="start_date" id="start_date" placeholder="start_date" style="width:100px"  value="<?php echo $dateStart; ?>" readonly='readonly'>
						<input type="text" name="end_date" id="end_date" placeholder="end_date" style="width:100px"  value="<?php echo $dateEnd; ?>" readonly='readonly'> </td>
					</tr>
					<tr>
						<td>Pmt Counts:</td>
						<td><select name = 'pmt_counts'>
							<option value=''>All</option>
							<option value='1'>1</option>
							<option value='2'>2</option>
							<option value='3'>3</option>
							<option value='4'>4</option>
							<option value='gt4'>Greater than 4</option>
						</select></td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<td>OR User ID:</td><td><input type="text" name="user_id" id="user_id" placeholder="user_id" style="width:100px"  value="<?php echo $idUser; ?>"></td>
					</tr>
					<tr>
					<td colspan=4 style="padding-left: 322px;"><input type="submit" name="submit" value="Search" style='color:#333;background-color:#C6D5FD;border:1px solid #333;' ></td>
					</tr>
				</table>
            </form>
        </div>
    </fieldset>
    
    <?php
    if(
       array_key_exists('searched_data',$_REQUEST) && !empty($_REQUEST['searched_data']) ||
       (array_key_exists('pmt_counts',$_REQUEST) && !empty($_REQUEST['pmt_counts'])) ||
       (array_key_exists('user_id',$_REQUEST) && !empty($_REQUEST['user_id'])) ||
       (array_key_exists('start_date',$_REQUEST) && !empty($_REQUEST['start_date'])) ||
       (array_key_exists('end_date',$_REQUEST) && !empty($_REQUEST['end_date']))
    ){
        //print_r($_REQUEST);die;
        $searchData = $_REQUEST['searched_data'];
        $pmtCounts = $_REQUEST['pmt_counts'];
        $userId = $_REQUEST['user_id'];
        $start_date = $_REQUEST['start_date'];
        $end_date = $_REQUEST['end_date'];
       
        if(!empty($searchData) && !empty($pmtCounts)){
            $pmtCondStr = "`pmt_counts` = $pmtCounts";
            $pmtCondStr = $pmtCounts == 'gt4' ? '`pmt_counts` > 4' : $pmtCondStr;
            $condStr = "`billing_shipping` like '%$searchData%' AND $pmtCondStr";
        }elseif(!empty($pmtCounts)){
            $pmtCondStr = "`pmt_counts` = $pmtCounts";
            $condStr = $pmtCounts == 'gt4' ? '`pmt_counts` > 4' : $pmtCondStr;
        }elseif(!empty($userId)){
            $condStr = "`user_id` = $userId";
        }elseif(!empty($searchData)){
            $condStr = "`billing_shipping` like '%$searchData%'";
        }else{
            $condStr = "";
        }
        
        if(empty($userId)){
            if(!empty($start_date) && empty($end_date)){
                $end_date = $start_date;
            }elseif(empty($start_date) && !empty($end_date)){
               $start_date =  $end_date;
            }
            $endDate = !empty($end_date) ? date('Y-m-d',strtotime($end_date . "+1 days")) : '';
            if(!empty($condStr) && !empty($start_date)){
               $condStr .= " AND `paid_date` >= '$start_date' and `paid_date` <= '$endDate'";
            }elseif(empty($start_date) && empty($endDate)){
                $condStr;
            }else/*if(empty($condStr) && !empty($start_date))*/{
                $condStr .= "`paid_date` >= '$start_date' and `paid_date` <= '$endDate'";
            }
        }
        
        $total = $wpdb->get_var("SELECT COUNT(*) FROM `frc_premium_users` WHERE $condStr ORDER BY `id` DESC");
        $post_per_page = 25;
        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
        $offset = ( $page * $post_per_page ) - $post_per_page;
        $search_qry ="SELECT * FROM `frc_premium_users` WHERE $condStr";
        $rsltObjs = $wpdb->get_results( $search_qry . " ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}");
        
        
    }else{
        //print_r($_REQUEST);die;
        $total = $wpdb->get_var("SELECT COUNT(*) FROM `frc_premium_users` ORDER BY `id` DESC");
        $post_per_page = 25;
        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
        $offset = ( $page * $post_per_page ) - $post_per_page;
        $qry = "SELECT * FROM `frc_premium_users`";
        $rsltObjs = $wpdb->get_results( $qry . " ORDER BY `id` DESC LIMIT ${offset}, ${post_per_page}");
    }
    echo "<span style='font-size:200%; color:grey;padding-left:20px;'><u>Premium Users</u></span> [<strong>Total Records</strong>: <span style='background-color: yellow;'> &nbsp;$total </span>]";
    echo $tableBlock = '
    <div class="table-responsive" style="padding-left:20px;padding-top:5px;">
    <table style="font-family:  Serif, Georgia, \'Times New Roman\';font-size: 90%;">
        <tr bgcolor="#C6D5FD">
            <td><b>User ID</b></td>
            <td><b>Directory Roles</b></td>
            <td><b>Premium Roles</b></td>
            <td><b>Classified Roles</b></td>
            <td><b>Shop Roles</b></td>
            <td><b>FName</b></td>
            <td><b>LName</b></td>
            <td><b>Email</b></td>
            <td><b>Phone</b></td>
            <td><b>Pmt Counts</b></td>
            <td style="padding-left: 18px;"><b>Pmt Date</b></td>
            <!--th>Amount</th-->
            <td><b>Company</b></td>
            <td style="padding-left: 10px;"><b>Address1</b></td>
            <td><b>Address2</b></td>
            <td style="padding-left: 18px;"><b>City</b></td>
            <td><b>State</b></td>
            <td style="padding-left: 8px;"><b>Zip</b></td>
        </tr>';
        $loopCounter = 0;
        foreach($rsltObjs as $key => $reslt){
            $bgColor = $key % 2 == 0 ? '#CCC' : '#FFF';
            $frc = 'FRC-';
            $billData = unserialize($reslt->billing_shipping);
            $pmtCount =  $reslt->pmt_counts;
            extract($billData);
            $phpDate = strtotime( $reslt->paid_date );
            $dateArr = array(
                            date('M j, y',strtotime("-2 days")),
                            date('M j, y',strtotime("-1 days")),
                            Date('M j, y'),
                            date('M j, y',strtotime("+1 days")),
                            );
            $paidDate = date( 'M j, y h:i A', $phpDate );
            $pmtDetails = unserialize($reslt->pmt_details);
            $titleStr = $rowStr = "";
            if(count($pmtDetails) >= 1){
                $sum = 0;
                $counter = 0;
                foreach($pmtDetails as $pmtDetail){
                    $counter++;
                    //print_r($pmtDetail);die;
                    $postID = $pmtDetail['post_id'];
                    $amount =  $pmtDetail['amount'];
                    $pmtDate =  $pmtDetail['pmt_date'];
                    $sum+=$amount;
                    $rowStr.="<tr><td>$counter</td><td>$postID</td><td>$pmtDate</td><td>$amount</td></tr>";                    
                }
                $roleArr = array();
                //if(date( 'M j, y', $phpDate) == Date('M j, y')){
                if(
                   in_array(date( 'M j, y', $phpDate), $dateArr) ||
                   true || //temporary
                   in_array(date( 'M j, y', strtotime($phpDate. ' + 1 days' )), $dateArr) ||
                   in_array(date( 'M j, y', strtotime($phpDate. ' - 1 days' )), $dateArr) ||
                   in_array(date( 'M j, y', strtotime($phpDate. ' - 2 days' )), $dateArr)
                ){
                    if($loopCounter++ <= 2){
                      $curl = curl_init();
                      $userID = $reslt->user_id;
                      $reqURL= "https://www.freerangecamping.co.nz/directory/wp-content/frc_user_roles/grab_roles.php?user_id=$userID";
                      curl_setopt($curl, CURLOPT_URL, $reqURL);
                      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                      $result = curl_exec($curl);
                      curl_close($curl);
                      $roleArr = unserialize($result);
                    }
                    
                }
                
                //print_r($roleArr['wp']);//die;
                $dirRole = array_key_exists('directory', $roleArr) ? implode(", ", array_keys($roleArr['directory'])) : '';
                $classifiedRole = array_key_exists('classified', $roleArr) ? implode(", ", array_keys($roleArr['classified'])) : '';
                $shopRole = array_key_exists('shop', $roleArr) ? implode(", ", array_keys($roleArr['shop'])) : '';//echo "shop role:".
                $wpRole = array_key_exists('wp', $roleArr) ? implode(", ", array_keys($roleArr['wp'])) : '';//echo "prem role:".
                if($shopRole != "premium"){
                    $missingRoleArr[] = $reslt->user_id;
                }
                $summary = "<tr><td><b>Summary</b></td><td><b>Pmt Count:$counter</b></td><td><b>Total</b></td><td>$sum</td></tr>";
                $titleStr.='<div style="overflow-x:scroll;font-size:10px;">
                                <table>'.$summary.'<tr><td colspan="4"><hr /></td><tr><td><b>Sr.No</b></td><td><b>Post ID</b></td><td><b>Pmt Date</b></td><td><b>Amount</b></td></tr>'.$rowStr.'<tr><td colspan="4"><hr /></td>'.$summary.'</table></div>';
            }
            echo "<tr style='background:{$bgColor}'>
                        <td nowrap='nowrap' title='$titleStr'>FRC-{$reslt->user_id}</td>
                        <td>$dirRole</td>
                        <td>$wpRole</td>
                        <td>$classifiedRole</td>
                        <td>$shopRole</td>
                        <td>{$_billing_first_name}</td>
                        <td>{$_billing_last_name}</td>
                        <td>$reslt->email</td>
                        <td>{$_billing_phone}</td>
                        <td style='padding-left: 18px;'>{$pmtCount}</td>
                        <td nowrap='nowrap'><a href=#inline_content id=date class=inline rel= $reslt->user_id >$paidDate</a></td>
                        <!--td>$reslt->order_total</td-->
                        <td style='padding-left: 8px;'>{$_billing_company}</td>
                        <td>{$_billing_address_1}</td>
                        <td>{$_billing_address_2}</td>
                        <td>{$_billing_city}</td>
                        <td>{$_billing_state}</td>
                        <td>{$_billing_postcode}</td>
                    </tr>";
        }
    echo'</table></div><br><br>';
    
    echo '<div class="pagination" style="padding-left:20px;">';
    echo paginate_links( array(
    'base' => add_query_arg( 'cpage', '%#%' ),
    'format' => '',
    'prev_text' => __('&laquo;'),
    'next_text' => __('&raquo;'),
    'total' => ceil($total / $post_per_page),
    'current' => $page,
    'type' => 'plain'
    ));
    echo '</div>';
?>
<script>
   $( document ).tooltip({
    content: function() {
        return $(this).attr('title');
    }
   });
   
   $( function() {
        $( "#start_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
        $( "#end_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
    } );
  </script>
<script>
    /* role block */
    //Start:block of showing colobox of roles and levels by curl
    $(document).ready(function(){
        $(document).on('click', '.inline', function() {
            $.blockUI({ message: 'Loading ...' });
            var user_id = $(this).attr('rel');
            targeturl = "get_roles.php?user_id="+user_id;
            $.ajax({
                url: targeturl,
                type: 'post',
                dataType: "json",
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                },
                success: function(response) {
                    $.unblockUI();
                    var res = response;
                    var userId = res.user_id;
                    var login = res.login;
                    var roles = res.roles;
                    
                    var prem_roles = res.prem_roles;
                    var class_roles = res.class_roles;
                    var shop_roles = res.shop_roles;
                    var dir_roles = res.gwr_roles;
                    
                    var selected_role = prem_roles.concat(dir_roles).concat(shop_roles).concat(class_roles);
                    //alert(selected_role);
                    var sel_roles = selected_role.filter((v, i, a) => a.indexOf(v) === i); //directory_7,premium
                    if(Array.isArray(sel_roles)){;}
                    $('#user_login').html(login);
                    $( "#role" ).empty();
                    $( "#prem_role" ).empty();
                    $( "#class_role" ).empty();
                    $( "#dir_role" ).empty();
                    $( "#shop_role" ).empty();
                    
                    $('#hidden').attr("value",userId);
                    var counter = 0;
                    
                    checkBoxHTML = "<table width='100%'><tr>";
                    $.each(roles ,function(index,role) {
                        if (counter != 0 && counter % 4 == 0) {
                            checkBoxHTML+="</tr><tr>";
                        }
                        //console.log(counter + " : " + role + '>>'+$.inArray( role.toString(), sel_roles ));
                        var chkStr = "";
                        /*if ($.inArray(role, sel_roles) != -1) {
                            chkStr = " checked='checked'";
                        }*/
                        for (i = 0; i < sel_roles.length; i++) { //$.inArray(role, sel_roles) !== -1
                            arrRole = sel_roles[i];
                            if (arrRole == index) {
                                chkStr = " checked='checked'";
                                break;
                            }
                        }
                        checkBoxHTML+="<td width='25%'><input class='test' type='checkbox'"+chkStr+" name='role' value='"+index+"'><label style='font-size:10px;'>"+role+"</label></td>";
                        
                        counter++;
                    });
                    checkBoxHTML+="</tr></table>";
                    $('#role').append(checkBoxHTML);
                    var prem_role = prem_roles.join(' , ');
                    $('#prem_role').append(prem_role);
                    
                    var class_role = class_roles.join(' , ');
                    $('#class_role').append(class_role);
                    
                    var dir_role = dir_roles.join(' , ');
                    $('#dir_role').append(dir_role);
                    
                    var shop_role = shop_roles.join(' , ');
                    $('#shop_role').append(shop_role);
                    
                    $(".inline").colorbox({ open: true, inline:true, width:"50%"});
                }
            });
        });
        
    //End:block of showing colorbox of roles and levels by curl
        $( document ).tooltip({
            content: function() {
                return $(this).attr('title');
            }
        });
       
        $( function() {
            $( "#start_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
            $( "#end_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
        } );
        
        $(document).on('click', '#email_id', function() {
            var user_id = $(this).attr('rel');
            var form_id = 'form_'+user_id;
            document.getElementById(form_id).submit();
        });
    //Start:updated roles block        
        $(document).on('click', '#submit', function() {
            var user_id = document.getElementById('hidden').value;
            var roleIds = [];
            $('input[name="role"]:checked').each(function() {
                roleIds.push($(this).val());//$(this).next('label').text();//$(this).attr('name')
            });
            
            var a = "user_id="+user_id;
            $.each(roleIds ,function(index,roleId) {
                    a += '&id[]='+roleId;
                
            });
            var url = "https://freerangecamping.co.nz/cron/wp-content/curl/save_capabilities.php?"+a;
            //http://freerangecamping.co.nz/cron/wp-content/curl/curl.php?user_id=17051
            $.ajax({
                type: "post",
                dataType: "json",
                url: url,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                },
                success: function(msg){
                    alert(msg);
                    $.colorbox.close();
                },
                error: function() {
                    alert( 'Something goes wrong!' );
                }
            });
            return false;
            
        });
    //End:updated roles block
    });
</script>
    <?php
       // echo "<pre>"; print_r($missingRoleArr);echo "</pre>";
        echo implode(", ", $missingRoleArr);
    ?>
<!--Start:block of colobox---->
<div style='display:none'>
    <div id='inline_content' style='padding:10px; background:#fff;'>
        You are updating roles for user: <span id='user_login'></span>
        <form action="" id="myForm" method="post">
            <p style="color:blue;"><strong>Roles: </strong></p>
            <p id='role'> <?php //echo $role; ?></p>
            <p style="color:blue;">Current User Roles are:</p>
            <p style="color:blue;"><strong>Premium Roles: </strong><span id='prem_role'></span></p>
            <p style="color:blue;"><strong>Directory Roles: </strong><span id='dir_role'></span></p>
            <p style="color:blue;"><strong>Shop Roles: </strong><span id='shop_role'></span></p>
            <p style="color:blue;"><strong>Classified Roles: </strong><span id='class_role'></span></p>
            <p><input type="submit" id="submit" name="submit" value="submit"></p>
            <input type="hidden" id="hidden">
        </form>
    </div>
</div>
<!--End:block of colobox---->
</body>