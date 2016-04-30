<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.sobilling');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.sodocument');
	phpgw::import_class('rental.soinvoice');
	phpgw::import_class('rental.sonotification');
	phpgw::import_class('rental.soprice_item');
	phpgw::import_class('rental.socontract_price_item');
	phpgw::import_class('rental.soadjustment');
	phpgw::import_class('rental.soparty');
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'document', 'inc/model/');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract_price_item', 'inc/model/');
	include_class('rental', 'notification', 'inc/model/');
	include 'SnappyMedia.php';
	include 'SnappyPdf.php';

	class rental_uimakepdf extends rental_uicommon
	{

		private $pdf_templates = array();
		public $public_functions = array
			(
			'index' => true,
			'view' => true,
			'makePDF' => true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::contracts');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('contracts');
		}

		public function query()
		{

		}

		/**
		 * View a list of all contracts
		 */
		public function index()
		{
			self::redirect(array('menuaction' => 'rental.uicontract.index'));
		}

		/**
		 * Common function for viewing or editing a contract
		 *
		 * @param $editable whether or not the contract should be editable in the view
		 * @param $contract_id the id of the contract to show
		 */
		public function viewedit( $editable, $contract_id, $contract = null, $location_id = null, $notification = null, string $message = null, string $error = null )
		{

			$cancel_link = self::link(array('menuaction' => 'rental.uicontract.index', 'populate_form' => 'yes'));
			$adjustment_id = (int)phpgw::get_var('adjustment_id');
			if ($adjustment_id)
			{
				$cancel_link = self::link(array('menuaction' => 'rental.uiadjustment.show_affected_contracts',
						'id' => $adjustment_id));
				$cancel_text = 'contract_regulation_back';
			}


			if (isset($contract_id) && $contract_id > 0)
			{
				if ($contract == null)
				{
					$contract = rental_socontract::get_instance()->get_single($contract_id);
				}
				if ($contract)
				{

					if ($editable && !$contract->has_permission(PHPGW_ACL_EDIT))
					{
						$editable = false;
						$error .= '<br/>' . lang('permission_denied_edit_contract');
					}

					if (!$editable && !$contract->has_permission(PHPGW_ACL_READ))
					{
						phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_view_contract'));
					}

					$parties = rental_soparty::get_instance()->get(0, 0, '', false, '', '', array(
						'contract_id' => $contract->get_id()));
					$party = reset($parties); //

					$contract_dates = $contract->get_contract_date();

					$composites = rental_socomposite::get_instance()->get(0, 0, '', false, '', '', array(
						'contract_id' => $contract->get_id()));
					$composite = reset($composites);

					$units = $composite->get_units();


					$price_items = rental_socontract_price_item::get_instance()->get(0, 0, '', false, '', '', array(
						'contract_id' => $contract->get_id()));
					$months = rental_socontract::get_instance()->get_months_in_term($contract->get_term_id());



					$one_time_price_items = array();
					$termin_price_items = array();

					foreach ($price_items as $item)
					{
						if ($item->is_one_time())
						{
							array_push($one_time_price_items, $item);
						}
						else
						{
							array_push($termin_price_items, $item);
						}
					}

					$data = array
						(
						'contract' => $contract,
						'months' => $months,
						'contract_party' => $party,
						'contract_dates' => $contract_dates,
						'composite' => $composite,
						'units' => $units,
						'price_items' => $price_items,
						'one_time_price_items' => $one_time_price_items,
						'termin_price_items' => $termin_price_items,
						'notification' => $notification,
						'editable' => $editable,
						'message' => isset($message) ? $message : phpgw::get_var('message'),
						'error' => isset($error) ? $error : phpgw::get_var('error'),
						'cancel_link' => $cancel_link,
						'cancel_text' => $cancel_text
					);
					$contract->check_consistency();

					$this->get_pdf_templates();
					$template_file = 'pdf/' . $this->pdf_templates[$_GET[pdf_template]][1];
					$this->render($template_file, $data);
				}
			}
			else
			{
				phpgwapi_cache::message_set('Missing contract_id as input', 'error');
				self::redirect(array('menuaction' => 'rental.uicontract.index'));
			}
		}

		/**
		 * View a contract
		 */
		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');
			$contract_id = (int)phpgw::get_var('id');
			return $this->viewedit(false, $contract_id);
		}

		/**
		 * Make PDF of a contract
		 */
		public function makePDF()
		{
			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();
			$tmp_dir = $GLOBALS['phpgw_info']['server']['temp_dir'];
			$myFile = $tmp_dir . "/temp_contract_" . strtotime(date('Y-m-d')) . ".html";
			$fh = fopen($myFile, 'w') or die("can't open file");
			$stringData = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
			fwrite($fh, $stringData);
			$stringData = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title></title></head><body>';
			fwrite($fh, $stringData);
			$stringData = $_SESSION['contract_html'];
			fwrite($fh, $stringData);
			$stringData = '</div></body></html>';
			fwrite($fh, $stringData);
			fclose($fh);
			//echo $_SESSION['contract_html'];
			$_SESSION['contract_html'] = "";

			$pdf_file_name = $tmp_dir . "/temp_contract_" . strtotime(date('Y-m-d')) . ".pdf";

			//var_dump($config->config_data['path_to_wkhtmltopdf']);
			//var_dump($GLOBALS['phpgw_info']);
			$wkhtmltopdf_executable = $config->config_data['path_to_wkhtmltopdf'];
			if (!is_file($wkhtmltopdf_executable))
			{
				throw new Exception('wkhtmltopdf not configured correctly');
			}
			$snappy = new SnappyPdf();
			//$snappy->setExecutable('/opt/portico/pe/rental/wkhtmltopdf-i386'); // or whatever else
			$snappy->setExecutable($wkhtmltopdf_executable); // or whatever else
			$snappy->save($myFile, $pdf_file_name);

			$contract_id = phpgw::get_var('id');

			if (!is_file($pdf_file_name))
			{
				throw new Exception('pdf-file not produced');
			}

			$this->savePDFToContract($pdf_file_name, $contract_id, 'Kontrakt');
		}
		/*
		 * Store a contract as PDF to VFS
		 * Add generated PDF to list of contract documents
		 */

		public function savePDFToContract( $file, $contract_id, $title )
		{
			//Create a document object
			$document = new rental_document();
			$document->set_title($title);
			$document->set_name("Kontrakt_" . strtotime(date('Y-m-d')) . ".pdf");
			$document->set_type_id(1);
			$document->set_contract_id($contract_id);
			$document->set_party_id(NULL);


			//Retrieve the document properties
			$document_properties = $this->get_type_and_id($document);

			// Move file from temporary storage to vfs
			$result = rental_sodocument::get_instance()->write_document_to_vfs
				(
				$document_properties['document_type'], $file, $document_properties['id'], "Kontrakt_" . strtotime(date('Y-m-d')) . ".pdf"
			);

			if ($result)
			{
				if (rental_sodocument::get_instance()->store($document))
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit',
						'id' => $contract_id, 'tab' => 'documents'));
				}
				else
				{
					// Handle failure on storing document
					$this->redirect($document, $document_properties, '', '');
				}
			}
			else
			{
				//Handle vfs failure to store document
				$this->redirect($document, $document_properties, '', '');
			}
			return false;
		}

		/**
		 * Utility method for finding out whether a document is bound to a
		 * contract or a party.
		 * 
		 * @param $document	the given document
		 * @return name/value array ('document_type','id')
		 */
		private function get_type_and_id( $document )
		{
			$document_type;
			$id;
			$contract_id = $document->get_contract_id();
			$party_id = $document->get_party_id();
			if (isset($contract_id) && $contract_id > 0)
			{
				$document_type = rental_sodocument::$CONTRACT_DOCUMENTS;
				$id = $contract_id;
			}
			else if (isset($party_id) && $party_id > 0)
			{
				$document_type = rental_sodocument::$PARTY_DOCUMENTS;
				$id = $party_id;
			}
			return array
				(
				'document_type' => $document_type,
				'id' => $id
			);
		}

		/**
		 * 
		 * Public function scans the contract template directory for pdf contract templates 
		 */
		public function get_pdf_templates()
		{
			$get_template_config = true;
			$files = scandir('rental/templates/base/pdf/');
			foreach ($files as $file)
			{
				$ending = substr($file, -3, 3);
				if ($ending == 'php')
				{
					include 'rental/templates/base/pdf/' . $file;
					$template_files = array($template_name, $file);
					$this->pdf_templates[] = $template_files;
				}
			}
		}
	}