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
<img src="<?php echo $phpgw->common->image('addressbook','navbar.gif'); ?>" border="0">
<font face="<?php echo $font ?>" size="2"><p/>
�ӥ��ͥ��ѡ��ȥʡ���ͧ�ͤʤɤ�Ϣ���������ݴɤ��뤿��Υ��ɥ쥹Ģ�Ǥ���
<ul><li><b>�ɲ�	:</b><br/>
�ɲåܥ���򥯥�å�����ȡ����ι��ܤ����Ϥ��뤳�Ȥ��Ǥ��ޤ���
<table width="80%">
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font; ?>" size="2">
̾:<br/>
�Żҥ᡼��:<br/>
���������ֹ�:<br/>
��������ֹ�:<br/>
��������:<br/>
Į��:<br/>
�Զ�Į¼:<br/>
��ƻ�ܸ�:<br/>
͹���ֹ�:<br/>
����������:<br/>
���롼������:<br/>
�Ρ���:</td>
<td bgcolor="#ccddeb" width="50%" valign="top">
<font face="<?php echo $font; ?>" size="2">
��:<br/>
���̾:<br/>
FAX:<br/>
�ڡ����㡼�ֹ�:<br/>
����¾���ֹ�:<br/>
������:</td></table>
�ʤɤ�
�ƹ��ܤ����Ϥ����顢OK�ܥ���򥯥�å����ޤ���</li><p/></ul>
�ץ饤�١��ȥǡ����˥�����������ˤϡ����ѵ��ġʥ桼������ˤ����ꤹ��ɬ�פ�����ޤ���
�桼������ǤϤ��ʤ��������������ɥ쥹Ģ��¾�Υ桼������ɽ����������������뤳�Ȥ��Ǥ��륢�������������ꤹ�뤳�Ȥ��Ǥ��ޤ���
<p/>
<?php $phpgw->common->phpgw_footer(); ?>

