<?php
	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader'	 => true,
		'nonavbar'	 => true,
		'currentapp' => 'property'
	);

	include_once('../header.inc.php');

	$test = <<<HTML
<!DOCTYPE HTML><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<p>IKKE SVAR PÅ DENNE E-POSTEN. DITT SVAR VIL IKKE BLI LEST AV EN SAKSBEHANDLER.</p><p>Vedlagt følger utbetalingsvedtak for helserefusjon.</p><p>Endring av opplysninger om hvem som skal motta utbetalingsvedtak må sendes til Helfo via Altinn.<br>Du finner informasjon om dette på vår nettside: <a href="https://helfo.no/avtale/endre-avtale/slik-endrer-du-opplysninger-i-avtale-">Slik endrer du opplysninger i avtale</a></p><p>Med vennlig hilsen<br>Helfo<br><a href="https://www.helfo.no">www.helfo.no</a></p></html>
HTML;

	if(!mb_check_encoding($test, 'UTF-8'))
	{
		$test = utf8_encode($test);
	}

	$tidy_options = array(
		'indent'						 => 2,
		'output-xhtml'					 => true,
		'drop-font-tags'				 => true,
		'clean'							 => true,
		'merge-spans'					 => true,
		'drop-proprietary-attributes'	 => true,
		'char-encoding'					 => 'utf8'
	);

	if (class_exists('tidy'))
	{
		$tidy	 = new tidy;
		$test	 = $tidy->repairString($test);
		$tidy->parseString($test, $tidy_options, 'utf8');
		$test	 = $tidy->html();
	}

	$dom			 = new DOMDocument();
	$dom->recover	 = true;
	$dom->loadHTML($test);//, LIBXML_NOBLANKS | LIBXML_XINCLUDE  );
	$xpath			 = new DOMXPath($dom);
	$nodes			 = $xpath->query('//*[@style]');  // Find elements with a style attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('style'); // Remove style attribute
	}
	unset($node);
	$nodes = $xpath->query('//*[@class]');  // Find elements with a class attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('class'); // Remove class attribute
	}
	unset($node);
	$nodes = $xpath->query('//*[@lang]');  // Find elements with a lang attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('lang'); // Remove lang attribute
	}
	unset($node);
	$nodes = $xpath->query('//*[@align]');  // Find elements with a align attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('align'); // Remove align attribute
	}
	unset($node);
	$nodes = $xpath->query('//*[@size]');  // Find elements with a size attribute
	foreach ($nodes as $node)
	{
		$node->removeAttribute('size'); // Remove size attribute
	}
	unset($node);

//	$body = $dom->getElementsByTagName('body');
//   $body = $body->item(0);


	$test = $dom->saveHTML();

	if (class_exists('tidy'))
	{
		$tidy	 = new tidy;
		$tidy->parseString($test);
		$test	 = $tidy->body();
	//	$test =  phpgw::clean_html($test);
	}





	echo $test;
	die();

