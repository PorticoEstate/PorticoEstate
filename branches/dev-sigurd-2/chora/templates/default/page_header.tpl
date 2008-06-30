<link href="<?php echo $phpgw->link('/chora/css.php') ?>" rel="stylesheet" type="text/css" /> 
<table width="100%" cellspacing="1" cellpadding="4" border="0">
<tr class="menuhead"><td>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr class="menuhead" valign="middle">
<td align="left" class="menuhead">
<span class="title"><?php echo $title ?></span>
</td>
<td align="right">
<table border="0" cellspacing="0" cellpadding="0"><tr><td align="right">
<img src="<?php echo graphic('chora.gif'); ?>" width="16" height="16" border="0" alt="Chora Homepage" />
 &nbsp;
</td>
<td align="left">
<a href="http://horde.org/chora/" class="menuhead">CHORA</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td></tr>
<tr class="menu"><td>
<table width="100%" cellspacing="0" cellpadding="1" border="0">
<tr><td>
Location: <b>
[ <a href="<?php echo $phpgw->link('/chora/cvs.php','&rt='.$rt) ?>"><?php echo $conf['options']['cvsRootName'] ?></a> ]

<?php
	while (list($null,$dir) = each($wherePath_arr))
	{
		$wherePath = $wherePath . '/' . $dir;
		if ($dir == 'Attic' || $dir=='')
		{
			continue;
		}
?>
/ <a href="<?php echo $phpgw->link('/chora/cvs.php','where=' . $wherePath) ?>"><?php echo $dir ?></a>
<?php
	}
?>
</b>
<?php
	if (isset($onb) && $onb)
	{
?>
&nbsp; &nbsp; <i>(Tracking Branch <b><?php echo $onb ?></b>)</i>
<?php
	}
?>
<?php
	if ($where == '' && sizeof($cvsroots) > 1)
	{
?>
&nbsp; &nbsp;
(<?php
		echo repositories();
?>)
<?php
	}
?>
</td>
<?php
	if (!empty($extraLink))
	{
?>
<td align="right">
<?php
		echo $extraLink;
?>
</td>
<?php
	}
?>
</tr>
</table>
</td></tr>
<?php
	if ($where == '' && @is_file($conf['paths']['introText']))
	{
?>
<tr class="info">
<td>
<?php
		@readfile($conf['paths']['introText']);
?>
</td>
</tr>
<?php
	}
?>
</table>
