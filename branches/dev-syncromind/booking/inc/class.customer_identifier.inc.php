<?php
	phpgw::import_class('booking.uicommon');

	class booking_customer_identifier {
		const TYPE_SSN = 'ssn';
		const TYPE_ORGANIZATION_NUMBER = 'organization_number';

		protected $field_prefix='customer_';
		protected $identifier_type_field;
		
		protected static $valid_types = array(
			self::TYPE_SSN,
			self::TYPE_ORGANIZATION_NUMBER,
		);
		
		function __construct() {
			$this->identifier_type_field = $this->field_prefix.'identifier_type';
		}
		
		public function is_valid_customer_identifier_type($type) {
			return in_array($type, $this->get_valid_types());
		}
		
		public function copy_between(array $from_entity, array &$to_entity) {
			if ($from_entity_customer_identifier = $this->get_current_identifier_type($from_entity))
			{
				$to_entity[$this->identifier_type_field] = $from_entity_customer_identifier;
                if(intval($from_entity['customer_internal']) == 1)
                {
                    if ((strlen($from_entity['customer_number']) == 6) || (strlen($from_entity['customer_number']) == 5))    			
                    {
                    	$to_entity[$this->field_prefix.$from_entity_customer_identifier] = $from_entity['customer_number'];
                    }
                    elseif ((strlen($from_entity['customer_organization_number']) == 6) ||  (strlen($from_entity['customer_organization_number']) == 5)) 
					{
                    	$to_entity[$this->field_prefix.$from_entity_customer_identifier] = $from_entity['customer_organization_number'];
					} 			
                    else
                    {
                       	// FIXME Sigurd Nes 4. februar 2012: det er feil i datasettet om denne slår til
                       	$to_entity[$this->field_prefix.$from_entity_customer_identifier] = $from_entity['customer_number'];//lang('None');
                    }
                } 
                else 
                {
    				$to_entity[$this->field_prefix.$from_entity_customer_identifier] = $this->get_current_identifier_value($from_entity);
                }
                
			}
		}
		
		/** 
		 * Extract customer identifier from _POST into $data
		 */
		public function extract_form_data(&$data)
		{	
			$current_type = isset($_POST[$this->identifier_type_field]) && $this->is_valid_customer_identifier_type($_POST[$this->identifier_type_field]) ?
									$_POST[$this->identifier_type_field] : null;
									
			if (!$current_type) { 
				$data[$this->identifier_type_field] = null;
				foreach ($this->get_valid_types() as $type) {
					$data[$this->field_prefix.$type] = null;
				}
				return;
			}
			
			$identifier_field = $this->field_prefix.$current_type;
			$identifier_value = isset($_POST[$identifier_field]) ? trim($_POST[$identifier_field]) : null;
			
			if (empty($identifier_value)) {
				$data[$this->identifier_type_field] = null;
				$data[$identifier_field] = null;
			} else {
				$data[$this->identifier_type_field] = $current_type;
				$data[$identifier_field] = $identifier_value;
			}
			
			
			//Clear other customer identifier fields
			foreach ($this->get_valid_types() as $type) {
				$current_type != $type AND $data[$this->field_prefix.$type] = null;
			}
		}
		
		public function validate($data) {
			if (isset($data[$this->identifier_type_field])) {	
				if (!$this->is_valid_customer_identifier_type($data[$this->identifier_type_field])) {
					return array($this->field_prefix.$current_type => 'Invalid customer identifier type');
				}
				
				$identifier_field = trim($data[$this->identifier_type_field]);
				
				if (empty($identifier_field)) {
					return array($this->field_prefix.$current_type => sprintf('Missing value for customer\'s %s', $data[$this->identifier_type_field]));
				}
				
				$identifier_field = $this->field_prefix.$identifier_field;
				
			} else {
				foreach ($this->get_valid_types() as $type) {
					if (isset($data[$this->field_prefix.$type]) && !empty($data[$this->field_prefix.$type])) {
						return array($this->field_prefix.$current_type => 'Customer identifier type must be specified');
					}
				}
			}
			
			return array();
		}
		
		public function get_current_identifier_value($data) {
			if (!($identifier_field = $this->get_current_identifier_type($data))) {
				return null;
			}

			$identifier_field = $this->field_prefix.$identifier_field;
			$identifier_value = isset($data[$identifier_field]) ? trim($data[$identifier_field]) : null;

			if ($identifier_field == 'customer_organization_number' and (strlen($identifier_value) != 5 and strlen($identifier_value) != 6 and strlen($identifier_value) != 9)){
				return null;
			}

			if ($identifier_field == 'customer_ssn' and strlen($identifier_value) != 11){
				return null;
			}

			return (empty($identifier_value) ? null : $identifier_value);
		}
		
		public function get_current_identifier_type($data) {
			$identifier_field = trim($data[$this->identifier_type_field]);	
			return (empty($identifier_field) ? null : $identifier_field);
		}
		
		public function install(booking_uicommon $ui, &$entity = null) {
			$js = <<<JST
			(function() {
				var Dom = YAHOO.util.Dom;
				var Event = YAHOO.util.Event;

				var select_input_id = 'field_{$this->identifier_type_field}';
				var select_input = Dom.get(select_input_id);
				
				if (!select_input) { return; }
				
				var selectedIndex = document.getElementById(select_input_id).selectedIndex;

				var items = Dom.getElementsBy(function(){return true;}, 'option', select_input);
				var all_cust_fields = {};
				var cust_field;
				for (var i = items.length - 1; i >= 0; i--){
					if (items[i].value.length <= 0) { continue; }
					cust_field = Dom.get('field_{$this->field_prefix}'+items[i].value);
					all_cust_fields[items[i].value] = cust_field;

					if (i == selectedIndex) { continue; }
					Dom.setStyle(cust_field, 'display', 'none')
				};

				var enableCustField = function(field_type) {
					for (var key in all_cust_fields) {
						Dom.setStyle(all_cust_fields[key], 'display', 'none');
					}

					if (all_cust_fields[field_type] == undefined) { return; }

					Dom.setStyle(all_cust_fields[field_type], 'display', 'block');
					if (all_cust_fields[field_type].name == 'customer_ssn') {
						all_cust_fields[field_type].value = '6 siffer (DDMMÅÅ) eller 11 siffer';
					} else if (all_cust_fields[field_type].name == 'customer_organization_number') {
						all_cust_fields[field_type].value = '9 siffer';
					}
					all_cust_fields[field_type].focus();
					all_cust_fields[field_type].select();
				}
				
				Event.addListener(select_input, 'change', function(e) {
					enableCustField(this[this.selectedIndex].value);
				});

				// Wouldn't work in IE6:
				// Dom.batch(items, function(opt) {
				// 	Event.addListener(opt, 'click', function(e) {
				// 		enableCustField(this.value);
				// 	})
				// });
			})();
JST;

			if (is_array($entity)) {
				$this->add_current_identifier_info($entity);
			}
			
			$ui->add_template_file('customer_identifier');
			$ui->add_js_load_event($js);
		}
		
		public function add_current_identifier_info(&$entity) {
			$entity['customer_identifier_types'] = $this->get_valid_types_ui_values();
		
			if ($customer_identifier_type = $this->get_current_identifier_type($entity)) {
				$entity['customer_identifier_label'] = booking_uicommon::humanize($customer_identifier_type);
			}
		
			if ($customer_identifier_value = $this->get_current_identifier_value($entity)) {
				$entity['customer_identifier_value'] = $customer_identifier_value;
			}
		}
		
		public function get_valid_types() {
			return self::$valid_types;
		}
		
		public function get_valid_types_ui_values() {
			return array_combine($this->get_valid_types(), array_map(array('booking_uicommon', 'humanize'), $this->get_valid_types())); 
		}
	}
