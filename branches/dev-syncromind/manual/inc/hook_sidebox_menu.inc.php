<?php
	
	if(isset($GLOBALS['phpgw']->help->output['help_values']) && is_array($GLOBALS['phpgw']->help->output['help_values']))
	{
		foreach ($GLOBALS['phpgw']->help->output['help_values'] as $entry => $values)
		{
			//_debug_array($values['listbox']);
			display_sidebox($appname,$values['title'],$values['listbox'],$use_lang = false);
		}
	}
