<?php
	phpgw::import_class('booking.socommon');

	class booking_socompleted_reservation_export_file extends booking_socommon
	{	
		protected 
			$file_storage,
			$so_completed_reservation;
		
		protected static $export_type_to_file_type_map = array(
			'internal' => 'csv',
			'external' => 'txt',
		);
		
		function __construct()
		{		
			$this->file_storage = CreateObject('booking.filestorage', $this);
			$this->so_completed_reservation = CreateObject('booking.socompleted_reservation_export');
			
			parent::__construct('bb_completed_reservation_export_file', 
				array(
					'id'				=> array('type' => 'int'),
					'type' 			=> array('type' => 'string', 'required' => true, 'query' => true),
					'filename' 		=> array('type' => 'string'),
					'total_cost'	=> array('type' => 'decimal', 'required' => true),
					'total_items'	=> array('type' => 'int', 'required' => true),
					key(booking_socommon::$AUTO_CREATED_ON) => current(booking_socommon::$AUTO_CREATED_ON),
					key(booking_socommon::$AUTO_CREATED_BY) => current(booking_socommon::$AUTO_CREATED_BY),
					'created_by_name' => booking_socommon::$REL_CREATED_BY_NAME,
				)
			);
		}
		
		protected function file_type_for_export_type($export_type) {
			return isset(self::$export_type_to_file_type_map[$export_type]) ? 
				self::$export_type_to_file_type_map[$export_type] :
				'txt';
		}
		
		protected function get_available_export_types() {
			return $this->so_completed_reservation->get_available_export_types();
		}
		
		protected function get_export_file_data($from_entity, $of_type) {
			return $this->so_completed_reservation->get_export_file_data($from_entity, $of_type);
		}
		
		public function get_file($entity_file) {
			if (isset($entity_file['filename']) && !empty($entity_file['filename'])) {
				return $this->file_storage->get($entity_file['filename']);
			}
			
			return null;
		}
		
		public function generate_for(array $completed_reservation_exports) {
			$export_types = $this->get_available_export_types();
			
			$export_data = array_fill_keys($export_types, array());
			$export_configurations = array_fill_keys($export_types, array());
			$total_items = array_fill_keys($export_types, 0);
			$total_cost = array_fill_keys($export_types, 0.0);
			
			$this->db->transaction_begin();
			
			foreach($export_types as $export_type) {
				foreach ($completed_reservation_exports as $export) {
					list($conf, $export_result) = $this->get_export_file_data($export, $export_type);
					$export_data[$export_type][] = $export_result['data'];
					$export_configurations[$export_type][$export['id']] = $conf;
					$total_items[$export_type] += $export_result['total_items'];
					$total_cost[$export_type] += $export_result['total_cost'];
				}
			}
			
			$entity_export_files = array();
			$export_files = array();
			$export_conf_updates = array();
			
			foreach($export_types as $export_type) {
				$entity_export_file = array();
				$entity_export_file['type'] = $export_type;
				$entity_export_file['total_cost'] = $total_cost[$export_type];
				$entity_export_file['total_items'] = $total_items[$export_type];
				
				$receipt = $this->add($entity_export_file);
				$entity_export_file['id'] = $receipt['id'];
				
				$entity_export_file['filename'] = 'export_'.$export_type.'_'.$entity_export_file['id'].'.'.$this->file_type_for_export_type($export_type);
				
				$export_file = new booking_storage_object($entity_export_file['filename']);
				$export_files[] = $export_file;
				$export_file->set_data(join("\n",$export_data[$export_type]));
				$this->file_storage->attach($export_file)->persist();

				$this->update($entity_export_file); //Save the generated file name
				$entity_export_files[$entity_export_file['id']] = $entity_export_file;
				
				foreach($export_configurations[$export_type] as $export_id => $conf) {
					$export_conf_updates[] = sprintf(
						"UPDATE bb_completed_reservation_export_configuration SET export_file_id=%s WHERE id=%s",
						$entity_export_file['id'],
						$conf['id']
					);
				}
			}
			
			$this->db_query(
				join(";\n", $export_conf_updates),
				__LINE__, __FILE__
			);
			
			if ($this->db->transaction_commit()) { 
				return $entity_export_files;
			}
			
			try {
				foreach($export_files as $export_file) {
					if ($export_file->exists()) {
						$export_file->delete();
					}
				}
			} catch (booking_unattached_storage_object $e) { }
			
			throw new UnexpectedValueException('Transaction failed.');
		}
	}