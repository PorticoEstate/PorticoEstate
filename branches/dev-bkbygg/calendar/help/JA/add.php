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
	$font = $phpgw_info['theme']['font'];
?>
<img src="<?php echo $phpgw->common->image('calendar','navbar.gif'); ?>" border="0">
<font face="<?php echo $font; ?>" size="2"><p/>
����������ñ�̤Ǥθ�����ǽ�ʥ����������������塼�륢�ץꥱ�������Ǥ���ͥ���٤ι⤤���٥�Ȥ����ε�ǽ�������Ƥ��ޤ���<br/>
<ul><li><b>�����ɲ�:</b> <img src="<?php echo $phpgw->common->image('calendar','new.gif'); ?>"><br/>
��ʬ���Ȥ䥰�롼�פ�¾�Υ��С��Τ���ο�����ͽ����ɲä��뤿��ˡ����Υ�������򥯥�å����ޤ���
���ι��ܤ����Ϥ���ڡ�����ɽ������ޤ���
<table width="80%">
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font; ?>" size="2">
���סʥ����ȥ��:<br/>
�ܺ�:<br/>
��:<br/>
����:<br/>
����:<br/>
ͥ����:<br/>
����������:</td>
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font; ?>" size="2">
���롼��:<br/>
���ü�:<br/>
���֤�������:<br/>
���֤���λ��:<br/>
����:</td></table>
�ʤɤι��ܤ����Ϥ����¹ԥܥ���򥯥�å����ޤ���<br/>
<b>����:</b> ¾�Υ��ץꥱ�������������Ƥ��륢���������ʥץ饤�١��ȡ����롼�ס������Х�ˤ⡢���Υ��ץꥱ�������������Ƥ��ޤ���</li></ul><p/></font>
<?php $phpgw->common->phpgw_footer(); ?>
