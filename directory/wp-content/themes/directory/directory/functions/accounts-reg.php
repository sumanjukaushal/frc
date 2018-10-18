<?php

global $paypal, $aitThemeOptions, $wp_roles, $registerErrors, $registerMessages;

$credentials = array();
$credentials['USER'] = (isset($aitThemeOptions->members->paypalUser)) ? $aitThemeOptions->members->paypalUser : '';
$credentials['PWD'] = (isset($aitThemeOptions->members->paypalPassword)) ? $aitThemeOptions->members->paypalPassword : '';
$credentials['SIGNATURE'] = (isset($aitThemeOptions->members->paypalSignature)) ? $aitThemeOptions->members->paypalSignature : '';
$sandbox = (isset($aitThemeOptions->members->paypalType) && $aitThemeOptions->members->paypalType == 'live') ? '' : 'sandbox.';
$sandboxBool = (!empty($sandbox)) ? true : false;

$paypal = new Paypal($credentials,$sandboxBool);

/**
 * Register or upgrade user
 */
if(isset($_GET['dir-register']) && ($_GET['dir-register'] == 'register' || $_GET['dir-register'] == 'upgrade') && isset($_POST['user-submit'])) {

	// register user with minimal role
	$upgrade = false;
	if($_GET['dir-register'] == 'upgrade'){
		$upgrade = true;
		$currentRolePriceName =  $_POST['directory-role-current-price'];
		$userId = $_POST['user_id'];
	} else {
		$userId = aitRegisterDirectoryUser($_POST['user_login'],$_POST['user_email']);
	}

	// if errors
	if(is_wp_error( $userId )){

		$registerErrors = $userId;

	} else {

		$roleNum = 1;
		$rolePrice = '0';
		$free = true;
		$price = '0';
		$packageName = '';

		// set role
		if(isset($_POST['directory-role'])){
			$role = $_POST['directory-role'];
			if (($role == "directory_1") || ($role == "directory_2") || ($role == "directory_3") || ($role == "directory_4") || ($role == "directory_5")){
				$roleNum = intval(substr($role, 10));
				switch ($role) {
					case "directory_1":
						if(isset($aitThemeOptions->members->role1Price) && trim($aitThemeOptions->members->role1Price) !== '0') {
							$rolePrice = $aitThemeOptions->members->role1Price;
							$free = false;
							$price = trim($aitThemeOptions->members->role1Price);
							$packageName = $wp_roles->role_names[$role];
						}
						break;
					case "directory_2":
						if(isset($aitThemeOptions->members->role2Price) && trim($aitThemeOptions->members->role2Price) !== '0') {
							$rolePrice = $aitThemeOptions->members->role2Price;
							$free = false;
							if($upgrade && $currentRolePriceName != 'none'){
								$price = floatval(trim($aitThemeOptions->members->role2Price)) - floatval(trim($aitThemeOptions->members->$currentRolePriceName));
							} else {
								$price = trim($aitThemeOptions->members->role2Price);
							}
							$packageName = $wp_roles->role_names[$role];
						}
						break;
					case "directory_3":
						if(isset($aitThemeOptions->members->role3Price) && trim($aitThemeOptions->members->role3Price) !== '0') {
							$rolePrice = $aitThemeOptions->members->role3Price;
							$free = false;
							if($upgrade && $currentRolePriceName != 'none'){
								$price = floatval(trim($aitThemeOptions->members->role3Price)) - floatval(trim($aitThemeOptions->members->$currentRolePriceName));
							} else {
								$price = trim($aitThemeOptions->members->role3Price);
							}
							$packageName = $wp_roles->role_names[$role];
						}
						break;
					case "directory_4":
						if(isset($aitThemeOptions->members->role4Price) && trim($aitThemeOptions->members->role4Price) !== '0') {
							$rolePrice = $aitThemeOptions->members->role4Price;
							$free = false;
							if($upgrade && $currentRolePriceName != 'none'){
								$price = floatval(trim($aitThemeOptions->members->role4Price)) - floatval(trim($aitThemeOptions->members->$currentRolePriceName));
							} else {
								$price = trim($aitThemeOptions->members->role4Price);
							}
							$packageName = $wp_roles->role_names[$role];
						}
						break;
					case "directory_5":
						if(isset($aitThemeOptions->members->role5Price) && trim($aitThemeOptions->members->role5Price) !== '0') {
							$rolePrice = $aitThemeOptions->members->role5Price;
							$free = false;
							if($upgrade && $currentRolePriceName != 'none'){
								$price = floatval(trim($aitThemeOptions->members->role5Price)) - floatval(trim($aitThemeOptions->members->$currentRolePriceName));
							} else {
								$price = trim($aitThemeOptions->members->role5Price);
							}
							$packageName = $wp_roles->role_names[$role];
						}
						break;
					default:
						break;
				}
				// non free
				if( isset($aitThemeOptions->members->enablePaypal) && (!$free) ){

					$currencyCode = (isset($aitThemeOptions->members->paypalCurrencyCode)) ? $aitThemeOptions->members->paypalCurrencyCode : 'USD';
					$sandbox = (isset($aitThemeOptions->members->paypalType) && $aitThemeOptions->members->paypalType == 'live') ? '' : 'sandbox.';
					$paymentName = (isset($aitThemeOptions->members->paypalPaymentName)) ? $aitThemeOptions->members->paypalPaymentName : __('Directory Package','ait');
					$paymentDescription = ($upgrade) ? __('Upgrade to ','ait') . $packageName : $packageName;

					if($upgrade){
						$paymentName .= __(' Upgrade','ait');
					}

					$returnUrl = ($upgrade) ? admin_url("/profile.php?dir-register=success&upgrade=1&role=".$role) : home_url("/?dir-register=success&role=".$role);
					$cancelUrl = ($upgrade) ? admin_url("/profile.php?dir-register=cancel&upgrade=1") : home_url("/?dir-register=cancel");
					$urlParams = array(
						'RETURNURL' => $returnUrl,
						'CANCELURL' => $cancelUrl
					);

					if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
						
						$periodName = 'role'.$roleNum.'Period';
						$period = __('year','ait');
						switch ($aitThemeOptions->members->$periodName) {
							case 'Year':
								$period = __('year','ait');
								break;
							case 'Month':
								$period = __('month','ait');
								break;
							case 'Week':
								$period = __('week','ait');
								break;
							case 'Day':
								$period = __('day','ait');
								break;
						}
						$recurringDescription = $rolePrice.' '.$currencyCode.' '.__('per','ait').' '.$period;
						$recurringDescriptionFull = $rolePrice.' '.$currencyCode.' '.__('per','ait').' '.$period.' '.__('for','ait').' '.$packageName;
						
						// Recurring payments
						$recurring = array(
							'L_BILLINGTYPE0' => 'RecurringPayments',
							'L_BILLINGAGREEMENTDESCRIPTION0' => $recurringDescriptionFull
						);
						$params = $urlParams + $recurring;

					} else {
						
						// Single payments
						$orderParams = array(
							'PAYMENTREQUEST_0_AMT' => $price,
							'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
							'PAYMENTREQUEST_0_CURRENCYCODE' => $currencyCode,
							'PAYMENTREQUEST_0_ITEMAMT' => $price
						);
						$itemParams = array(
							'L_PAYMENTREQUEST_0_NAME0' => $paymentName,
							'L_PAYMENTREQUEST_0_DESC0' => $paymentDescription,
							'L_PAYMENTREQUEST_0_AMT0' => $price,
							'L_PAYMENTREQUEST_0_QTY0' => '1'
						);
						$params = $urlParams + $orderParams + $itemParams;

					}
					
					$response = $paypal -> request('SetExpressCheckout',$params);

					$errors = new WP_Error();
					if(!$response){
						$errorMessage = __( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'ait' );
						$detailErrorMessage = reset($paypal->getErrors());
						$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
						$registerErrors = $errors;
					}
					
					// Request successful
					if(is_array($response) && $response['ACK'] == 'Success') {
						
						// write token to DB
						$token = $response['TOKEN'];
						update_user_meta($userId, 'ait_dir_reg_paypal_token', $token);
						update_user_meta($userId, 'ait_dir_reg_paypal_role', $role);

						// write recurring data
						if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {

							$type = ($upgrade) ? 'upgrade' : 'register';
							update_user_meta($userId, 'dir_paypal_recurring_profile_type',$type);

							update_user_meta($userId, 'dir_paypal_recurring_profile_amt',$rolePrice);
							update_user_meta($userId, 'dir_paypal_recurring_profile_init_amt',$price);
							update_user_meta($userId, 'dir_paypal_recurring_profile_period',$aitThemeOptions->members->$periodName);
							update_user_meta($userId, 'dir_paypal_recurring_profile_desc_full',$recurringDescriptionFull); 
							update_user_meta($userId, 'dir_paypal_recurring_profile_desc',$recurringDescription); 

						}

						// go to payment site
						header( 'Location: https://www.'.$sandbox.'paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
						die();

					} else {
						$errorMessage = __( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'ait' );
						$detailErrorMessage = (isset($response['L_LONGMESSAGE0'])) ? $response['L_LONGMESSAGE0'] : '';
						$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
						$registerErrors = $errors;
					}

				} else {

					// free
					$user = new WP_User( $userId );
					$user->set_role( $role );

					aitDirWriteActivationTime( $userId, $role );

					if($upgrade){
						// upgrade
						$registerMessages = __('Your directory account was upgraded!','ait');
					} else {
						$registerMessages = __('Your directory account was activated! Check your email address for password!','ait');
					}

				}
			}
		}
	}
	unset($_POST);
}

