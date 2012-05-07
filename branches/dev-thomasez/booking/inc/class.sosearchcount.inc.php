<?php
	phpgw::import_class('booking.socommon');
	
	/*
	*	Class for search counter. Refers to database table bb_searchcount
	*	Database object has these attribues:
	*	id - sequence number
	*	term - a term that is being searched for
	*	period - YYYYMM, which month/year the search is done
	*/
	class booking_searchcount extends booking_socommon
	{
		function __construct()
		{
			parent::__construct( 'bb_searchcount', 
				array(
					'id'		=> array( 'type' => 'int'),
					'term'	=> array( 'type' => 'string', 'query' => true),
					'period'	=> array( 'type' => 'int', 'required' => true),
					'count'	=> array( 'type' => 'int', 'required' => true ),
				)
			);
		}

		function increaseTerm( $term, $period = -1 ) {

			// Set period to now if not specified
			if( $period == -1 ) $period = date( "Ym" );

			// Make sure term always is lower case. We can skip this if phpGroupware supports CITEXT?
			$term = strtolower( $term );

			// Check for existing record
			$query = "SELECT count FROM bb_searchcount WHERE term='" . $term . "' AND period=" . $period . ";";
//			error_log( $query );
			$this->db->query( $query, __LINE__, __FILE__ );
			if( $GLOBALS['phpgw']->db->next_record())
			{
				// $previousCount = $GLOBALS['phpgw']->db->f( 'count', false );
				$this->db->query( "UPDATE bb_searchcount SET count=count+1 WHERE term='" . $term . "' AND period=" . $period . ";", __LINE__, __FILE__ );
			} else {
				$this->db->query( "INSERT INTO bb_searchcount ( term, period, count ) VALUES ( '" . $term . "', " . $period . ", 1 );" );
			}
		}
	}
