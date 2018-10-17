<?php


	
include('functions.php');
    $curlURL = "http://freerangecamping.co.nz/directory/wp-content/wlm_sync/sync_classified_shop.php";
	$ch = curl_init();
	curl_setopt_array($ch, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $curlURL,
						CURLOPT_USERAGENT => getRandomUserAgent()
					));
	$dirResponse = curl_exec($ch);
	curl_close ($ch );

$to      = 'kalyanrajiv@gmail.com';
$subject = 'Cron subject'.time();
$message = $dirResponse;
$headers = 'From: webmaster@freerangecamping.co.nz' . "\r\n" .
    'Reply-To: webmaster@freerangecamping.co.nz' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
mail($to, $subject, $message, $headers);
?>