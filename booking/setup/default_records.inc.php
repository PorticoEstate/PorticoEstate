<?php
	switch ($GLOBALS['phpgw_info']['server']['db_type'])
	{
		case 'mssql':
		case 'mssqlnative':
		case 'mysql':
		case 'postgres':
			$GLOBALS['phpgw_setup']->oProc->query(
				"CREATE VIEW bb_document_view " .
				"AS SELECT bb_document.id AS id, bb_document.name AS name, bb_document.owner_id AS owner_id, bb_document.category AS category, bb_document.description AS description, bb_document.type AS type " .
				"FROM " .
				"((SELECT *, 'building' as type from bb_document_building) UNION ALL (SELECT *, 'resource' as type from bb_document_resource)) " .
				"as bb_document;"
			);

			$GLOBALS['phpgw_setup']->oProc->query(
				"CREATE VIEW bb_application_association AS " .
				"SELECT 'booking' AS type, application_id, id, from_, to_, cost, active FROM bb_booking WHERE application_id IS NOT NULL " .
				"UNION " .
				"SELECT 'allocation' AS type, application_id, id, from_, to_, cost, active FROM bb_allocation  WHERE application_id IS NOT NULL " .
				"UNION " .
				"SELECT 'event' AS type, application_id, id, from_, to_, cost, active FROM bb_event  WHERE application_id IS NOT NULL"
			);
			break;
		default:
		//do nothing for now
	}

	// Insert start values for billing sequential numbers
	$oProc->query("INSERT INTO bb_billing_sequential_number_generator ( name, value ) VALUES ( 'internal', 1 ), ( 'external', 1 )");

	$GLOBALS['phpgw']->locations->add('.admin', 'Admin section', 'booking');
	$GLOBALS['phpgw']->locations->add('.office', 'office', 'booking');
	$GLOBALS['phpgw']->locations->add('.office.user', 'office/user relation', 'booking', false, 'bb_office_user');
	$GLOBALS['phpgw']->db = clone($GLOBALS['phpgw_setup']->oProc->m_odb);

	$attrib = array
		(
		'appname' => 'booking',
		'location' => '.office.user',
		'column_name' => 'account_id',
		'input_text' => 'User',
		'statustext' => 'System user',
		'search' => true,
		'list' => true,
		'column_info' => array
			(
			'type' => 'user',
			'nullable' => 'False',
			'custom' => 1
		)
	);

	$GLOBALS['phpgw']->custom_fields->add($attrib, 'bb_office_user');

	$bb_activity = array(
		array("1", null,"Idrett", 'Idrett',"1"),
		array("2", null,"Kultur", 'Kultur',"1"),
		array("3", null,"Friluftsliv", 'Friluftsliv',"1"),
		array("4","1","Badminton", 'Badminton',"1"),
		array("5","1","Amerikansk fotball", 'Amerikansk fotball',"1"),
		array("6","1","Annen idrett", 'Annen idrett',"1"),
		array("7","1","Bandy - inne", 'Bandy - inne',"1"),
		array("8","1","Basketball", 'Basketball',"1"),
		array("9","1","Bedriftsidrett", 'Bedriftsidrett',"1"),
		array("10","1","Boksing", 'Boksing',"1"),
		array("11","1","Bordtennis", 'Bordtennis',"1"),
		array("12","1","Bryting", 'Bryting',"1"),
		array("13","1","Cheerleading", 'Cheerleading',"1"),
		array("14","1","Dansing", 'Dansing',"1"),
		array("15","1","Fotball", 'Fotball',"1"),
		array("16","1","Friidrett", 'Friidrett',"1"),
		array("17","1","Håndball", 'Håndball',"1"),
		array("18","1","Innebandy", 'Innebandy',"1"),
		array("19","1","Kampsport", 'Kampsport',"1"),
		array("20","1","Klatring", 'Klatring',"1"),
		array("21","1","Orientering", 'Orientering',"1"),
		array("22","1","Skisport", 'Skisport',"1"),
		array("23","1","Skyting", 'Skyting',"1"),
		array("24","1","Stuping", 'Stuping',"1"),
		array("25","1","Styrkeløfting", 'Styrkeløfting',"1"),
		array("26","1","Svømming", 'Svømming',"1"),
		array("27","1","Turn", 'Turn',"1"),
		array("28","1","Vannsport", 'Vannsport',"1"),
		array("29","1","Volleyball", 'Volleyball',"1"),
		array("30","1","Vektløfting", 'Vektløfting',"1"),
		array("31","2","Dans", 'Dans',"1"),
		array("32","2","Teater", 'Teater',"1"),
		array("33","2","Speidar", 'Speidar',"1"),
		array("34","2","Musikk / Korps", 'Musikk / Korps',"1"),
		array("35","2","Sosiale møteplassar", 'Sosiale møteplassar',"1"),
		array("36","2","Musikk", 'Musikk',"1"),
		array("37","2","Festivaler / Mønstringer", 'Festivaler / Mønstringer',"1"),
		array("38","2","Humanitære organiasjoner", 'Humanitære organiasjoner',"1"),
		array("39","2","Interesseorganiasjonar", 'Interesseorganiasjonar',"1"),
		array("40","2","Kor", 'Kor',"1"),
		array("41","2","Kulturlokaler", 'Kulturlokaler',"1"),
		array("42","2","Kulturlokaler formidling og øving", 'Kulturlokaler formidling og øving',"1"),
		array("43","2","Kulturlokaler øving og verksteder", 'Kulturlokaler øving og verksteder',"1"),
		array("44","2","Kulturvern og sogelag", 'Kulturvern og sogelag',"1"),
		array("45","2","Kunst, håndverk og media", 'Kunst, håndverk og media',"1"),
		array("46","2","Meningheter og trossamfunn", 'Meningheter og trossamfunn',"1"),
		array("47", null,"Annet", 'Annet',"1"),
//		array("48","47","Idrett møterom", 'Idrett møterom',"0"),
		array("49", 47,"Annet - Idrett møterom", 'Annet - Idrett møterom',"1"),
		array("50", 47,"Annet - Kultur møterom", 'Annet - Kultur møterom',"1"),
		array("51", 47,"Annet - Internt i kommunen", 'Annet - Internt i kommunen',"1"),
		array("52", 47,"Annet - Offentlig arrangement", 'Annet - Offentlig arrangement',"1"),
		array("53", 47,"Annet - Personlig markering", 'Annet - Personlig markering',"1"),
		array("54","1","PU/HU", 'PU/HU',"1"),
		array("55", null,"Skule","Skular i kommunen som bruker kommunale idrettsanlegg til idrett og arrangementer","1"),
		array("56","1","Idrettshall","Kommunal idrettshall","1"),
		array("57","1","Fleridrettslag","Idrettslag med flere idrettsgrener tilknytte laget.","1"),
		array("58","1","Fotballbane","Fotballbaner. Grus, kunstgras eller naturgrasbane","1"),
		array("59","1","Symjehall","Kommunal symjehall","1"),
		array("60","1","Gymsal","Gymsal tilknytta til skule","1"),
		array("61", null,"Undervisning/opplæring", 'Undervisning/opplæring',"1"),
		array("62", null,"Kommersiell utleige","Utleie til kommersielle arrangementer i kommunale bygg og idrettsanlegg","1"),
		array("63","1","Sykling","Sykling","1"),
		array("64","2","Grendalag", 'Grendalag',"1")
	);

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_activity ON', __LINE__, __FILE__);
	}

	foreach ($bb_activity as $value_set)
	{
		$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert($value_set);
		$sql = "INSERT INTO bb_activity (id, parent_id, name, description, active) VALUES ({$values})";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_activity OFF', __LINE__, __FILE__);
	}
	else if ($GLOBALS['phpgw_info']['server']['db_type'] == 'postgres')
	{
		$GLOBALS['phpgw_setup']->oProc->query("SELECT setval('seq_bb_activity', COALESCE((SELECT MAX(id)+1 FROM bb_activity), 1), false)", __LINE__, __FILE__);
	}
	else // mysql
	{
		$GLOBALS['phpgw_setup']->oProc->query('SELECT MAX(id)+1 AS next_value FROM bb_activity', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$_next_value = (int) $GLOBALS['phpgw_setup']->oProc->f('next_value');

		$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE bb_activity AUTO_INCREMENT = {$_next_value}", __LINE__, __FILE__);
	}



	$bb_agegroup = array(
		array(1,"Småbarn 0-5 år",0,"''",0,1),
		array(2,"Born 0-12 år",1,"Barn fra 0 til og med 12 år",1,1),
		array(3,"Ungdom 13-19 år",2,"''",1,1),
		array(4,"Vaksen 20- 59 år",4,"''",1,1),
		array(5,"Pensjonister",0,"''",0,1),
		array(6,"Unge voksne 20- 25 år",3,"''",0,1),
		array(7,"Senior 60+år",5,"''",1,1),
		array(8,"Publikum",6,"Her legger du inn estimert publikum.",1,1),
		array(9,"Møtedeltakare",8,"''",1,1),
		array(10,"Småbarn 0-5 år",0,"",0,2),
		array(11,"Born 0-12 år",1,"Barn fra 0 til og med 12 år",1,2),
		array(12,"Ungdom 13-19 år",2,"",1,2),
		array(13,"Vaksen 20- 59 år",4,"",1,2),
		array(14,"Pensjonister",0,"",0,2),
		array(15,"Unge voksne 20- 25 år",3,"",0,2),
		array(16,"Senior 60+år",5,"",1,2),
		array(17,"Publikum",6,"Her legger du inn estimert publikum.",1,2),
		array(18,"Møtedeltakare",8,"",1,2),
		array(19,"Småbarn 0-5 år",0,"",0,3),
		array(20,"Born 0-12 år",1,"Barn fra 0 til og med 12 år",1,3),
		array(21,"Ungdom 13-19 år",2,"",1,3),
		array(22,"Vaksen 20- 59 år",4,"",1,3),
		array(23,"Pensjonister",0,"",0,3),
		array(24,"Unge voksne 20- 25 år",3,"",0,3),
		array(25,"Senior 60+år",5,"",1,3),
		array(26,"Publikum",6,"Her legger du inn estimert publikum.",1,3),
		array(27,"Møtedeltakare",8,"",1,3),
	);

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_agegroup ON', __LINE__, __FILE__);
	}

	foreach ($bb_agegroup as $value_set)
	{
		$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert($value_set);
		$sql = "INSERT INTO bb_agegroup (id, name, sort, description, active, activity_id) VALUES ({$values})";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_agegroup OFF', __LINE__, __FILE__);
	}
	else if ($GLOBALS['phpgw_info']['server']['db_type'] == 'postgres')
	{
		$GLOBALS['phpgw_setup']->oProc->query("SELECT setval('seq_bb_agegroup', COALESCE((SELECT MAX(id)+1 FROM bb_agegroup), 1), false)", __LINE__, __FILE__);
	}
	else // mysql
	{
		$GLOBALS['phpgw_setup']->oProc->query('SELECT MAX(id)+1 AS next_value FROM bb_agegroup', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$_next_value = (int) $GLOBALS['phpgw_setup']->oProc->f('next_value');

		$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE bb_agegroup AUTO_INCREMENT = {$_next_value}", __LINE__, __FILE__);
	}

	$bb_targetaudience = array(
		array(1,"Fleirkulturelle",7,"''",0,1),
		array(2,"Born",1,"Barn fra 0 til og med 18 år",1,1),
		array(3,"Ungdom",2,"Ungdom 13 til og med 19 år",1,1),
		array(4,"Vaksen",3,"Vaksne mellom 20 - 59 år",1,1),
		array(5,"Utviklingshemma",5,"''",0,1),
		array(6,"Senior",4,"Senior fra 60 år",1,1),
		array(7,"Funksjonshemma",6,"Funksjonhemma",1,1),
		array(8,"Amatørkultur",9,"''",0,1),
		array(9,"Offentleg arrangement",10,"Arrangement i regi av det offentlege",0,1),
		array(10,"Profesjonell kultur",8,"''",0,1),
		array(11,"Toppidrett",7,"Idrett på topp nivå i Norge",0,1),
		array(12,"Publikum",12,"Publikum til stades",1,1),
		array(13,"Private arrangement",11,"Private arrangement",1,1),
		array(14,"Møte",9,"Møte i lokale",1,1),
		array(15,"Fleirkulturelle",7,"",0,2),
		array(16,"Born",1,"Barn fra 0 til og med 18 år",1,2),
		array(17,"Ungdom",2,"Ungdom 13 til og med 19 år",1,2),
		array(18,"Vaksen",3,"Vaksne mellom 20 - 59 år",1,2),
		array(19,"Utviklingshemma",5,"",0,2),
		array(20,"Senior",4,"Senior fra 60 år",1,2),
		array(21,"Funksjonshemma",6,"Funksjonhemma",1,2),
		array(22,"Amatørkultur",9,"",0,2),
		array(23,"Offentleg arrangement",10,"Arrangement i regi av det offentlege",0,2),
		array(24,"Profesjonell kultur",8,"",0,2),
		array(25,"Toppidrett",7,"Idrett på topp nivå i Norge",0,2),
		array(26,"Publikum",12,"Publikum til stades",1,2),
		array(27,"Private arrangement",11,"Private arrangement",1,2),
		array(28,"Møte",9,"Møte i lokale",1,2),
		array(29,"Fleirkulturelle",7,"",0,3),
		array(30,"Born",1,"Barn fra 0 til og med 18 år",1,3),
		array(31,"Ungdom",2,"Ungdom 13 til og med 19 år",1,3),
		array(32,"Vaksen",3,"Vaksne mellom 20 - 59 år",1,3),
		array(33,"Utviklingshemma",5,"",0,3),
		array(34,"Senior",4,"Senior fra 60 år",1,3),
		array(35,"Funksjonshemma",6,"Funksjonhemma",1,3),
		array(36,"Amatørkultur",9,"",0,3),
		array(37,"Offentleg arrangement",10,"Arrangement i regi av det offentlege",0,3),
		array(38,"Profesjonell kultur",8,"",0,3),
		array(39,"Toppidrett",7,"Idrett på topp nivå i Norge",0,3),
		array(40,"Publikum",12,"Publikum til stades",1,3),
		array(41,"Private arrangement",11,"Private arrangement",1,3),
		array(42,"Møte",9,"Møte i lokale",1,3),
	);

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_targetaudience ON', __LINE__, __FILE__);
	}
	foreach ($bb_targetaudience as $value_set)
	{
		$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert($value_set);
		$sql = "INSERT INTO bb_targetaudience (id, name, sort, description, active, activity_id) VALUES ({$values})";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_targetaudience OFF', __LINE__, __FILE__);
	}
	else if ($GLOBALS['phpgw_info']['server']['db_type'] == 'postgres')
	{
		$GLOBALS['phpgw_setup']->oProc->query("SELECT setval('seq_bb_targetaudience', COALESCE((SELECT MAX(id)+1 FROM bb_targetaudience), 1), false)", __LINE__, __FILE__);
	}
	else // mysql
	{
		$GLOBALS['phpgw_setup']->oProc->query('SELECT MAX(id)+1 AS next_value FROM bb_targetaudience', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$_next_value = (int) $GLOBALS['phpgw_setup']->oProc->f('next_value');

		$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE bb_targetaudience AUTO_INCREMENT = {$_next_value}", __LINE__, __FILE__);
	}

	// Default rescategory

	$bb_rescategory = array(
		array(1,"Lokale",1,1,1),
		array(2,"Utstyr",false, false,1),
	);

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_rescategory ON', __LINE__, __FILE__);
	}

	foreach ($bb_rescategory as $value_set)
	{
		$values	= $GLOBALS['phpgw_setup']->oProc->validate_insert($value_set);
		$sql = "INSERT INTO bb_rescategory (id, name, capacity, e_lock, active) VALUES ({$values})";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_rescategory OFF', __LINE__, __FILE__);
	}
	else if ($GLOBALS['phpgw_info']['server']['db_type'] == 'postgres')
	{
		$GLOBALS['phpgw_setup']->oProc->query("SELECT setval('seq_bb_rescategory', COALESCE((SELECT MAX(id)+1 FROM bb_rescategory), 1), false)", __LINE__, __FILE__);
	}
	else // mysql
	{
		$GLOBALS['phpgw_setup']->oProc->query('SELECT MAX(id)+1 AS next_value FROM bb_rescategory', __LINE__, __FILE__);
		$GLOBALS['phpgw_setup']->oProc->next_record();
		$_next_value = (int) $GLOBALS['phpgw_setup']->oProc->f('next_value');

		$GLOBALS['phpgw_setup']->oProc->query("ALTER TABLE bb_rescategory AUTO_INCREMENT = {$_next_value}", __LINE__, __FILE__);
	}

	$bb_rescategory_activity = array(1,2,3,47,55,61,62);

	foreach ($bb_rescategory_activity as $activity_id)
	{
		$sql = "INSERT INTO bb_rescategory_activity (rescategory_id, activity_id) VALUES (1, $activity_id)";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		$sql = "INSERT INTO bb_rescategory_activity (rescategory_id, activity_id) VALUES (2, $activity_id)";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}