// check token (paypal merchant authorization) and Do Payment
if(isset($_GET['dir-register']) && ($_GET['dir-register'] == 'success') && !empty($_GET['token'])) {

	// find token
	global $wpdb, $registerErrors, $registerMessages;
	$token = $_GET['token'];
	$tokenRow = $wpdb->get_row( "SELECT * FROM $wpdb->usermeta WHERE meta_value = '$token'" );
	if($tokenRow){
		
		// get user id
		$userId = $tokenRow->user_id;
		// delete token from DB
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->usermeta WHERE meta_value = %s", $token ) );
		
		// get role
		$role = get_user_meta($userId,'ait_dir_reg_paypal_role',true);

		// get checkout details from token
		$checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));

		if( is_array($checkoutDetails) && ($checkoutDetails['ACK'] == 'Success') ) {
			
			// check if payment is recurring
			if (isset($checkoutDetails['BILLINGAGREEMENTACCEPTEDSTATUS']) && $checkoutDetails['BILLINGAGREEMENTACCEPTEDSTATUS']) {

				// Cancel old profile
				$oldProfile = get_user_meta($userId,'dir_paypal_recurring_profile_id',true);
				if (!empty($oldProfile)) {
					$cancelParams = array(
						'PROFILEID' => $oldProfile,
						'ACTION' => 'Cancel'
					);
					$paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
				}
				
				// $type = get_user_meta($userId,'dir_paypal_recurring_profile_type',true);
				// if (!empty($type) && ($type == 'upgrade')) {
				$initAmt = get_user_meta($userId,'dir_paypal_recurring_profile_init_amt',true);
				// } else {
				// 	$initAmt = '0';
				// }
				$amt = get_user_meta($userId,'dir_paypal_recurring_profile_amt',true);
				$currencyCode = (isset($aitThemeOptions->members->paypalCurrencyCode)) ? $aitThemeOptions->members->paypalCurrencyCode : 'USD';
				$description = get_user_meta($userId,'dir_paypal_recurring_profile_desc_full',true);
				$desc = get_user_meta($userId,'dir_paypal_recurring_profile_desc',true);
				$period = get_user_meta($userId,'dir_paypal_recurring_profile_period',true);

				$periodNum = (60 * 60 * 24 * 365);
				switch ($period) {
					case 'Year':
						$periodNum = (60 * 60 * 24 * 365);
						break;
					case 'Month':
						$periodNum = (60 * 60 * 24 * 30);
						break;
					case 'Week':
						$periodNum = (60 * 60 * 24 * 7);
						break;
					case 'Day':
						$periodNum = (60 * 60 * 24);
						break;
				}

				// if (!empty($type) && ($type == 'upgrade')) {
				$timeToBegin = time() + $periodNum;
				// } else {
				// 	$timeToBegin = time();
				// }
				$begins = date('Y-m-d',$timeToBegin).'T'.'00:00:00Z';

				// Recurring payment
				$recurringParams = array(
					'TOKEN' => $checkoutDetails['TOKEN'],
					'PAYERID' => $checkoutDetails['PAYERID'],
					'INITAMT' => $initAmt,
					'AMT' => $amt,
					'CURRENCYCODE' => $currencyCode,
					'DESC' => $description,
					'BILLINGPERIOD' => $period,
					'BILLINGFREQUENCY' => '1',
					'PROFILESTARTDATE' => $begins,
					'FAILEDINITAMTACTION' => 'CancelOnFailure',
					'AUTOBILLOUTAMT' => 'NoAutoBill',
					'MAXFAILEDPAYMENTS' => '0'
				);
				$recurringPayment = $paypal -> request('CreateRecurringPaymentsProfile', $recurringParams);

				// recurring profile created
				if( is_array($recurringPayment) && $recurringPayment['ACK'] == 'Success' ) {
					
					// write profile id to DB
					update_user_meta( $userId, 'dir_paypal_recurring_profile_id', $recurringPayment['PROFILEID'] );
					// set role
					$user = new WP_User( $userId );
					$user->set_role($role);
					// write description to DB
					update_user_meta( $userId, 'dir_paypal_recurring_profile_active_desc', $desc );

					// write activation time only for info
					// aitDirWriteActivationTime( $userId, $role );

					// show messages
					if(isset($_GET['upgrade'])){
						$registerMessages = __('PayPal recurring payments profile created. Your directory account was upgraded!','ait');
					} else {
						$registerMessages = __('PayPal recurring payments profile created. Your directory account was activated! Check your email address for password!','ait');
					}
				}

			} else {
				 
				//  Single payment
				$params = array(
					'TOKEN' => $checkoutDetails['TOKEN'],
					'PAYERID' => $checkoutDetails['PAYERID'],
					'PAYMENTACTION' => 'Sale',
					'PAYMENTREQUEST_0_AMT' => $checkoutDetails['PAYMENTREQUEST_0_AMT'], // Same amount as in the original request
					'PAYMENTREQUEST_0_CURRENCYCODE' => $checkoutDetails['CURRENCYCODE'] // Same currency as the original request
				);
				$singlePayment = $paypal -> request('DoExpressCheckoutPayment',$params);

				// IF PAYMENT OK
				if( is_array($singlePayment) && $singlePayment['ACK'] == 'Success') {
					
					// set role
					$user = new WP_User( $userId );
					$user->set_role($role);

					// write activation time
					aitDirWriteActivationTime( $userId, $role );

					// We'll fetch the transaction ID for internal bookkeeping
					$transactionId = $singlePayment['PAYMENTINFO_0_TRANSACTIONID'];
					update_user_meta( $userId, 'dir_paypal_transaction_id', $transactionId );

					// show messages
					if(isset($_GET['upgrade'])){
						$registerMessages = __('Your directory account was upgraded!','ait');
					} else {
						$registerMessages = __('Your directory account was activated! Check your email address for password!','ait');
					}

				}

			}

		}

	}
}

