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
����ˤ��륢������򥯥�å�����ȡ������ʻ���ñ�̡ˡ���������Ӻ����ͽ���ɽ�����뤳�Ȥ��Ǥ��ޤ���<br/>
<ul><li><b>ɽ��:</b><img src="<?php echo $phpgw->common->image('calendar','today.gif'); ?>">���� <img src="<?php echo $phpgw->common->image('calendar','week.gif'); ?>">���� <img src="<?php echo $phpgw->common->image('calendar','month.gif'); ?>">���� <img src="<?php echo $phpgw->common->image('calendar','year.gif'); ?>">��ǯ<br/>
<i>����:</i><br/>
������ͽ������ñ�̤˶��ڤä�ɽ�����ޤ������ϻ��֤Ƚ�λ���֤ϡ��桼������ˤ����ꤷ�ޤ���<br/>
<i>����:</i><br/>
��ñ�̤�ͽ���ɽ�����ޤ������ν��������ϡ��桼������ˤ����ꤷ�ޤ���<br/>
<i>����:</i><br/>
��ñ�̤�ͽ���ɽ�����ޤ������ɽ���ϥǥե��������ȤʤäƤ��ޤ����������˥�󥯥�å��ǥ����������뤳�Ȥ��Ǥ��ޤ���<br/>
<i>��ǯ:</i><br/>
ǯñ�̤�ͽ���ɽ�����ޤ�����������ñ�̤Υ����������ǯʬɽ�����ޤ���<p/></li></ul></font>
<?php $phpgw->common->phpgw_footer(); ?>