// Default groups and users
	$GLOBALS['phpgw']->accounts = createObject('phpgwapi.accounts');
	$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');
	$GLOBALS['phpgw']->acl->enable_inheritance = true;


	$modules = array(
		'booking',
		'manual',
		'preferences',
		'property'
	);

	$aclobj = & $GLOBALS['phpgw']->acl;

	if (!$GLOBALS['phpgw']->accounts->exists('booking_group'))
	{
		$account = new phpgwapi_group();
		$account->lid = 'booking_group';
		$account->firstname = 'Booking';
		$account->lastname = 'Group';
		$booking_group = $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);
	}
	else
	{
		$booking_group = $GLOBALS['phpgw']->accounts->name2id('booking_group');
	}

	$aclobj->set_account_id($booking_group, true);
	$aclobj->add('booking', '.office', 7);
	$aclobj->add('booking', 'run', 1);
	$aclobj->add('property', '.', 1);
	$aclobj->add('property', 'run', 1);
	$aclobj->add('preferences', 'changepassword', 1);
	$aclobj->add('preferences', '.', 1);
	$aclobj->add('preferences', 'run', 1);
	$aclobj->save_repository();

	if (!$GLOBALS['phpgw']->accounts->exists('booking_admin'))
	{
		$account = new phpgwapi_group();
		$account->lid = 'booking_admin';
		$account->firstname = 'Booking Admin';
		$account->lastname = 'Group';
		$booking_admin = $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);
	}
	else
	{
		$booking_admin = $GLOBALS['phpgw']->accounts->name2id('booking_admin');
	}

	$aclobj->set_account_id($booking_admin, true);
	$aclobj->add('booking', 'run', 1);
	$aclobj->add('booking', 'admin', 15);
	$aclobj->add('booking', '.office', 15);
	$aclobj->add('property', '.admin', 15);