// delete token and show messages if user cancel payment 
if(isset($_GET['dir-register']) && ($_GET['dir-register'] == 'cancel') && isset($_GET['token'])){
	
	// delete token from DB
	global $wpdb;
	$token = $_GET['token'];
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->usermeta WHERE meta_value = %s", $token ) );

	// show message
	global $registerErrors;
	$errors = new WP_Error();
	if (isset($_GET['upgrade'])) {
		$message = __("You canceled payment. Your account wasn't changed.","ait");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	} else {
		$message = __("You canceled payment. Your account was registered but without option to add items. Upgrade your account in admin to add items.","ait");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	}
}

// get recurring payment details
if(isset($_GET['dir-recurring-check'])) {
	$registerMessages = (aitCheckPayPalSubscription($_GET['dir-recurring-check'])) ? __('PayPal recurring payments profile is active.','ait') : __("PayPal recurring payments profile isn't active.",'ait');
}

// check if recurring payment profile is active
function aitCheckPayPalSubscription($profileId) {
	global $paypal;
	$recurringCheck = $paypal -> request('GetRecurringPaymentsProfileDetails',array('PROFILEID' => $profileId));
	if( is_array($recurringCheck) && ($recurringCheck['ACK'] == 'Success') && ($recurringCheck['STATUS'] == 'Active' || $recurringCheck['STATUS'] == 'Pending')) {
		return true;
	} else {
		return false;
	}
}

