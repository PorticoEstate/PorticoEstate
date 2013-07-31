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
			if ( $app == $currentapp)
			{
				$app_data['text'] = "[<b>{$app_data['text']}</b>]";
			}

			$applications[] = array
			(
				'value'=> array
				(
					'id'	=> $i,
					'label' => $app_data['text'],
				//	'href'	=> str_replace('&amp;','&', $app_data['url']) . '&phpgw_return_as=noframes',
					'href'	=> str_replace('&amp;','&', $app_data['url']),
					'expanded'	=> false
				),
				'children'	=> array()
			);

			$mapping[$i] = array
			(
				'id'		=> $i,
				'name'		=> $app,
				'expanded'	=> false,
				'highlight'	=> $app == $currentapp ? true : false
			);
				
			$i ++;
		}
		$applications = json_encode($applications);
		$mapping = json_encode($mapping);

$html = <<<HTML
		<div id="treeDiv1"></div>
		<script type="text/javascript">
		   var apps = {$applications};
		   var mapping = {$mapping};
			var proxy_data = ['first_element_is_dummy'];
		</script>
HTML;


$data = '["first_element_is_dummy",{"label":"Administrasjon","href":"/~sn5607/savannah_trunk/index.php?menuaction=admin.uimainscreen.mainscreen&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"1","expanded":false},{"label":"Kontakter","href":"/~sn5607/savannah_trunk/index.php?menuaction=addressbook.uiaddressbook.index&section=Persons&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"2","expanded":false},{"label":"Eiendom","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uitts.index&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"3","expanded":true},{"label":"SMS","href":"/~sn5607/savannah_trunk/index.php?menuaction=sms.uisms.index&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"4","expanded":false},{"label":"!bim","href":"/~sn5607/savannah_trunk/index.php?menuaction=bim.uibim.showModels&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"5","expanded":false},{"label":"Frontend","href":"/~sn5607/savannah_trunk/index.php?menuaction=frontend.uifrontend.index&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"6","expanded":false},{"label":"!messenger","href":"/~sn5607/savannah_trunk/index.php?menuaction=messenger.uimessenger.inbox&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"7","expanded":false},{"label":"Kompetanse styring","href":"/~sn5607/savannah_trunk/index.php?menuaction=hrm.uiuser.index&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"8","expanded":false},{"label":"Catch","href":"/~sn5607/savannah_trunk/index.php?menuaction=catch.uicatch.index&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"9","expanded":false},{"label":"Leie","href":"/~sn5607/savannah_trunk/index.php?menuaction=rental.uifrontpage.index&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"10","expanded":false},{"label":"Kontroll","href":"/~sn5607/savannah_trunk/index.php?menuaction=controller.uicontrol.control_list&click_history=63f5342fbfd7264eae6ecffa11a7b7c4","parent":"","id":"11","expanded":false},{"label":"Lokalisering","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uilocation.index&type_id=1&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"12","isLeaf":false,"expanded":true},{"label":"!IFC","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uiifc.import&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"13","isLeaf":false,"expanded":false},{"label":"Meldinger","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uitts.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"14","isLeaf":false,"expanded":false},{"label":"prosjekt","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uiproject.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"15","isLeaf":false,"expanded":false},{"label":"Planlagte oppgaver","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uievent.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"16","isLeaf":true,"expanded":false},{"label":"Faktura","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uiinvoice2.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"17","isLeaf":false,"expanded":false},{"label":"Budsjett","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uibudget.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"18","isLeaf":false,"expanded":false},{"label":"Avtale","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uiagreement.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"19","isLeaf":false,"expanded":false},{"label":"Dokumentasjon","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uidocument.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"20","isLeaf":false,"expanded":false},{"label":"Tilpasset","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uicustom.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"21","isLeaf":true,"expanded":false},{"label":"Brukerutstyr","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uientity.index&entity_id=1&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"22","isLeaf":false,"expanded":false},{"label":"Rapportering","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uientity.index&entity_id=2&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"23","isLeaf":false,"expanded":false},{"label":"Bygningsdelsregister","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uientity.index&entity_id=3&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"24","isLeaf":false,"expanded":false},{"label":"UtstyrsRegister::Ny","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uientity.index&entity_id=4&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"25","isLeaf":false,"expanded":false},{"label":"JasperReports","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uijasper.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"3","id":"26","isLeaf":true,"expanded":false},{"label":"Eiendom","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uilocation.index&type_id=1&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"12","id":"27","isLeaf":true,"expanded":false},{"label":"Bygg","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uilocation.index&type_id=2&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"12","id":"28","isLeaf":true,"expanded":false},{"label":"Etasje","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uilocation.index&type_id=3&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"12","id":"29","isLeaf":true,"expanded":false},{"label":"Bruksenhet","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uilocation.index&type_id=4&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"12","id":"30","isLeaf":true,"expanded":false},{"label":"Rom","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uilocation.index&type_id=5&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"12","id":"31","isLeaf":true,"expanded":false},{"label":"GrunnEiendom","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uigab.index&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"12","id":"32","isLeaf":true,"expanded":false},{"label":"Sammendrag","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uilocation.summary&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"12","id":"33","isLeaf":true,"expanded":false},{"label":"Rolle for Ansvarsmatrise","href":"/~sn5607/savannah_trunk/index.php?menuaction=property.uilocation.responsiblility_role&click_history=aa5941db8a41d50b64493e5c4ee3a625","parent":"12","id":"34","isLeaf":true,"expanded":false}]';

//_debug_array(json_decode($data));die();

// End--------------------------------------------------

	$GLOBALS['phpgw']->common->phpgw_header();
	echo parse_navbar();

	echo $html;


	$GLOBALS['phpgw']->common->phpgw_footer();