//	$aclobj->add('property', '.', 1);
	$aclobj->add('property', 'run', 1);
	$aclobj->add('property', '.admin_booking', 1);
	$aclobj->add('property', '.location', 15);
	$aclobj->add('property', '.owner', 15);
	$aclobj->add('preferences', 'changepassword', 1);
	$aclobj->add('preferences', '.', 1);
	$aclobj->add('preferences', 'run', 1);
	$aclobj->save_repository();

	$custom_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('booking', 'run'));

	$receipt_section_common = $custom_config->add_section(array
		(
			'name' => 'common_archive',
			'descr' => 'common archive config'
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_common['section_id'],
			'input_type'	=> 'listbox',
			'name'			=> 'method',
			'descr'			=> 'Export / import method',
			'choice'		=> array('public360','gi_arkiv'),
		//	'value'			=> '',
		)
	);

	$receipt_section_public360 = $custom_config->add_section(array
		(
			'name' => 'public360',
			'descr' => 'public360 archive config'
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_public360['section_id'],
			'input_type'	=> 'password',
			'name'			=> 'authkey',
			'descr'			=> 'authkey',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_public360['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'webservicehost',
			'descr'			=> 'webservicehost',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_public360['section_id'],
			'input_type'	=> 'listbox',
			'name'			=> 'debug',
			'descr'			=> 'debug',
			'choice'		=> array(1),
		)
	);

	$receipt_section_gi_arkiv = $custom_config->add_section(array
		(
			'name' => 'gi_arkiv',
			'descr' => 'Geointegrasjon arkiv'
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'webservicehost',
			'descr'			=> 'webservicehost',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'username',
			'descr'			=> 'username',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'password',
			'name'			=> 'password',
			'descr'			=> 'password',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'journalenhet',
			'descr'			=> 'journalenhet',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'arkivnoekkel',
			'descr'			=> 'arkivnoekkel',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'arkivnoekkel_text',
			'descr'			=> 'arkivnoekkel_text',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'fagsystem',
			'descr'			=> 'fagsystem',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'arkivdel',
			'descr'			=> 'arkivdel',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'sakspart_rolle',
			'descr'			=> 'sakspart_rolle',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'klientnavn',
			'descr'			=> 'klientnavn',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'klientversjon',
			'descr'			=> 'klientversjon',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'text',
			'name'			=> 'referanseoppsett',
			'descr'			=> 'referanseoppsett',
			'value'			=> '',
		)
	);

	$receipt = $custom_config->add_attrib(array
		(
			'section_id'	=> $receipt_section_gi_arkiv['section_id'],
			'input_type'	=> 'listbox',
			'name'			=> 'debug',
			'descr'			=> 'debug',
			'choice'		=> array(1),
		)
	);

	$asyncservice = CreateObject('phpgwapi.asyncservice');

	/**
	 * Reservation
	 */
	$asyncservice->set_timer(
		array('min' => "*/5"), 'booking_async_task_delete_expired_blocks', 'booking.async_task.doRun', array(
				'task_class' => "booking.async_task_delete_expired_blocks")
	);

	/**
	 * Billing
	 */
	$asyncservice->set_timer(
		array('hour' => "*/1"), 'booking_async_task_update_reservation_state', 'booking.async_task.doRun', array(
				'task_class' => "booking.async_task_update_reservation_state")
	);


	/**
	 * Participants
	 */
	$asyncservice->set_timer(
		array('day' => "*/1"), 'booking_async_task_delete_participants', 'booking.async_task.doRun', array(
				'task_class' => "booking.async_task_delete_participants")
	);

	/**
	 * The Anonymous frontend-user from access-log
	 */
	$asyncservice->set_timer(
		array('day' => "*/1"), 'booking_async_task_delete_access_log', 'booking.async_task.doRun', array(
				'task_class' => "booking.async_task_delete_access_log")
	);


	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_article_category ON', __LINE__, __FILE__);
	}

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO bb_article_category ( id, name)"
			. " VALUES (1, 'resource')", __LINE__, __FILE__);

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO bb_article_category ( id, name)"
			. " VALUES (2, 'service')", __LINE__, __FILE__);

	if (in_array($GLOBALS['phpgw_info']['server']['db_type'], array('mssql', 'mssqlnative')))
	{
		$GLOBALS['phpgw_setup']->oProc->query('SET identity_insert bb_article_category OFF', __LINE__, __FILE__);
	}


	switch ($GLOBALS['phpgw_info']['server']['db_type'])
	{
		case 'mssql':
		case 'mssqlnative':
			$concat_symbol = '+';
			break;
		case 'postgres':
			$concat_symbol = '||';
			break;
		default:
			$concat_symbol = '||';
		//do nothing for now
	}


	$GLOBALS['phpgw_setup']->oProc->m_odb->query(
		"CREATE VIEW bb_article_view
		 AS
		 SELECT bb_resource.id,
			bb_building.name {$concat_symbol} '::' {$concat_symbol} bb_resource.name as name,
			'Ressurs' || bb_resource.name as description,
			bb_resource.active,
			1 AS article_cat_id
		   FROM bb_resource
		   JOIN bb_building_resource ON bb_building_resource.resource_id = bb_resource.id
		   JOIN bb_building ON bb_building_resource.building_id = bb_building.id
		UNION
		 SELECT bb_service.id,
			bb_service.name,
			bb_service.description,
			bb_service.active,
			2 AS article_cat_id
		   FROM bb_service" , __LINE__, __FILE__
		);

	$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO bb_payment_method (id, payment_gateway_name, payment_gateway_mode) VALUES(1, 'Vipps', 'live')");
	$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO bb_payment_method (id, payment_gateway_name, payment_gateway_mode, is_default) VALUES(2, 'Etterfakturering', 'live', 1)");

	$text = <<<HTML
		<p>For å få tilgang til adgangskontrollsystemet SALTO - må den ansvarlige for bookingen laste ned en app til telefonen på forhånd.</p>
		<p>Nøkkel vil bli pushet ut til appen ca 10 minutt før avtaletidspunktet.</p>
		<p>Android:</p>
		<a href="https://play.google.com/store/apps/details?id=com.saltosystems.justin&hl=no&gl=US" target="_blank" rel="noreferrer noopener">https://play.google.com/store/apps/details?id=com.saltosystems.justin&hl=no&gl=US</a>
		<p>Apple:</p>
		<a href="https://apps.apple.com/no/app/justin-mobile/id960998088" target="_blank" rel="noreferrer noopener">https://apps.apple.com/no/app/justin-mobile/id960998088</a>
HTML;
	$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO bb_e_lock_system (id, name, sms_alert) VALUES(1, 'STANLEY', 1)");
	$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO bb_e_lock_system (id, name, sms_alert) VALUES(2, 'ARX', 1)");
	$GLOBALS['phpgw_setup']->oProc->m_odb->query("INSERT INTO bb_e_lock_system (id, name, instruction) VALUES(3, 'SALTO', '{$text}')");