/**
 * Generate upgrade account admin
 */
if ( !isset($GLOBALS['aitThemeOptions']->members->easyAdminEnable) ) {
	add_action('admin_menu', 'aitDirUpgradeDirectoryAccount');
	function aitDirUpgradeDirectoryAccount() {
		add_users_page(__('Directory Account','ait'), __('Directory Account','ait'), 'directory_account_update', 'dir-account', 'aitRenderDirectoryAccountPage');
	}
}
function aitRenderDirectoryAccountPage() {
	global $aitThemeOptions, $current_user;
	$user = new WP_User($current_user->ID);
	$usrRoles = $user->roles;

	if (!isset($aitThemeOptions->members->easyAdminEnable)) {
		echo '<div class="wrap">';
		echo '<div id="icon-users" class="icon32"><br></div>';
		echo '<h2>'.__('Directory Account','ait').'</h2>';
	}
	
	$firstRole = reset($usrRoles);
	if($firstRole){
		if (strpos($firstRole,'directory_') !== false) {
			$roleName = $GLOBALS['wp_roles']->role_names[$firstRole];
			$roleNumber = intval(substr($firstRole, 10));
			$roleCodePrice = 'role'.$roleNumber.'Price';
		} else {
			$roleName = __('None','ait');
			$roleNumber = 0;
			$roleCodePrice = 'none';
		}

		if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
			$recurringProfileId = get_user_meta($user->ID,'dir_paypal_recurring_profile_id',true);
			$recurringDescription = get_user_meta($user->ID,'dir_paypal_recurring_profile_active_desc',true);
			global $paypal;
			$recurringCheck = $paypal -> request('GetRecurringPaymentsProfileDetails',array('PROFILEID' => $recurringProfileId));
			if( is_array($recurringCheck) && ($recurringCheck['ACK'] == 'Success') ) {
				$recurringStatus = $recurringCheck['STATUS'];
			} else {
				$recurringStatus = __('Non-active','ait');
			}
		}

		if ( !isset($GLOBALS['aitThemeOptions']->members->easyAdminEnable) ) { ?>
		<form method="post" action="<?php echo admin_url('/profile.php?dir-register=upgrade'); ?>" class="wp-user-form">
		<?php } ?>

		<input type="hidden" name="user_id" value="<?php echo $user->ID; ?>">
		<table class="form-table">
		<tbody>
			<tr>
				<th><label for="user_account_type"><?php echo __('Account type','ait'); ?></label></th>
				<td><input type="text" name="user_account_type" id="user_account_type" value="<?php echo $roleName; ?>" disabled="disabled" class="regular-text"></td>
			</tr>
			<?php if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) { ?>
				<?php if (!empty($recurringDescription) && isDirectoryUser()) { ?>
				<tr>
					<th><label for="user_account_recurring_profile_desc"><?php echo __('PayPal profile','ait'); ?></label></th>
					<td><input type="text" name="user_account_recurring_profile_desc" id="user_account_recurring_profile_desc" value="<?php echo $recurringDescription; ?>" disabled="disabled" class="regular-text"></td>
				</tr>
				<?php } ?>
				<?php if (!empty($recurringProfileId) && isDirectoryUser()) { ?>
				<tr>
					<th><label for="user_account_recurring_profile_id"><?php echo __('PayPal profile ID','ait'); ?></label></th>
					<td><input type="text" name="user_account_recurring_profile_id" id="user_account_recurring_profile_id" value="<?php echo $recurringProfileId; ?>" disabled="disabled" class="regular-text"></td>
				</tr>
				<?php } ?>
				<tr>
					<th><label for="user_account_recurring_status"><?php echo __('PayPal profile status','ait'); ?></label></th>
					<td><input type="text" name="user_account_recurring_status" id="user_account_recurring_status" value="<?php echo $recurringStatus; ?>" disabled="disabled" class="regular-text"></td>
				</tr>
			<?php } else { ?>
				<tr>
					<th><label for="user_account_expiration"><?php echo __('Days left before expiration','ait'); ?></label></th>
					<td><input type="text" name="user_account_expiration" id="user_account_expiration" value="<?php echo aitDirGetDaysLeft(); ?>" disabled="disabled" class="regular-text"></td>
				</tr>
			<?php } ?>
			<?php if (!(isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring') && ($recurringStatus == 'Pending'))) { ?>
			<tr>
				<input type="hidden" name="directory-role-current-price" value="<?php echo $roleCodePrice; ?>">
				<?php
				global $aitThemeOptions;
				$output = '<th><label for="directory-role">'.__('Upgrade account','ait').'</label></th><td><select name="directory-role" id="ait_dir_user_account_update">';
				$currency = (isset($aitThemeOptions->members->paypalCurrencyCode)) ? $aitThemeOptions->members->paypalCurrencyCode : 'USD';
				$roleNumber++;
				$upCount = 0;
				for ($i=$roleNumber; $i <= 5; $i++) {
					$roleEnable = 'role'.$i.'Enable';
					$roleName = 'role'.$i.'Name';
					$rolePrice = 'role'.$i.'Price';
					if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
						$periodName = 'role'.$i.'Period';
						$rolePeriod = __('year','ait');
						switch ($aitThemeOptions->members->$periodName) {
							case 'Year':
								$rolePeriod = __('year','ait');
								break;
							case 'Month':
								$rolePeriod = __('month','ait');
								break;
							case 'Week':
								$rolePeriod = __('week','ait');
								break;
							case 'Day':
								$rolePeriod = __('day','ait');
								break;
						}
					}
					$free = (trim($aitThemeOptions->members->$rolePrice) == '0') ? true : false;
					if(isset($aitThemeOptions->members->$roleEnable)){
						$output.= '<option value="directory_'.$i.'"';
						if($free) { $output .= ' class="free"'; }
						$output .= '>'.$aitThemeOptions->members->$roleName;
						if(!$free) {
							if($roleCodePrice == 'none'){
								$upgradePrice = trim($aitThemeOptions->members->$rolePrice);
							} else {
								$upgradePrice = floatval(trim($aitThemeOptions->members->$rolePrice)) - floatval(trim($aitThemeOptions->members->$roleCodePrice));
							}
							if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
								$output .= ' - '.trim($aitThemeOptions->members->$rolePrice).' '.$currency.' '.__('per','ait').' '.$rolePeriod;
							} else {
								$output .= ' ('.$upgradePrice.' '.$currency.')';
							}
						} else {
							$output .= ' ('.__('Free','ait').')';
						}
						$output .= '</option>';
						$upCount++;
					}
				}
				$output .= '</select></td>';
				if($upCount > 0) { echo $output; }
				?>
			</tr>
			<?php } ?>
		</tbody>
		</table>
		<?php if (!(isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring') && ($recurringStatus == 'Pending'))) { ?>
		<?php if($upCount > 0) { 
			echo '<p class="submit"><input type="submit" name="user-submit" data-form-url="'.admin_url('/profile.php?dir-register=upgrade').'" value="'.__('Upgrade Account', 'ait').'" class="user-submit button button-primary" /></p>';
		} }
		if ( isset($aitThemeOptions->members->easyAdminEnable) ) { ?>
			<div class="icon32" id="icon-profile"><br></div>
			<h2><?php _e('Profile','ait'); ?></h2>
		<?php } else { ?>
			</form>
		<?php }
	} else {
	}
	if ( !isset($aitThemeOptions->members->easyAdminEnable) ) echo '</div>';
}

