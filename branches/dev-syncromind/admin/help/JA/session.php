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
		'currentapp'	=> 'manual',
		'admin_header'	=> True,
		'enable_utilities_class'	=> True
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
	$appname = 'admin';
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border=0> 
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2">
<p>
���ε�ǽ�ϡ��̾盧�Υ����ƥ�Υ����ƥ�����ԤΤ����Ѳ�ǽ�Ǥ���
�����ƥ�����Ԥϡ����٤ƤΥ��ץꥱ������󡢥桼���ȥ��롼�פΥ�������ȡ����å����������ޤ���
<ul>
<li><b>���å�������:</b>
<p><i>���å���󻲾�:</i>
<br>���ߤΥ��å����Ρ�IP���ɥ쥹���������֡������ɥ���֤ʤɤ�ɽ�����ޤ������å��������Ǥ��뤳�Ȥ��ǽ�Ǥ���
<p><i>��������������:</i>
<br>phpGroupWare�ؤΥ�����������ɽ�����ޤ���������ID,IP���ɥ쥹,���������,�������Ȼ���,���ѻ��֤�ɽ�����ޤ���
</ul>
