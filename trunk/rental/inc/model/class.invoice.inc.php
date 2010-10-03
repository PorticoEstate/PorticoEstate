<?php
	phpgw::import_class('rental.socontract_price_item');
	phpgw::import_class('rental.soinvoice_price_item');
	include_class('rental', 'model', 'inc/model/');
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'invoice_price_item', 'inc/model/');

	class rental_invoice extends rental_model
	{
		protected $id;
		protected $billing_id; // The billing job that created this invoice
		protected $contract_id; // Contract that this invoice belongs to
		protected $party_id; // Party that is the recepient of this invoice
		protected $party;
		protected $timestamp_created; // Billing date
		protected $timestamp_start; // Start date of invoice
		protected $timestamp_end; // End date of invoice
		protected $invoice_price_items;
		protected $total_sum;
		protected $total_area;
		protected $header;
		protected $account_in; // 'Art' for the income side
		protected $account_out; // 'Art' for the outlay side
		protected $composite_names; // From composite - not part of invoice db data
		protected $project_id;
		protected $service_id;
		protected $responsibility_id;
		protected $old_contract_id;
		protected $term_id;
		protected $term_label;
		protected $billing_title;
		protected $serial_number;
		protected $reference;
		
		public static $so;
		
		public function __construct(int $id, int $billing_id, int $contract_id, int $timestamp_created, int $timestamp_start, int $timestamp_end, float $total_sum, float $total_area, string $header, string $account_in, string $account_out, string $service_id, string $responsibility_id)
		{
			$this->id = (int)$id;
			$this->billing_id = (int)$billing_id;
			$this->contract_id = (int)$contract_id;
			$this->timestamp_created = (int)$timestamp_created;
			$this->timestamp_start = (int)$timestamp_start;
			$this->timestamp_end = (int)$timestamp_end;
			$this->total_sum = (float)$total_sum;
			$this->total_area = (float)$total_area;
			$this->invoice_price_items = null;
			$this->header = $header;
			$this->account_in = $account_in;
			$this->account_out = $account_out;
			$this->service_id = $service_id;
			$this->responsibility_id = $responsibility_id;
			$this->composite_names = array();
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
	
		public function get_id(){ return $this->id; }
		
		public function set_billing_id($billing_id)
		{
			$this->billing_id = $billing_id;
		}
	
		public function get_billing_id(){ return $this->billing_id; }
			
		
		public function set_contract_id($contract_id)
		{
			$this->contract_id = $contract_id;
		}
	
		public function get_contract_id(){ return $this->contract_id; }

		public function set_timestamp_created($timestamp_created)
		{
			$this->timestamp_created = $timestamp_created;
		}
	
		public function get_timestamp_created(){ return $this->timestamp_created; }

		public function set_party_id($party_id)
		{
			$this->party_id = $party_id;
		}
	
		public function get_party_id(){ return $this->party_id; }

		public function set_party(rental_party $party)
		{
			$this->party = $party;
		}
	
		public function get_party(){ return $this->party; }
		
		public function set_timestamp_start($timestamp_start)
		{
			$this->timestamp_start = $timestamp_start;
		}
	
		public function get_timestamp_start(){ return $this->timestamp_start; }

		public function set_timestamp_end($timestamp_end)
		{
			$this->timestamp_end = $timestamp_end;
		}
	
		public function get_timestamp_end(){ return $this->timestamp_end; }
		
		/**
		 * Adds a invoice price item to the invoice.
		 * NOTE: The price item must store itself. The invoice object does
		 * nothing with its items while storing itself.
		 * 
		 * @param $invoice_price_item rental_invoice_price_item to add.
		 */
		public function add_invoice_price_item(rental_invoice_price_item &$invoice_price_item)
		{
			if($invoice_price_items == null)
			{
				$invoice_price_items = array();
			}
			$invoice_price_items[] = $invoice_price_item;
		}
		
		public function set_total_sum(float $total_sum)
		{
			$this->total_sum = (float)$total_sum;
		}
		
		public function get_total_sum(){ return $this->total_sum; }
		
		public function set_total_area(float $total_area)
		{
			$this->$total_area = (float)$total_area;
		}
		
		public function get_total_area(){ return $this->total_area; }
		
		public function add_composite_name(string $name)
		{
			if(!in_array($name, $this->composite_names))
			{
				$this->composite_names[] = $name;
			}
		}
		
		public function set_header($header)
		{
			$this->header = $header;
		}
	
		public function get_header(){ return $this->header; }
		
		public function set_account_in($account_in)
		{
			$this->account_in = $account_in;
		}
	
		public function get_account_in(){ return $this->account_in; }
			
		public function set_account_out($account_out)
		{
			$this->account_out = $account_out;
		}
			
		public function get_account_out(){ return $this->account_out; }
		
		public function set_service_id($service_id)
		{
			$this->service_id = $service_id;
		}
	
		public function get_service_id(){ return $this->service_id; }
		
		public function set_responsibility_id($responsibility_id)
		{
			$this->responsibility_id = $responsibility_id;
		}
		
		public function get_responsibility_id(){ return $this->responsibility_id; }
		
		public function set_project_id($project_id)
		{
			$this->project_id = $project_id;
		}
	
		public function get_project_id(){ return $this->project_id; }
		
		public function set_old_contract_id($old_contract_id)
		{
			$this->old_contract_id = $old_contract_id;
		}
	
		public function get_old_contract_id(){ return $this->old_contract_id; }

		public function get_composite_names()
		{
			$names = '';
			foreach($this->composite_names as $name) {
				$names .= "{$name}<br/>";
			}
			return $names;
		}
		
		public function set_term_id($term_id)
		{
			$this->term_id = $term_id;
		}
	
		public function get_term_id(){ return $this->term_id; }
		
		public function set_serial_number($serial_number)
		{
			$this->serial_number = $serial_number;
		}
	
		public function get_serial_number(){ return $this->serial_number; }
		
		
		public function set_term_label($term_label)
		{
			$this->term_label = $term_label;
		}
	
		public function get_term_label(){ return $this->term_label; }
		
		public function set_month($month)
		{
			$this->month = $month;
		}
	
		public function get_month(){ return $this->month; }
		
		public function set_billing_title($billing_title)
		{
			$this->billing_title = $billing_title;
		}

		public function get_reference(){ return $this->reference; }

		public function set_reference($reference)
		{
			$this->reference = $reference;
		}
	
		public function get_billing_title(){ return $this->billing_title; }
		
		/**
		 * Create invoice
		 * 
		 * @param int $decimals	the number of decimals on the total sum of the onvoice
		 * @param int $billing_id	the billing this invoice is part of
		 * @param int $contract_id	the contract
		 * @param bool $override	flag to indicate if the invoice start period should be overridden with the billing start date of contract
		 * @param int $timestamp_invoice_start	the startdate of the invoice period
		 * @param int $timestamp_invoice_end	the enddate of the invoice period
		 * @param bool $bill_only_one_time	flag to indicate if the the invoice should only bil one time price elements
		 * @return rental_invoice	the newly created invoice
		 */
		public static function create_invoice(int $decimals, int $billing_id, int $contract_id, bool $override,int $timestamp_invoice_start, int $timestamp_invoice_end, $bill_only_one_time)
		{
			$contract = rental_socontract::get_instance()->get_single($contract_id);
			
			// If the invoice period should be overriden with the biling start date
			if($override)
			{
				$timestamp_invoice_start = $contract->get_billing_start_date();
			}
			
			// If no account out is specified: check if the contract type defines any data to be used in this field (AGRESSO specific logic)  
			$account_out = $contract->get_account_out();
			if(!isset($account_out) || $account_out == '')
			{
				//If no account out - check the contract type for default
				$account_tmp = rental_socontract::get_instance()->get_contract_type_account($contract->get_contract_type_id());
				if(isset($account_tmp) && $account_tmp != '')
				{
					$account_out = $account_tmp;
				}
				else
				{
					$account_out = rental_socontract::get_instance()->get_default_account($contract->get_location_id(), false);
				}
			}
			
			// Create invoice ...
			$invoice = new rental_invoice(
				-1, 								// no identifier
				$billing_id,						// the billing identifier
				$contract_id, 						// the contract identifier
				time(), 							// the creation time
				$timestamp_invoice_start, 			// the invoice start date
				$timestamp_invoice_end, 			// the invoice end date
				0,									// the total sum of invoice (not calculated yet)
				$contract->get_rented_area(), 		// the area rented on the contract
				$contract->get_invoice_header(),	// the invoice header
				$contract->get_account_in(), 		// the ingoing account number
				$account_out, 						// the outgoing account number
				$contract->get_service_id(),		// the service identifier (internal)
				$contract->get_responsibility_id()	// the responsibility identifier (internal)
			 );
			
			 // ... and add party identifier, project number and the old contract identifier
			$invoice->set_party_id($contract->get_payer_id());
			$invoice->set_project_id($contract->get_project_id());
			$invoice->set_old_contract_id($contract->get_old_contract_id());
			
			rental_soinvoice::get_instance()->store($invoice); // We must store the invoice at this point to have an id to give to the price item
			
			// Retrieve the contract price items: only one-time or all
			if($bill_only_one_time)
			{
				$contract_price_items = rental_socontract_price_item::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $contract->get_id(), 'one_time' => true));
			}
			else
			{
				$contract_price_items = rental_socontract_price_item::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $contract->get_id()));
			}
			
			$total_sum = 0; // Holding the total price of the invoice
			
			$contract_dates = $contract->get_contract_date();
			if(isset($contract_dates))
			{
				$contract_start = $contract->get_contract_date()->get_start_date();
				$contract_end = $contract->get_contract_date()->get_end_date();
			}
			
			// Run through the contract price items
			foreach($contract_price_items as $contract_price_item)
			{
				// ---- Period calculation ---
				// Determine start date for price item
				$contract_price_item_start = $contract_price_item->get_date_start();
				if($contract_price_item_start == null || $contract_price_item_start == '') // Date not set
				{
					// We just use the invoice date for our calculations
					$contract_price_item_start = $timestamp_invoice_start;
				}
				
				// Determine end date for price item
				$contract_price_item_end = $contract_price_item->get_date_end();
				if($contract_price_item_end == null || $contract_price_item_end == '') // Date not set
				{
					// We just use the invoice date for our calculations
					$contract_price_item_end = $timestamp_invoice_end;
				}
				
				// Sanity check - end date should never be before start date
				if($contract_price_item_end < $contract_price_item_start) 
				{
					continue; // We don't add this price item - continue to next
				}
				
				// Checking the start date against the invoice dates
				if($contract_price_item_start < $timestamp_invoice_start) // Start of price item before invoice start
				{
					$invoice_price_item_start = $timestamp_invoice_start; // We use the invoice start
				}
				else if($contract_price_item_start > $timestamp_invoice_end) // Start of price item after this invoice ends
				{
					continue; // We don't add this price item - continue to next
				}
				else // Price item start date is somewhere between start and end
				{
					$invoice_price_item_start = $contract_price_item_start; // We use the price item start
				}
				
				// Checking the end date against invoice dates
				if($contract_price_item_end < $timestamp_invoice_start) // End of price item before this invoice starts
				{
					continue; // We don't add this price item - continue to next
				}
				else if($contract_price_item_end < $timestamp_invoice_end) // End of price item before invoice end
				{
					$invoice_price_item_end = $contract_price_item_end; // We use the price item end
				} 
				else // Price item end date is somewhere after invoice end
				{
					$invoice_price_item_end = $timestamp_invoice_end;	// We use the invoice end
				}
				
				// Checking the contract dates against the temporary price item dates
				if(isset($contract_start) && !$contract_price_item->is_one_time())
				{
					if($contract_start > $timestamp_invoice_end) // The start of the contract is after the billing period (should never happen)
					{
						continue; //No price items for this contract will be billed
					}
					
					if($contract_start > $invoice_price_item_start) // The contract start is after the start of the price item
					{
						$invoice_price_item_start = $contract_start;
					}
				}
				
				if(isset($contract_end) && !$contract_price_item->is_one_time())
				{
					if($contract_end < $timestamp_invoice_start) // The end of the contract is before the billing period (should never happen)
					{
						continue; //No price items for this contract will be billed
					}
					
					if($contract_end < $invoice_price_item_end) // The contract start is after the start of the price item
					{
						$invoice_price_item_end = $contract_end;
					}
				}
				
				// --- End of period calculation ---
				
				// Create a new invoice price item
				$invoice_price_item = new rental_invoice_price_item(
					$decimals,									// the number of decimals to use for the total price of the price item
					-1, 										// no price item identifier
					$invoice->get_id(), 						// the invoice identifier
					$contract_price_item->get_title(),			// the contract price item title
					$contract_price_item->get_agresso_id(), 	// the contract price item agresso identifier
					$contract_price_item->is_area(), 			// flag for specifying if the contract is of area/piece
					$contract_price_item->get_price(),			// the price of the contract price item
					$contract_price_item->get_area(), 			// the rented area on this contract (derived from contract)
					$contract_price_item->get_count(), 			// the number of items on this price item
					$invoice_price_item_start, 					// the start date from which this price item should be calculated
					$invoice_price_item_end						// the end date to which this price item should be calculated
				);
				
				// If the contract price item is of type one-time and it's dates are within the invoice period ...
				if($contract_price_item->is_one_time()){
					if($contract_price_item_start >= $timestamp_invoice_start && $contract_price_item_start <= $timestamp_invoice_end){
						// ... set the total price of the invoice price item to the total price of the contract price item
						$invoice_price_item->set_total_price($contract_price_item->get_total_price());
						// ... and set the contract price item as billed
						$contract_price_item->set_is_billed(true);
						rental_socontract_price_item::get_instance()->store($contract_price_item);
					}
				}
				
				// Store the invoice price item
				rental_soinvoice_price_item::get_instance()->store($invoice_price_item);
				
				// Add the price item to the invoice
				$invoice->add_invoice_price_item($invoice_price_item);
				
				// Add this price item's total sum to the tota sum of the invoice
				$total_sum += $invoice_price_item->get_total_price();
			} // end of looping through the contract price items
			
			
			// Set the total sum of the invoice rounded to the specified number of decimals
			$invoice->set_total_sum(round($total_sum, $decimals));
			
			// ... and store the invoice
			rental_soinvoice::get_instance()->store($invoice);
			return $invoice;
		}
		
		public function serialize()
		{
			$party_name = '';
			if($this->get_party() != null)
			{
				$serialized_party = $this->get_party()->serialize();
				$party_name = $serialized_party['name'];
			}
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			return array(
				'id'				=> $this->get_id(),
				'contract_id'		=> $this->get_contract_id(),
				'term_label'		=> $this->get_term_label(),
				'timestamp_created'	=> date($date_format, $this->get_timestamp_created()),
				'composite_name'	=> $this->get_composite_names(),
				'party_name'		=> $party_name,
				'total_sum'			=> $this->get_total_sum(),
				'old_contract_id'	=> $this->get_old_contract_id(),
				'serial_number'		=> $this->get_serial_number()
			);
		}
		
	}
		
?>