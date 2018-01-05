<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></meta>
<link rel="stylesheet" href="layersmenu-demo.css" type="text/css"></link>
<link rel="stylesheet" href="layerstreemenu.css" type="text/css"></link>
<style type="text/css">
<!--
@import url("layerstreemenu-hidden.css");
//-->
</style>
<link rel="shortcut icon" href="LOGOS/shortcut_icon_phplm.png"></link>
<title>The PHP Layers Menu System</title>
<script language="JavaScript" type="text/javascript">
<!--
<?php include ("libjs/layersmenu-browser_detection.js"); ?>
// -->
</script>
<script language="JavaScript" type="text/javascript" src="libjs/layerstreemenu-cookies.js"></script>
</head>
<body>

<div class="normalbox">
<div class="normal" align="center">
<b>A file-based example with a JavaScript Tree Menu</b>
</div>
</div>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="20%" valign="top">
<div class="normalbox">
<div class="normal">
JavaScript Tree Menu
</div>
<?php
include ("lib/PHPLIB.php");
include ("lib/layersmenu-common.inc.php");
include ("lib/treemenu.inc.php");
$mid = new TreeMenu();
//$mid->setDirroot("./");
////$mid->setLibjsdir("./libjs/");
////$mid->setImgdir("./images/");
//$mid->setImgwww("images/");
$mid->setMenuStructureFile("layersmenu-vertical-1.txt");
$mid->parseStructureForMenu("treemenu1");
print $mid->newTreeMenu("treemenu1");
 /* alternatively:
$mid->newTreeMenu("treemenu1");
$mid->printTreeMenu("treemenu1");
*/
/* alternatively:
$mid->newTreeMenu("treemenu1");
$tree_menu1 = $mid->getTreeMenu("treemenu1");
print $tree_menu1;
*/
?>
</div>
<br />
<center>
<a href="http://phplayersmenu.sourceforge.net/"><img border="0"
src="LOGOS/powered_by_phplm.png" alt="Powered by PHP Layers Menu" height="31" width="88" /></a>
</center>
<br />
<center>
<a href="http://validator.w3.org/check/referer"><img border="0"
src="images/valid-xhtml10.png" alt="Valid XHTML 1.0!" height="31" width="88" /></a>
</center>
<br />
<center>
<a href="http://jigsaw.w3.org/css-validator/"><img border="0"
src="images/vcss.png" alt="Valid CSS!" height="31" width="88" /></a>
</center>
</td>
<td valign="top">
<div class="normalbox">
<div class="normal">
<?php include ("README.ihtml"); ?>
</div>
</div>
</td>
</tr>
</table>

</body>
</html>
