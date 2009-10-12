<?php
	/**************************************************************************\
	* phpGroupWare - Chat                                                      *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chat',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->db->query('SELECT * FROM phpgw_sessions ORDER BY session_lid');

	$size = $GLOBALS['phpgw']->db->nf();
	if (($size == 0) || ($size == 1))
	{
		$size = 2;
	}

	echo '<head><META HTTP-EQUIV="Refresh" Content="30" HREF="#bottom">
</head>';
?>

<body>
    <center><b><?php echo lang('Online'); ?>:</b>
    <form method="post" action="<?php echo $GLOBALS['phpgw']->link('/chat/load.php') ?>" target="_top">
    <input type="hidden" name="channel" value="<?php echo $channel; ?>">
    <input type="hidden" name="action" value="newprivate">
    <select name="channel" size="<?php echo $size; ?>">

<?php
	$GLOBALS['phpgw']->db->query('select distinct session_lid from phpgw_sessions ORDER BY session_lid');
	while($GLOBALS['phpgw']->db->next_record())
	{
		$user_name = $GLOBALS['phpgw']->db->f('session_lid');
		if(strpos($user_name,'@'))
		{
			$name = explode('@',$user_name);
			$user_name = $name[0];
		}
		echo '<option value="' . $user_name . '">' . $user_name."\n";
	}
?>

	</select><br>
	<input type="submit" value="<?php echo lang('Private Chat') ?>"></form>

	<a href="<?php echo $GLOBALS['phpgw']->link('/chat/users.php') ?>"><?php echo lang('Refresh Users') ?></a>
	</center><p>
</html>
</body>
