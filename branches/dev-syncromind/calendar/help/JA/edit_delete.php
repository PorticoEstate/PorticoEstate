<?php
  /**************************************************************************\
  * phpGroupWare - User manual                                               *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	$phpgw_flags = Array(
		'currentapp'	=> 'manual'
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
?>
<img src="<?php echo $phpgw->common->image('calendar','navbar.gif'); ?>" border="0">
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2"><p/>
����������ñ�̤Ǥθ�����ǽ�ʥ����������������塼�륢�ץꥱ�������Ǥ���ͥ���٤ι⤤���٥�Ȥ����ε�ǽ�������Ƥ��ޤ���<br/>
<ul><li><b>����:���</b>&nbsp&nbsp<img src="<?php echo $phpgw->common->image('calendar','circle.gif'); ?>"><br/>
ͽ����������뤿��ˡ����Υ�������򥯥�å����ޤ���
��������ͽ�꤬ɽ�����졢��������������򤹤�ܥ���ɽ������ޤ���<br/>
<b>����:</b>���������ϡ���ʬ�Ǻ���������Τ˸¤�ޤ���</li><p/></ul></font>
<?php $phpgw->common->phpgw_footer(); ?>