// write activation time
add_action('set_user_role', 'aitDirWriteActivationTime',1,2);
function aitDirWriteActivationTime($id, $role) {

	global $wpdb;
	if($role == 'directory_1' || $role == 'directory_2' || $role == 'directory_3' || $role == 'directory_4' || $role == 'directory_5'){
		update_user_meta( $id, 'dir_activation_time', array( 'role' => $role, 'time' => time()) );
		// expired posts back to published
		$wpdb->query($wpdb->prepare( "UPDATE $wpdb->posts SET post_status = 'publish' WHERE post_author = %d AND post_status = 'expired'", intval($id)) );
	}

}

// Accounts expiration - schedule the accounts check daily
if( !wp_next_scheduled( 'ait_check_user_expirations' ) ) {
	wp_schedule_event( time(), 'daily', 'ait_check_user_expirations' );
}
add_action( 'ait_check_user_expirations', 'aitDirCheckUsersExpirations' );
function aitDirCheckUsersExpirations() {
	global $aitThemeOptions, $wpdb;
	if(isset($aitThemeOptions->members)){
		// recurring payments - expire inactive subscriptions
		if (isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
			$users = $wpdb->get_results("SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'dir_paypal_recurring_profile_id'");
			foreach ($users as $user) {
				if (!aitCheckPayPalSubscription($user->meta_value)) {
					aitDirExpireUser($user->user_id);
				}
			}
		}
		// single payments
		$times = $wpdb->get_results("SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'dir_activation_time'");
		foreach ($times as $time) {
			$data = unserialize($time->meta_value);

			$timeInSec = $data['time'];
			$role = $data['role'];

			$differenceInSec = time() - $timeInSec;
			$differenceInDays = floor($differenceInSec / 60 / 60 / 24);

			if($role == 'directory_1' && isset($aitThemeOptions->members->role1Time) && trim($aitThemeOptions->members->role1Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role1Time);
				if($differenceInDays >= $limit){
					aitDirExpireUser($time->user_id);
				}
			}
			if($role == 'directory_2' && isset($aitThemeOptions->members->role2Time) && trim($aitThemeOptions->members->role2Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role2Time);
				if($differenceInDays >= $limit){
					aitDirExpireUser($time->user_id);
				}
			}
			if($role == 'directory_3' && isset($aitThemeOptions->members->role3Time) && trim($aitThemeOptions->members->role3Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role3Time);
				if($differenceInDays >= $limit){
					aitDirExpireUser($time->user_id);
				}
			}
			if($role == 'directory_4' && isset($aitThemeOptions->members->role4Time) && trim($aitThemeOptions->members->role4Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role4Time);
				if($differenceInDays >= $limit){
					aitDirExpireUser($time->user_id);
				}
			}
			if($role == 'directory_5' && isset($aitThemeOptions->members->role5Time) && trim($aitThemeOptions->members->role5Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role5Time);
				if($differenceInDays >= $limit){
					aitDirExpireUser($time->user_id);
				}
			}
		}
	}
}

