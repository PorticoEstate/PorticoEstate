<?php
	if(isset($language) && isset($nls['charsets'][$language]))
	{
		header('Content-type: text/html; charset=' . $nls['charsets'][$language]);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">

<!--          Chora: Copyright 2000-2001, The Horde Project.           -->
<!-- Horde Project: http://horde.org/ | Chora: http://horde.org/chora/ -->
<!--     GNU Public License: http://www.fsf.org/copyleft/gpl.html      -->

<html>
<head>
<?php
	/* Print the page title. */
	$page_title = '';
	if(!empty($conf['sitename']))
	{
		$page_title .= $conf['sitename'];
	}
	if(!empty($title))
	{
		$page_title .= ' :: ' . $title;
	}
	if(!empty($refresh_time) && ($refresh_time > 0) && !empty($refresh_url))
	{
		echo "<meta http-equiv=\"refresh\" content=\"$refresh_time;url=$refresh_url\">\n";
	}
?>
<title><?php echo $page_title ?></title>
<link href="<?php echo $conf['horde']['paths']['root'] ?>/css.php?app=chora" rel="stylesheet" text="text/css" />
</head>
<?php
	if(!empty($js_onLoad))
	{
?>
<body onload="<?php echo $js_onLoad ?>">
<?php
	}
	else
	{
?>
<body>
<?php
	}
?>
