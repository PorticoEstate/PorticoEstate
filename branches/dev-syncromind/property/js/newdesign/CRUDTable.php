<?php

$task=$_POST['task'] ;
switch($task) {
case 'deleteitem':
	DeleteItem();
	break;
case 'find':
	Find();
	break;

}

function DeleteItem()
{
	$Id=$_POST['id'] ;
	echo $Id;
}

function Find()
{
	$payments=$_POST['payments'] ;
	$address=$_POST['address'] ;
	$prodid=$_POST['prodid'] ;
	$gaardsnr=$_POST['gaardsnr'] ;
	$bruksnr=$_POST['bruksnr'] ;
	$festenr=$_POST['festenr'] ;
	$seksjonsnr=$_POST['seksjonsnr'] ;

	$filterfields =  array ($payments,$address,$prodid,$gaardsnr,$bruksnr,$festenr,$seksjonsnr);

	$text = "";
	foreach($filterfields as $filter)
	{
		if ($filter != "") $text .=$filter ;

	}
	echo $text;


}






?>


