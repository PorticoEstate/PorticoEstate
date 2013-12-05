<?php

	/*
	* This class will update classification records baed on input.
	*/

	class lrs_el_anlegg extends property_boentity
	{

		function __construct()
		{
			parent::__construct();
			if($this->acl_location != '.entity.1.11')
			{
				throw new Exception("'lrs_el_anlegg'  is intended for location = '.entity.1.11'");
			}
		}

		function check_history($values,$values_attribute,$entity_id,$cat_id,$receipt)
		{
			$current_time = time() -2;
			
			$id = (int)$receipt['id'];
			foreach($values_attribute as $entry)
			{
				if($entry['name'] == 'auto_kontering')
				{
					$attrib_id = $entry['attrib_id'];
					$current_value = $entry['value'];
					break;
				}
			}

			$historylog = CreateObject('property.historylog','entity_1_11');
			$history_values = $historylog->return_array(array(),array('SO'),'history_timestamp','DESC',$id,$attrib_id);
			
			if(!$history_values)
			{
				return;
			}

			if(($history_values[0]['datetime'] > $current_time))
			{
				if((int)$history_values[0]['old_value'] != (int)$current_value)
				{
					//FIXME

					$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');

					$coordinator_name = $GLOBALS['phpgw_info']['user']['fullname'];
					$coordinator_email = "{$coordinator_name}<{$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}>";
					$bcc = '';
					$cc = $coordinator_email;

					$_to = $coordinator_email;
					
					$subject = 'Endring av EL-anlegg';
					
					$body = $this->get_xmldata($id,$current_value);

					$rcpt = $GLOBALS['phpgw']->send->msg('email', $_to, $subject, $body, '', $cc, $bcc, $coordinator_email, $coordinator_name, 'text', '', false , true);

				}
			}
		}

		protected function get_xmldata($id = 0, $current_value)
		{
			$this->db 	= & $GLOBALS['phpgw']->db;

			$id = (int) $id;
			$sql= "SELECT * FROM fm_entity_1_11 WHERE id = {$id}";

			$this->db->query($sql,__LINE__,__FILE__);
			$anlegg = array();

			$TreeID = $this->type;
			$PeriodFrom = date('Ym');
			if($current_value)
			{
				$PeriodTo = 209912;
			}
			else
			{
				$PeriodTo = '000000';			
			}

			$memory = xmlwriter_open_memory();
			xmlwriter_set_indent ( $memory , true );
			xmlwriter_start_document($memory,'1.0','UTF-8');
			xmlwriter_start_element($memory,'TreeListe');
				xmlwriter_write_attribute($memory,'TreeID',$TreeID);
				xmlwriter_write_attribute($memory,'xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
				xmlwriter_write_attribute($memory,'xsi:noNamespaceSchemaLocation','TreeListe.xsd');

			while ($this->db->next_record())
			{
				xmlwriter_start_element($memory,'Tree');
					xmlwriter_write_element($memory,'ID', 'TJ');
					xmlwriter_write_element($memory,'Verdi', substr($this->db->f('maalepunkt_id'),-8));
					xmlwriter_write_element($memory,'Beskrivelse', $this->db->f('address'));
					xmlwriter_write_element($memory,'Firma', 'BB');
					xmlwriter_write_element($memory,'PeriodeFra', $PeriodFrom);
					xmlwriter_write_element($memory,'PeriodeTil', $PeriodTo);
					xmlwriter_write_element($memory,'Status', 'N');
				    xmlwriter_start_element($memory,'BegrepsLister');
						xmlwriter_start_element($memory,'BegrepsListe');
						    xmlwriter_write_attribute($memory,'Prosent', 100);
							xmlwriter_start_element($memory,'Begrep');
								xmlwriter_write_comment($memory , 'Firma' );
								xmlwriter_write_element($memory,'ID', 'A3');
								xmlwriter_write_element($memory,'Verdi', 'BB');
							xmlwriter_end_element($memory);
							xmlwriter_start_element($memory,'Begrep');
								xmlwriter_write_comment($memory , 'Art' );
								xmlwriter_write_element($memory,'ID', 'A0');
								xmlwriter_write_element($memory,'Verdi', '12304121');
							xmlwriter_end_element($memory);
							xmlwriter_start_element($memory,'Begrep');
								xmlwriter_write_comment($memory , 'Asvar' );
								xmlwriter_write_element($memory,'ID', 'C1');
								xmlwriter_write_element($memory,'Verdi', 45);
							xmlwriter_end_element($memory);
							xmlwriter_start_element($memory,'Begrep');
								xmlwriter_write_comment($memory , 'Tjeneste' );
								xmlwriter_write_element($memory,'ID', 'TJE');
								xmlwriter_write_element($memory,'Verdi', '');
							xmlwriter_end_element($memory);
							xmlwriter_start_element($memory,'Begrep');
								xmlwriter_write_comment($memory , 'Objekt' );
								xmlwriter_write_element($memory,'ID', 'F0');
								xmlwriter_write_element($memory,'Verdi', $this->db->f('loc1'));
							xmlwriter_end_element($memory);
							xmlwriter_start_element($memory,'Begrep');
								xmlwriter_write_comment($memory , 'Prosjekt' );
								xmlwriter_write_element($memory,'ID', 'B0');
								xmlwriter_write_element($memory,'Verdi', '');
							xmlwriter_end_element($memory);
							xmlwriter_start_element($memory,'Begrep');
								xmlwriter_write_comment($memory , 'Fagkode' );
								xmlwriter_write_element($memory,'ID', 'B1');
								xmlwriter_write_element($memory,'Verdi', '999');
							xmlwriter_end_element($memory);
							xmlwriter_start_element($memory,'Begrep');
								xmlwriter_write_comment($memory , 'AV' );
								xmlwriter_write_element($memory,'ID', 'AV');
								xmlwriter_write_element($memory,'Verdi', '');
							xmlwriter_end_element($memory);
						xmlwriter_end_element($memory);
					xmlwriter_end_element($memory);
				xmlwriter_end_element($memory);
			}
			xmlwriter_end_element($memory);
			$xml = xmlwriter_output_memory($memory,true);

			if($this->debug)
			{
				header('Content-type: text/xml');
				echo $xml;
				die();
			}

			return $xml;
		}




	}

	$systemoversikt = new lrs_el_anlegg();
	$systemoversikt->check_history($values,$values_attribute,$entity_id,$cat_id,$receipt);