// chcek paypal subscription at startup
add_action('admin_init','aitDirCheckAccountLogedUser');
function aitDirCheckAccountLogedUser() {
	global $aitThemeOptions, $current_user;
	if (isDirectoryUser() && isset($aitThemeOptions->members->paypalPaymentType) && ($aitThemeOptions->members->paypalPaymentType == 'recurring')) {
		$profileId = get_user_meta($current_user->ID,'dir_paypal_recurring_profile_id',true);
		if ((!empty($profileId)) && (!aitCheckPayPalSubscription($profileId))) {
			aitDirExpireUser($current_user->ID);
		}
	}
}

function aitDirExpireUser($userId) {
	global $wpdb;
	$wpdb->query($wpdb->prepare( "UPDATE $wpdb->posts SET post_status = 'expired' WHERE post_author = %d AND post_status = 'publish'", intval($userId)) );
	$user = new WP_User( $userId );
	$user->set_role('subscriber');
}

function aitDirGetDaysLeft($userIdToTest = null) {
	global $wpdb, $current_user, $aitThemeOptions;

	$userId = (isset($userIdToTest)) ? intval($userIdToTest) : $current_user->ID;

	$data = $wpdb->get_row("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = 'dir_activation_time' AND user_id = ".$userId);
	$data = (isset($data->meta_value)) ? unserialize($data->meta_value) : array();

	if (!empty($data)) {
		$roleNumber = substr($data['role'], 10);
		$optionName = 'role'.$roleNumber.'Time';
		$limit = (isset($aitThemeOptions->members->$optionName)) ? 
			intval($aitThemeOptions->members->$optionName) : 0;
		if($limit > 0){
			$timeInSec = $data['time'];
			$differenceInSec = ($limit * 60 * 60 * 24) - (time() - $timeInSec);
			$differenceInDays = ceil($differenceInSec / 60 / 60 / 24);
			if($differenceInDays <= 0){
				$differenceInDays = __('Expired','ait');
			}
		} else {
			$differenceInDays = __('Unlimited','ait');
		}
	} else {
		$differenceInDays = __('Unlimited','ait');
	}

	return $differenceInDays;
}

/**
 * Handles registering a new user.
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|WP_Error Either user's ID or error on failure.
 */
function aitRegisterDirectoryUser( $user_login, $user_email ) {
	$errors = new WP_Error();

	// registrations disabled
	if (!get_option( 'users_can_register' )){
		$errors->add( 'registrations_disabled',__('ERROR: User registration is currently not allowed.', 'ait') );
		return $errors;
	}

	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( 'ERROR: Please enter a username.' , 'ait') );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( 'ERROR: This username is invalid because it uses illegal characters. Please enter a valid username.' , 'ait') );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( 'ERROR: This username is already registered. Please choose another one.', 'ait' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( 'ERROR: Please type your e-mail address.', 'ait' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( 'ERROR: The email address isn&#8217;t correct.', 'ait' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( 'ERROR: This email is already registered, please choose another one.', 'ait' ) );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;

	$user_pass = wp_generate_password( 12, false);
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( 'ERROR: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'ait' ), get_option( 'admin_email' ) ) );
		return $errors;
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	wp_new_user_notification( $user_id, $user_pass );

	return $user_id;
}