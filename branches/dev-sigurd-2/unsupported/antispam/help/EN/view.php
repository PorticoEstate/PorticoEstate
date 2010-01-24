<?php
	$phpgw_flags = array
	(
		'currentapp'	=>	'manual'
	 );
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
	$font = $phpgw_info['theme']['font'];
?>

<img src="<?php echo $phpgw->common->image('antispam','navbar.png'); ?>" border="0">
<font face="<?php echo $font; ?>" size="2">
Sorry not yet available
</font>
<?php $phpgw->common->phpgw_footer(); ?>
