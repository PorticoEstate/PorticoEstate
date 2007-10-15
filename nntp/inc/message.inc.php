<?php
	function add_to_addressbook($addr)
	{
		$str = '<a href="'.$GLOBALS['phpgw']->link('/addressbook/add.php','add_email='.urlencode($addr->adl)).'" target="_new"><img src="'.$GLOBALS['phpgw']->common->image('email','sm_envelope.gif').'" width="10" height="8" alt="'.lang('add to addressbook').'" border="0" align="ABSMIDDLE"></a>';
		return $str;
	}

	function monitor($on,$folder)
	{
		if ($on)
		{
			$str = '<a href="'.$GLOBALS['phpgw']->link('/nntp/monitor.php','folder='.urlencode($folder)).'" target="_new"><img src="'.$GLOBALS['phpgw']->common->image('email','sm_envelope.gif').'" width="10" height="8" alt="'.lang('monitor').'" border="0" align="ABSMIDDLE"></a>';
		}
		else
		{
			$str = '<img src="'.$GLOBALS['phpgw']->common->image('email','sm_envelope.gif').'" width="10" height="8" alt="'.lang('monitor').'" border="0" align="ABSMIDDLE">';
		}
		return $str;
	}

	function send_to($addr,$folder)
	{
		$str = '<a href="'.$GLOBALS['phpgw']->link('/nntp/compose.php','folder='.urlencode($folder).'&to='.urlencode($addr->adl)).'">'.$GLOBALS['phpgw']->strip_html($addr->personal).'</a>';
		return $str;
	}
?>
