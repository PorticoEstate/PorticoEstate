<?php
class ui_tools 
{
	var $public_functions = array
	(
		'color_picker' => true
	);

	function __construct()
	{
		$GLOBALS['phpgw']->xslttpl->add_file('form_elements', PHPGW_TEMPLATE_DIR);
	}

	/**
	 * Displays a color picker - designed to be used a popup
	 */
	function color_picker()
	{
		$GLOBALS['phpgw_info']['flags'] = array
		(
			'css' 			=> "import url({$GLOBALS['phpgw_info']['server']['webserver_url']}/js/yahoo/slider/examples/css/screen.css);\n",
			'currentapp'	=> 'phpgwapi',
			'noappheader'	=> true,
			'noappfooter'	=> true,
			'nofooter'		=> true,
			'noheader'		=> true,
			'nonavbar'		=> true,
		);

		$GLOBALS['phpgw']->js->validate_file('yahoo', 'YAHOO');
		$GLOBALS['phpgw']->js->validate_file('yahoo', 'log');
		$GLOBALS['phpgw']->js->validate_file('yahoo', 'color');
		$GLOBALS['phpgw']->js->validate_file('yahoo', 'event');
		$GLOBALS['phpgw']->js->validate_file('yahoo', 'dom');
		$GLOBALS['phpgw']->js->validate_file('yahoo', 'animation');
		$GLOBALS['phpgw']->js->validate_file('yahoo', 'dragdrop');
		$GLOBALS['phpgw']->js->validate_file('yahoo', 'slider');
		$GLOBALS['phpgw']->js->add_event('load', "standardSliderInit();\nrgbInit();\npickerInit();\n");
		/*
		<link rel="stylesheet" type="text/css" href="css/screen.css" />
		*/
		$GLOBALS['phpgw']->xslttpl->add_file('color_picker');

		$vals = array
		(
			'js_root' => "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/js/yahoo",
			'user_lang' => $GLOBALS['phpgw_info']['user']['preferences']['common']['lang']
		);

		$GLOBALS['phpgw']->xslttpl->set_var('color', array (
			'color_picker' => $vals
		));
		//exit(0);
	}

	function date($id, $label, $dateint = 0, $help = '', $class = '', $name = '', $disabled = false)
	{
		if ( !isset($GLOBALS['phpgw']->jscal) || !is_object($GLOBALS['phpgw']->jscal) )
		{
			$GLOBALS['phpgw']->jscal = createObject('phpgwapi.jscalendar');
		}

		$datestr = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $dateint);
		$elm = $this->textbox($id, $label, $datestr, $help, $class, $name, $disabled);
		$elm['lang_trigger'] = lang('calendar popup');
		$elm['type'] = 'date';
		$elm['img_trigger'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal', '.png');

		$GLOBALS['phpgw']->jscal->input($id);

		return $elm;
	}
	
	function password($id, $label, $help = '', $class = '', $name = '', $disabled = false)
	{
		$elm = $this->textbox($id, $label, '', $help, $class, $name, $disabled);
		$elm['type'] = 'password';
		return $elm;
	}
	
	function select($id, $label, $options, $selected = 0, $help = '', $class = '', $name = '', $disabled = false)
	{
		$elm = array
		(
			'id'		=> $id,
			'label'		=> $label,
			'options'	=> $this->select_list($options, $selected),
			'type'		=> 'select'
		);
		
		if ( $help )
		{
			$elm['help'] = $help;
		}

		if ( $class )
		{
			$elm['class'] = $class;
		}

		if ( $name )
		{
			$elm['name'] = $name;
		}
		else
		{
			$elm['name'] = $id;
		}

		if ( $disabled )
		{
			$elm['disbaled'] = 1;
		}
		return $elm;

	}

	/**
	 * Format and array properly to be used as a <select> list
	 * 
	 * @param array $input_list the array to be transformed
	 * @param int $selected the id to be "selected" (default value)
	 * @param string $id_key the array key for the id
	 * @param string $name_key the array key for the name 
	 */
	function select_list($input_list, $selected = 0, $id_key = 'id', $name_key = 'name') {
		$output_list = array ();
		if (isset ($input_list) && is_array($input_list)) {
			$i = 0;
			foreach ($input_list as $entry) {
				$output_list[$i] = array (
					'id' => $entry[$id_key],
					'name' => $entry[$name_key]
				);

				for ($j = count($selected) - 1; $j >= 0; -- $j) {
					if ($selected[$j] == $entry[$id_key]) {
						$output_list[$i]['selected'] = 'selected';
					}
				}
				++ $i;
			}
		}

		for ($i = count($output_list); $i >= 0; -- $i) {
			if (isset ($output_list[$i]['selected']) && $output_list[$i]['selected'] != 'selected') {
				unset ($output_list[$i]['selected']);
			}
		}
		return $output_list;
	}

	function textbox($id, $label, $value = '', $help = '', $class = '', $name = '', $disabled = false)
	{
		$elm = array
		(
			'id'	=> $id,
			'label'	=> $label,
			'type'	=> 'textbox'
		);
		
		if ( $value )
		{
			$elm['value'] = $value;
		}
		
		if ( $help )
		{
			$elm['help'] = $help;
		}

		if ( $class )
		{
			$elm['class'] = $class;
		}

		if ( $name )
		{
			$elm['name'] = $name;
		}
		else
		{
			$elm['name'] = $id;
		}

		if ( $disabled )
		{
			$elm['disbaled'] = 1;
		}
		return $elm;
	}
}

