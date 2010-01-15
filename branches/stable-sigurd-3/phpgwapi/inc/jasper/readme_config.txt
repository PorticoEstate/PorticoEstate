
//Generate config-file on the fly
// Start-------------------------------------------------
	$report_name		= 'report_participants';
	$report_source		= '/var/www/bk/dev-sigurd-2/booking/jasper/templates/participants.jrxml';			

	$memory = xmlwriter_open_memory();
	xmlwriter_start_document($memory,'1.0','UTF-8');

	xmlwriter_start_element ($memory,'JasperConfig'); // <JasperConfig>
		xmlwriter_start_element ($memory,'Reports'); // <Reports>	
			xmlwriter_start_element ($memory,'Report'); // <Report>			
				xmlwriter_write_attribute( $memory, 'name', $report_name);
				xmlwriter_write_attribute( $memory, 'source', $report_source);
			xmlwriter_end_element($memory); // </Report>
		xmlwriter_end_element($memory); // </Reports>
	xmlwriter_end_element($memory); // </JasperConfig>
	$xml = xmlwriter_output_memory($memory,true);

// Slutt-------------------------------

