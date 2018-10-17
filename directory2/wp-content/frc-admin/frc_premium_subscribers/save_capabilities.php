<?php
    require_once("../../wp-config.php");
	require_once('../../wp-load.php');
	global $wpdb;
    
    if(array_key_exists('user_id',$_REQUEST) && !empty($_REQUEST['user_id'])){
        $user_id = $_REQUEST['user_id'];
        $role_ids = $_REQUEST['id'];
        $Roles = array();
        foreach($role_ids as $key => $role_id){
            $Roles[$role_id] = 1; 
        }
        $RolesUpdt = serialize($Roles);
		//1 Updating in freerang_directory
		$currDB = "freerang_directory";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
        $qryUpdtPrm = $wpdb->update( 'gwr_usermeta',
									 array('meta_value' => $RolesUpdt),
									 array('user_id' => $user_id, 'meta_key' => 'gwr_capabilities') );
		
		//2 Updating in freerang_classified
        $currDB = "freerang_classified";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
        $qryUpdtCls = $wpdb->update( 'classified_usermeta',
									 array('meta_value' => $RolesUpdt),
									 array('user_id' => $user_id, 'meta_key' => 'classified_capabilities') );
		
		//3 Updating in freerang_premium
        $currDB = "freerang_premium";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
        $qryUpdtCls = $wpdb->update( 'classified_usermeta',
									 array('meta_value' => $RolesUpdt),
									 array('user_id' => $user_id, 'meta_key' => 'wp_capabilities') );
		
		//4 Updating in freerang_premium
        $currDB = "freerang_shop";
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, $currDB, DB_HOST);wp_cache_flush();
        $qryUpdtCls = $wpdb->update( 'classified_usermeta',
									 array('meta_value' => $RolesUpdt),
									 array('user_id' => $user_id, 'meta_key' => 'shop_capabilities') );
		
		$msg = array();
		if($qryUpdtPrm && $qryUpdtCls === false){
			$msg[] = 'Roles Not Updated';
		}else{
			$msg[] = 'Roles Updated';
		}
		echo json_encode($msg);
    }
?>
