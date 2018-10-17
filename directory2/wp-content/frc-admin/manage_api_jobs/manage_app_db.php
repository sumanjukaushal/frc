<?php
	//PHP 5.6.30 (cli) (built: Feb 23 2017 16:25:57) 
    //Script URL: http://freerangecamping.co.nz/directory/wp-content/manage_api_jobs/manage_app_db.php
    require_once("../../wp-config.php");
    require_once('../../wp-load.php');
	$appendToTable = '_07mar_2';//_2feb
    define('CAMP_SITE_TABLE', "camp_sites{$appendToTable}");
	define('TRACK_RECORD_TABLE', "`rasu_track_records{$appendToTable}`");
	define('SITE_PHOTOS_TABLE', "site_photos{$appendToTable}");
	$db = new SQLite3('/home/frcconz/public_html/directory/wp-content/uploads/20170306_db/frc.db');
	//equivalent to attach "/home/frcconz/public_html/directory/wp-content/uploads/20170306_db/frc.db" as db1;
	$results = $db->query("SELECT `site_id`, `site_name` FROM `camp_sites` LIMIT 0, 20");
	phpinfo();
	$gzfile = "test.gz";
	$fp = gzopen ($gzfile, 'w9');
	gzwrite ($fp, file_get_contents('/home/frcconz/public_html/directory/wp-content/uploads/20170306_db/frc.db'));
	gzclose($fp);
	echo "<pre>";
	echo $num_columns =  $results->numColumns(); ;
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		print_r($row);
	}
	$results->finalize(); //closing the recordset
	//-----prepare block---------
	$query = $db->prepare('SELECT * FROM `camp_sites` WHERE site_id=:SITE_ID');
	$query->bindValue(':SITE_ID', '28450', SQLITE3_TEXT);
	
	// Execute the query
	$result = $query->execute();
	$row = $result->fetchArray(SQLITE3_ASSOC);
	print_r($row);
	//End------------------------
	$db->close(); //closing the db connection
	
	$zip = new ZipArchive;
	$filename = "/home/frcconz/public_html/directory/wp-content/uploads/20170306_db/test.zip";
	if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
		$zip->addFile('/home/frcconz/public_html/directory/wp-content/uploads/20170306_db/frc.db', 'newname.txt');
		$zip->close();
		echo 'ok';
	} else {
		echo 'failed';
	}
	/*if ($zip->open('/home/frcconz/public_html/directory/wp-content/uploads/20170306_db/test.zip') === TRUE) {
		$zip->addFile('/home/frcconz/public_html/directory/wp-content/uploads/20170306_db/frc.db', 'newname.txt');
		$zip->close();
		echo 'ok';
	} else {
		echo 'failed';
	}*/
	die;
	function selectAll($table){ 
        $results = $this->_dbConn->query("SELECT * FROM $table"); 
        $cols = $results->numColumns(); 
        while ($row = $results->fetchArray()) { 
            for ($i = 1; $i < $cols; $i++) { 
                echo $results->columnName($i) . ': '; 
                echo $row[$i] . '<br />'; 
            } 
            //print_r($row); 
            //echo 'Username: ' . $row['USERNAME'] . '<br />'; 
        } 
        $this->_dbConn->close(); 
    } 
?>