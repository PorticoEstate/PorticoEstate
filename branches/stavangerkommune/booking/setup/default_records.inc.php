<?php
	switch ( $GLOBALS['phpgw_info']['server']['db_type'] )
	{
		case 'postgres':
			$GLOBALS['phpgw_setup']->oProc->query(
				"CREATE OR REPLACE VIEW bb_document_view ".
				"AS SELECT bb_document.id AS id, bb_document.name AS name, bb_document.owner_id AS owner_id, bb_document.category AS category, bb_document.description AS description, bb_document.type AS type ".
				"FROM ". 
					"((SELECT *, 'building' as type from bb_document_building) UNION ALL (SELECT *, 'resource' as type from bb_document_resource)) ".
				"as bb_document;"
			);

			$GLOBALS['phpgw_setup']->oProc->query(
				"CREATE OR REPLACE VIEW bb_application_association AS ".
				"SELECT 'booking' AS type, application_id, id, from_, to_, active FROM bb_booking WHERE application_id IS NOT NULL ".
				"UNION ".
				"SELECT 'allocation' AS type, application_id, id, from_, to_, active FROM bb_allocation  WHERE application_id IS NOT NULL ".
				"UNION ".
				"SELECT 'event' AS type, application_id, id, from_, to_, active FROM bb_event  WHERE application_id IS NOT NULL"
			);
			break;
		default:
			//do nothing for now
	}

	// Insert start values for billing sequential numbers
	$oProc->query( "INSERT INTO bb_billing_sequential_number_generator ( name, value ) VALUES ( 'internal', 1 ), ( 'external', 1 )" );

