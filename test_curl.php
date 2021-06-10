<?php

	$organization_number = '988367095';
    $proxy = 'http://proxy.bergen.kommune.no:8080';
	
	$url = "https://data.brreg.no/enhetsregisteret/api/enheter/{$organization_number}";

	$ch		 = curl_init();
	if ($proxy)
	{
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'accept: application/json',
		'Content-Type: application/json',
		'Content-Length: 0'
		));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result	 = curl_exec($ch);

	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	$ret = json_decode($result, true);

echo '<pre>';    
print_r($ret);
echo '</pre>';
