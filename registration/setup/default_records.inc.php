<?php
	/*	 * ************************************************************************\
	 * phpGroupWare - Setup                                                     *
	 * http://www.phpgroupware.org                                              *
	 * --------------------------------------------                             *
	 *  This program is free software; you can redistribute it and/or modify it *
	 *  under the terms of the GNU General Public License as published by the   *
	 *  Free Software Foundation; either version 2 of the License, or (at your  *
	 *  option) any later version.                                              *
	  \************************************************************************* */

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('bday','Birthday','birthday','','Y',1)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('email','E-Mail','email','','Y',2)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('n_given','First Name','first_name','','Y',3)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('n_family','Last Name','last_name','','Y',4)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('adr_one_street','Address','address','','Y',5)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('adr_one_locality','City','city','','Y',6)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('adr_one_region','State','state','','Y',7)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('adr_one_postalcode','ZIP/Postal','zip','','Y',8)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('adr_one_countryname','Country','country','','Y',9)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('tel_work','Phone','phone','','N',10)");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_reg_fields (field_name, field_text, field_type, field_values, field_required, field_order) VALUES ('gender','Gender','gender','','N',11)");

	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_config WHERE config_app='registration'");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app, config_name, config_value) VALUES ('registration','display_tos','True')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app, config_name, config_value) VALUES ('registration','activate_account','email')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app, config_name, config_value) VALUES ('registration','username_is','choice')");
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO phpgw_config (config_app, config_name, config_value) VALUES ('registration','password_is','choice')");

	$asyncservice = CreateObject('phpgwapi.asyncservice');
	$asyncservice->set_timer(
		array('hour' => "*/2"), 'registration_clear_reg_accounts', 'registration.hook_helper.clear_reg_accounts', null
	);
