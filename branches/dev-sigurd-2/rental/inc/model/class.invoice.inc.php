<?php
	include_class('rental', 'model', 'inc/model/');
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'invoice_price_item', 'inc/model/');

	class rental_invoice extends rental_model
	{
		protected $id;
		protected $billing_id; // The billing job that created this invoice
		protected $contract_id; // Contract that this invoice belongs to
		protected $party_id; // Party that is the recepient of this invoice
		protected $timestamp_created; // Billing date
		protected $timstamp_start; // Start date of invoice
		protected $timstamp_end; // End date of invoice
		protected $invoice_price_items;
		protected $total_sum;
		protected $contract;
		
		public static $so;
		
		public function __construct(int $id, int $billing_id, int $contract_id, int $timestamp_created, int $timestamp_start, int $timestamp_end, float $total_sum)
		{
			$this->id = (int)$id;
			$this->billing_id = (int)$billing_id;
			$this->contract_id = (int)$contract_id;
			$this->timestamp_created = (int)$timestamp_created;
			$this->timestamp_start = (int)$timestamp_start;
			$this->timestamp_end = (int)$timestamp_end;
			$this->total_sum = (float)$total_sum;
			$this->invoice_price_items = null;
			$this->contract = null;
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
		
		/**
		 * Returns the underlying contract for this object. Uses lazy loading.
		 * 
		 * @return rental_contract for the invoice, should never be null.
		 */
		public function get_contract()
		{
			if($this->contract == null)
			{
				$this->contract = rental_contract::get($this->get_contract_id());
			}
			return $this->contract;
		}

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
		
		public function set_total_sum($total_sum)
		{
			$this->total_sum = $total_sum;
		}
		
		public function get_total_sum(){ return $this->total_sum; }
			
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.soinvoice');
			}
			return self::$so;
		}
		
		/**
		 * Returns all invoices of a specified billing id.
		 * 
		 * @param $billing_id int with id of billing job.
		 * @return array of rental_invoice objects, empty array if no invoices
		 * found, never null.
		 */
		public static function get_invoices_for_billing(int $billing_id)
		{
			return rental_invoice::get_so()->get_invoices_for_billing($billing_id);
		}
		
		public static function create_invoice(int $decimals, int $billing_id, int $contract_id, int $timestamp_invoice_start, int $timestamp_invoice_end)
		{
			if($timestamp_invoice_start > $timestamp_invoice_end) // Sanity check
			{
				return null;
			}
			$contract = rental_contract::get($contract_id);
			$invoice = new rental_invoice(-1, $billing_id, $contract_id, time(), $timestamp_invoice_start, $timestamp_invoice_end, 0);
			$invoice->set_timestamp_created(time());
			$invoice->set_party_id($contract->get_payer_id());
			$contract_price_items = $contract->get_price_items();
			$invoice->store(); // We must store the invoice at this point to have an id to give to the price item
			$total_sum = 0;
			foreach($contract_price_items as $contract_price_item)
			{
				// We have to find the period the price item applies for on this invoice
				// First we get the dates from the contract price items
				$contract_price_item_start = $contract_price_item->get_date_start();
				if($contract_price_item_start == null || $contract_price_item_start == '') // Date not set
				{
					// We just use the invoice date for our calculations
					$contract_price_item_start = $timestamp_invoice_start;
				}
				else // Date set
				{
					$contract_price_item_start = strtotime($contract_price_item_start); // We have to translate to unix timestamp
				}
				$contract_price_item_end = $contract_price_item->get_date_end();
				if($contract_price_item_end == null || $contract_price_item_end == '') // Date not set
				{
					// We just use the invoice date for our calculations
					$contract_price_item_end = $timestamp_invoice_end;
				}
				else // Date set
				{
					$contract_price_item_end = strtotime($contract_price_item_end); // We have to translate to unix timestamp
				}
				
				// Secondly we find the timestamps that should be used for this invoice
				
				if($contract_price_item_end < $contract_price_item_start) // Sanity check - end date should never be before start date
				{
					continue; // We don't add this price item - continue to next
				}
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
					$invoice_price_item_start = $contract_price_item_start;
				}
				
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
					$invoice_price_item_end = $timestamp_invoice_end;
				}
				 
				$invoice_price_item = new rental_invoice_price_item($decimals, -1, $invoice->get_id(), $contract_price_item->get_title(), $contract_price_item->get_agresso_id(), $contract_price_item->is_area(), $contract_price_item->get_price(), $contract_price_item->get_area(), $contract_price_item->get_count(), $invoice_price_item_start, $invoice_price_item_end);
				$invoice_price_item->store(); // The price item must store itself
				$invoice->add_invoice_price_item($invoice_price_item);
				$total_sum += $invoice_price_item->get_total_price();
			}
			$invoice->set_total_sum(round($total_sum, $decimals));
			$invoice->store();
			return $invoice;
		}
		
		public function serialize()
		{
			return array();
		}
		
	}
		
?>