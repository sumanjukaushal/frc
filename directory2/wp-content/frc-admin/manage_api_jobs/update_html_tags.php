<?php
    //Script URL: https://www.freerangecamping.co.nz/directory/wp-content/manage_api_jobs/update_html_tags.php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
    $postType = isOldTheme() ? 'ait-dir-item' : 'ait-item';
	$getSkippedQry = "SELECT `gp`.ID FROM `gwr_posts` as `gp` WHERE (`gp`.`post_status`='publish' or `gp`.`post_status`='draft') and `gp`.`post_type`='$postType' AND (`post_content` LIKE '%<b style=\"font-weight: 300;\">%' OR `post_content` LIKE '%<span style=\"font-weight: 300;\">%')";
	//SELECT `gp`.ID FROM `gwr_posts` as `gp` WHERE (`gp`.`post_status`='publish' or `gp`.`post_status`='draft') and `gp`.`post_type`='ait-dir-item' AND (`post_content` LIKE '%<b style="font-weight: 300;">%' OR `post_content` LIKE '%<span style="font-weight: 300;">%')
	$skippedObj = $wpdb->get_results($getSkippedQry);
	$skippedItemArr = array();
	foreach($skippedObj as $sngObj){
		$skippedItemArr[] = $sngObj->ID;
	}
	//print_r($skippedItemArr);die;
	frcExport($skippedItemArr);
    die( "Script executed successfully");
    
    function isOldTheme(){
        $return = array();
        $args = array('public' => true);
        $post_types = get_post_types($args);
        foreach( $post_types as $post_type ){
            $taxonomies = get_object_taxonomies( $post_type );
            if (!empty($taxonomies)) {
                $return[$post_type] = $taxonomies;
            }
        }
        $oldTheme = false;
        if(is_array($return) && array_key_exists('ait-dir-item', $return)){
             $oldTheme = true;
        }
        return $oldTheme;
   }
   
   function frcExport($skippedArr = array()){
		/*
		23707,23788,25069,25068,25066,25894,26240,26189,26181,26392,28334,28371,28373,28364,25945,26228,22946,28715,28717,28724,28734,28740,28742,28727,28743,28750,28832,28840,28870,28871,28852,28873,28880,28886,23205,28762,28176,28823,28833,28817,2243,3174,2978,9245,9246,9560,9537,9494,9439,9399,9400,9387,9748,9725,9726,9710,9711,9695,9678,9785,9778,9905,9908,10899,10676,10690,10639,10581,10463,11876,11870,11866,11873,11851,11857,11863,12201,12606,13789,13823,13904,15326,15323,15594,15537,16100,16088,16199,16186,16184,16179,16171,16165,16163,16162,16304,16290,16291,16287,16288,16285,16289,16279,16280,16281,16286,16276,16282,16283,16272,16278,16273,16275,16268,16270,16271,16266,16267,16244,16245,16247,16248,16249,16250,16444,16525,16957,17243,17676,17634,17473,17418,17895,18177,18017,19056,18936,18922,18923,13558,20725,20756,21239,21504,22606,23019,23021,23074,23195,23282,23370,23562,23568,23574,23833,22599,23090,24188,24189,24190,24191,21575,21762,23939,21881,21993,21718,20968,21001,24073,21855,21773,21802,21768,21462,22326,21012,25235,25256,22120,24102,25166,22164,22140,21788,25167,25173,25203,25226,25233,25246,25247,25257,25276,25324,25232,25352,25350,22877,25365,25361,25360,25359,25148,21807,25152,25428,25430,25422,25424,25426,25427,25432,25462,25413,25415,25496,25497,25562,25565,25603,25606,25607,25614,25683,25688,25776,25802,25861,25864,25855,25939,25949,25955,25961,25921,25314,25761,26004,26005,26007,26009,26011,26045,26049,26055,26061,26064,26074,26082,26083,26087,26084,26086,26091,26092,26093,26094,26095,26106,26130,26133,26145,26138,26151,26159,26173,26245,26252,26212,26088,26275,26281,26292,26293,26294,26295,26296,26310,26309,26319,26321,26327,26345,26365,26375,26381,26387,26388,26394,26396,26397,26402,26400,26403,26410,26411,26412,26416,26420,26423,26444,26461,26467,26501,26304,26536,20092,21086,21367,25751,26506,26522,26485,26525,26526,26459,26548,26554,26556,26558,26559,26563,26577,26583,26584,26591,26592,26593,26594,26608,26611,26614,26722,26720,26714,26713,26708,26710,26675,26669,26646,26652,26741,26740,26738,26618,25260,25274,26846,26751,26759,26760,26761,26772,26779,26792,26798,26801,26807,26814,26815,26816,26817,26818,26967,26973,26978,26979,26985,26239,27018,26542,26913,26633,27312,27313,27314,27322,27328,27334,27340,27352,27353,27439,27440,27441,27442,27452,27453,27454,27455,27456,27457,27458,27459,27460,27461,27462,27510,27511,27512,27513,27514,27515,27516,27517,27518,27507,27647,27648,27650,27656,26616,26624,26666,26786,27763,27783,27782,27779,27778,27772,27784,27785,27788,27805,27807,27808,27813,27396,27818,27824,27825,27826,27832,27870,27877,27883,27884,27890,27896,27901,27966,27998,27999,28037,27641,25157,28468,25870,28322,28320,28276,28265,28264,28259,28255,28247,28241,28190,28185,28179,28178,28177,28172,28171,28165,28143,28119,28118,28110,28275,28109,27037,29839,25188,28340,28336,28137,27964
		*/
		global  $wpdb;
		$items = new WP_Query( array(
									 'post_type' => 'ait-dir-item',
									 'post__in' => $skippedArr,
									 'post_status' => array('draft', 'public', 'future', 'trash', 'pending'),
									 'posts_per_page' => -1
									 )
							  );
		//echo $wpdb->last_query;print_r($skippedArr);print_r($items);
		//foreach ($items->posts as $key => $item){echo "\n<br/>".$item->ID;}die;
        $dataArr = array();
        foreach ($items->posts as $key => $item) {
			$post_id = $item->ID;
            //fwrite($handle, "\nQuery for item:".$item->ID."\n");
			$htmlTag = array(
								'<b style="font-weight: 300;">',
								'</b>',
								'<span style="font-weight: 300;">',
								'</span>',
							);
			//>>>>>>>>>>find tag to be replaced>>>>>>>>>>>
			$pos1 = 0;
			while (($pos1 = strpos($item->post_content, $htmlTag[0], $pos1)) !== false) {
				$pos1 = (int)$pos1;
				$pos2 = strpos($item->post_content, $htmlTag[1], $pos1);
				$pos2 = $pos2 + strlen($htmlTag[1]);
				if($pos2 && $pos2 > $pos1){
					$len = (int)$pos2 - (int)$pos1;
					$tgtStr1 = $tgtStr = substr($item->post_content, $pos1, $len);
					$replaceWith = str_replace(
												array($htmlTag[0], $htmlTag[1]),
												array("",""),
												$tgtStr
											);
				}
				$item->post_content = str_replace($tgtStr, $replaceWith, $item->post_content);
			}
			//<<<<<<<<<<<<<find tag to be replaced<<<<<<<<<<<<<
		   
			//>>>>>>>>>>find tag to be replaced>>>>>>>>>>>
			$pos1 = 0;
			while (($pos1 = strpos($item->post_content, $htmlTag[2], $pos1)) !== false) {
				$pos1 = (int)$pos1;
				$pos2 = strpos($item->post_content, $htmlTag[3], $pos1);
				$pos2 = $pos2 + strlen($htmlTag[3]);
				if($pos2 && $pos2 > $pos1){
					$len = (int)$pos2 - (int)$pos1;
					$tgtStr1 = $tgtStr = substr($item->post_content, $pos1, $len);
					$replaceWith = str_replace(
								array($htmlTag[2], $htmlTag[3]),
								array("",""),
								$tgtStr
								);
				}
			   $item->post_content = str_replace($tgtStr, $replaceWith, $item->post_content);
			}
			//<<<<<<<<<<<<<find tag to be replaced<<<<<<<<<<<<<
			$wpdb->update( 
								   'gwr_posts', //table name
								   array('post_content' => $item->post_content), //data
								   array( 'ID' => $post_id ), //where
								   array( '%s'), //data format
								   array( '%d' ) //where format
							  );
			echo "\n<br/>\n<br/>";echo $wpdb->last_query;
			//$wpdb->query( $wpdb->last_query);
			echo "\n<br/>Record updated for post id: $post_id";
        }
    }
	 
	function mysql_escape_mimic($inp) { 
		if(is_array($inp)) 
			return array_map(__METHOD__, $inp); 
	
		if(!empty($inp) && is_string($inp)) { 
			return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
		} 
	
		return $inp; 
	}
	
	function mres($value){
		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
		$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
		return str_replace($search, $replace, $value);
	}
?>