<?php

	$GLOBALS['phpgw_info'] = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'                => true,
		'nonavbar'                => false,
		'currentapp'              => 'home',
		'enable_network_class'    => true,
		'enable_contacts_class'   => true,
		'enable_nextmatchs_class' => true
	);


	include_once('../header.inc.php');

// Start-------------------------------------------------

	phpgw::import_class('phpgwapi.yui');
	$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/examples/treeview/assets/css/folders/tree.css');
	phpgwapi_yui::load_widget('treeview');
	phpgwapi_yui::load_widget('cookie');
	$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'test.menu', 'property' );

		$currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
		$applications = array();
		$mapping = array(0 => array('name' => 'first_element_is_dummy'));
		$exclude = array('home', 'preferences', 'about', 'logout');
		$navbar = execMethod('phpgwapi.menu.get', 'navbar');

		$i = 1;
		foreach ( $navbar as $app => $app_data )
		{
			if ( in_array($app, $exclude) )
			{
				continue;
			}

			$applications[] = array
			(
				'value'=> array
				(
					'id'	=> $i,
					'app'	=> $app,
					'label' => $app_data['text'],
				//	'href'	=> str_replace('&amp;','&', $app_data['url']) . '&phpgw_return_as=noframes',
					'href'	=> str_replace('&amp;','&', $app_data['url']),
				),
				'children'	=> array()
			);

			$mapping[$i] = array
			(
				'id'		=> $i,
				'name'		=> $app,
				'expanded'	=> false,
				'highlight'	=> true,//$app == $currentapp ? true : false,
				'is_leaf'	=> false
			);
				
			$i ++;
		}
		$applications = json_encode($applications);
		$mapping = json_encode($mapping);

$html = <<<HTML
		<div id="MenutreeDiv1"></div>
		<script type="text/javascript">
		   var apps = {$applications};
		   var mapping = {$mapping};
			var proxy_data = ['first_element_is_dummy'];
		</script>
HTML;


// End--------------------------------------------------

	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();

	echo $html;


	$GLOBALS['phpgw']->common->phpgw_footer();


