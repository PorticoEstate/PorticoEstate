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
	);
	$phpgw_info['flags'] = $phpgw_flags;
	include('../../../header.inc.php');
	$appname = 'admin';
?>
<img src="<?php echo $phpgw->common->image($appname,'navbar.gif'); ?>" border=0> 
<font face="<?php echo $phpgw_info['theme']['font']; ?>" size="2"><p/>
���ε�ǽ�ϡ��̾盧�Υ����ƥ�Υ����ƥ�����ԤΤ����Ѳ�ǽ�Ǥ���
�����ƥ�����Ԥϡ����٤ƤΥ��ץꥱ������󡢥桼���ȥ��롼�פΥ�������ȡ����å����������ޤ���

<ul>
<li><b>��������ȴ���:</b><br/>
<i>�桼�����������:</i><br/>
�桼����������Ȥ��ɲá�������������뤳�Ȥ��Ǥ��ޤ������롼�פ�°������С���
��䡢���ץꥱ�������Υ�����������������ǽ�Ǥ���<br/>
<i>�桼�����롼��:</i><br/>
�桼������°���륰�롼�פ��ɲá�����������뤳�Ȥ��Ǥ��ޤ���</li><p/>

<li><b>���å�������:</b><br/>
<i>���å���󻲾�:</i><br/>
���ߤΥ��å����Ρ�IP���ɥ쥹���������֡������ɥ���֤ʤɤ�ɽ�����ޤ������å��������Ǥ��뤳�Ȥ��ǽ�Ǥ���<br/>
<i>��������������:</i><br/>
phpGroupWare�ؤΥ�����������ɽ�����ޤ���������ID,IP���ɥ쥹,���������,�������Ȼ���,���ѻ��֤�ɽ�����ޤ���</li><p/>

<li><b>Headline sites:</b><br/>
Administer headline sites as seen by users in the headlines application.<br/>
<i>Edit:</i> Options for the headline sites:<br/>
Display,BaseURL, NewsFile,Minutes between reloads,Listing Displayed,News Type.<br/>
<i>Delete:</i>Remove an existing headling site, clicking on delete will give
you a checking page to be sure you do want to delete.<br/>
<i>View:</i>Displays set options as in edit.<br/>
<i>Add:</i>Form for adding new headline site, options as in edit.</li><p/>

<li><b>�ͥåȥ˥塼��:</b><br/>
�˥塼�����롼�פι�������򤷤ޤ���</li><p/>
<li><b>�����о���:</b><br/>
�����Ф�ư��Ƥ��� PHP �ξ����phpinfo() ��ɽ�����ޤ���</li><p/>
</ul></font>
