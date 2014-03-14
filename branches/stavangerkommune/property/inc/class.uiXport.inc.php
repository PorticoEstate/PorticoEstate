<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage admin
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */
	class property_uiXport
	{

		var $public_functions = array
			(
			'import'	 => true,
			'export'	 => true,
			'rollback'	 => true
		);
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $cat_id;

		function property_uiXport()
		{

			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'property::invoice';
			$this->bo											 = CreateObject('property.boXport', true);
			$this->invoice										 = CreateObject('property.boinvoice');
			$this->bocommon										 = CreateObject('property.bocommon');
			$this->contacts										 = CreateObject('property.sogeneric');
			$this->contacts->get_location_info('vendor', false);

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.invoice';
			$this->acl_read		 = $this->acl->check('.invoice', PHPGW_ACL_READ, 'property');
			$this->acl_add		 = $this->acl->check('.invoice', PHPGW_ACL_ADD, 'property');
			$this->acl_edit		 = $this->acl->check('.invoice', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	 = $this->acl->check('.invoice', PHPGW_ACL_DELETE, 'property');
			$this->acl_manage	 = $this->acl->check('.invoice', 16, 'property');

			$this->start	 = $this->bo->start;
			$this->query	 = $this->bo->query;
			$this->sort		 = $this->bo->sort;
			$this->order	 = $this->bo->order;
			$this->filter	 = $this->bo->filter;
			$this->cat_id	 = $this->bo->cat_id;
		}

		function import()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop', 'perm' => 2, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::import';
			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'import_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data', 'import_receipt', '');

			$art				 = phpgw::get_var('art', 'int');
			$type				 = phpgw::get_var('type');
			$dim_b				 = phpgw::get_var('dim_b', 'int');
			$invoice_num		 = phpgw::get_var('invoice_num');
			$kid_nr				 = phpgw::get_var('kid_nr');
			$vendor_id			 = phpgw::get_var('vendor_id', 'int');
			$vendor_name		 = phpgw::get_var('vendor_name');
			$janitor			 = phpgw::get_var('janitor');
			$supervisor			 = phpgw::get_var('supervisor');
			$budget_responsible	 = phpgw::get_var('budget_responsible');
			$invoice_date		 = urldecode(phpgw::get_var('invoice_date'));
			$num_days			 = phpgw::get_var('num_days', 'int');
			$payment_date		 = urldecode(phpgw::get_var('payment_date'));
			$cancel				 = phpgw::get_var('cancel', 'bool');
			$convert			 = phpgw::get_var('convert', 'bool');
			$conv_type			 = phpgw::get_var('conv_type');
			$sday				 = phpgw::get_var('sday', 'int');
			$smonth				 = phpgw::get_var('smonth', 'int');
			$syear				 = phpgw::get_var('syear', 'int');
			$eday				 = phpgw::get_var('eday', 'int');
			$emonth				 = phpgw::get_var('emonth', 'int');
			$eyear				 = phpgw::get_var('eyear', 'int');
			$download			 = phpgw::get_var('download', 'bool');
			$auto_tax			 = phpgw::get_var('auto_tax', 'bool');

			$tsvfile = $_FILES['tsvfile']['tmp_name'];

			if(!$tsvfile)
			{
				$tsvfile = phpgw::get_var('tsvfile');
			}

			if($cancel && $tsvfile)
			{
				unlink($tsvfile);
			}

			if($convert)
			{
				unset($receipt);

				if($conv_type == '')
				{
					$receipt['error'][] = array('msg' => lang('Please - select a import format !'));
				}

				if(!$tsvfile)
				{
					$receipt['error'][] = array('msg' => lang('Please - select a file to import from !'));
				}

				if(!$art)
				{
					$receipt['error'][] = array('msg' => lang('Please - select type invoice!'));
				}
				if(!$vendor_id)
				{
					$receipt['error'][] = array('msg' => lang('Please - select Vendor!'));
				}

				if(!$type)
				{
					$receipt['error'][] = array('msg' => lang('Please - select type order!'));
				}

				if(!$budget_responsible)
				{
					$receipt['error'][] = array('msg' => lang('Please - select budget responsible!'));
				}

				if(!$this->invoice->check_vendor($vendor_id))
				{
					$receipt['error'][] = array('msg' => lang('That Vendor ID is not valid !') . ' : ' . $vendor_id);
				}

				if(!$payment_date && !$num_days)
				{
					$receipt['error'][] = array('msg' => lang('Please - select either payment date or number of days from invoice date !'));
				}

				if(!file_exists($tsvfile))
				{
					$receipt['error'][] = array('msg' => lang('The file is empty or removed!'));
				}
				if(!is_array($receipt['error']))
				{
					if($invoice_date)
					{
						$sdateparts	 = phpgwapi_datetime::date_array($invoice_date);
						$sday		 = $sdateparts['day'];
						$smonth		 = $sdateparts['month'];
						$syear		 = $sdateparts['year'];
						unset($sdateparts);

						$edateparts	 = phpgwapi_datetime::date_array($payment_date);
						$eday		 = $edateparts['day'];
						$emonth		 = $edateparts['month'];
						$eyear		 = $edateparts['year'];
						unset($edateparts);
					}

					$old	 = $tsvfile;
					$tsvfile = $GLOBALS['phpgw_info']['server']['temp_dir'] . '/invoice_import_' . basename($tsvfile);
					rename($old, $tsvfile);

					$invoice_common = array
						(
						'bilagsnr'			 => $this->invoice->next_bilagsnr(),
						'art'				 => $art,
						'type'				 => $type,
						'dim_b'				 => $dim_b,
						'invoice_num'		 => $invoice_num,
						'kid_nr'			 => $kid_nr,
						'vendor_id'			 => $vendor_id,
						'vendor_name'		 => $vendor_name,
						'janitor'			 => $janitor,
						'supervisor'		 => $supervisor,
						'budget_responsible' => $budget_responsible,
						'num_days'			 => $num_days,
						'sday'				 => $sday,
						'smonth'			 => $smonth,
						'syear'				 => $syear,
						'eday'				 => $eday,
						'emonth'			 => $emonth,
						'eyear'				 => $eyear,
						'tsvfile'			 => $tsvfile,
						'conv_type'			 => $conv_type,
						'invoice_date'		 => $invoice_date,
						'payment_date'		 => $payment_date,
						'auto_tax'			 => $auto_tax
					);

					$buffer = $this->bo->import($invoice_common, $download);

					if(!$download)
					{
						$receipt = $buffer;
						$GLOBALS['phpgw']->session->appsession('session_data', 'import_receipt', $receipt);
						unlink($tsvfile);
						unset($invoice_common);
						unset($art);
						unset($type);
						unset($dim_b);
						unset($invoice_num);
						unset($kid_nr);
						unset($vendor_id);
						unset($vendor_name);
						unset($janitor);
						unset($supervisor);
						unset($budget_responsible);
						unset($invoice_date);
						unset($num_days);
						unset($payment_date);
						unset($conv_type);
						unset($auto_tax);
						//						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiXport.import'));
					}
					else
					{
						$GLOBALS['phpgw_info']['flags']['noframework'] = true;
						$this->debug_import($buffer, $invoice_common);
						return;
					}
				}
			}


			set_time_limit(0);

			$link_data = array
				(
				'menuaction' => 'property.uiXport.import',
				'sub'		 => $sub
			);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$GLOBALS['phpgw']->jqcal->add_listener('invoice_date');
			$GLOBALS['phpgw']->jqcal->add_listener('payment_date');

			$data = array
				(
				'menu'								 => $this->bocommon->get_menu(),
				'msgbox_data'						 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'cancel_action'						 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.index', 'sub' => $sub)),
				'lang_cancel'						 => lang('Cancel'),
				'lang_cancel_statustext'			 => lang('cancel the import'),
				'action_url'						 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property' . '.uiXport.import')),
				'tsvfilename'						 => '',
				'lang_debug'						 => lang('Debug output in browser'),
				'lang_debug_statustext'				 => lang('Check this to have the output to screen before import (recommended)'),
				'value_debug'						 => $download,
				'lang_import'						 => lang('Import'),
				'lang_import_statustext'			 => lang('click this button to start the import'),
				'lang_invoice_date'					 => lang('invoice date'),
				'lang_payment_date'					 => lang('Payment date'),
				'lang_no_of_days'					 => lang('Days'),
				'lang_invoice_number'				 => lang('Invoice Number'),
				'lang_invoice_num_statustext'		 => lang('Enter Invoice Number'),
				'lang_select'						 => lang('Select per button !'),
				'lang_kidnr'						 => lang('KID nr'),
				'lang_kid_nr_statustext'			 => lang('Enter Kid nr'),
				'lang_vendor'						 => lang('Vendor'),
				'addressbook_link'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilookup.vendor')),
				'lang_invoice_date_statustext'		 => lang('Enter the invoice date'),
				'lang_num_days_statustext'			 => lang('Enter the payment date or the payment delay'),
				'lang_payment_date_statustext'		 => lang('Enter the payment date or the payment delay'),
				'lang_file_statustext'				 => lang('Select the file to import from'),
				'lang_vendor_statustext'			 => lang('Select the vendor by clicking the button'),
				'lang_vendor_name_statustext'		 => lang('Select the vendor by clicking the button'),
				'lang_select_vendor_statustext'		 => lang('Select the vendor by clicking this button'),
				'value_invoice_date'				 => $invoice_date,
				'value_payment_date'				 => $payment_date,
				'value_belop'						 => $belop,
				'value_vendor_id'					 => $vendor_id,
				'value_vendor_name'					 => $vendor_name,
				'value_kid_nr'						 => $kid_nr,
				'value_dim_b'						 => $dim_b,
				'value_invoice_num'					 => $invoice_num,
				'value_merknad'						 => $merknad,
				'value_num_days'					 => $num_days,
				//				'value_tsvfile'						=> $tsvfile,
				'lang_file'							 => lang('File'),
				'lang_conv'							 => lang('Conversion'),
				'conv_list'							 => $this->bo->select_import_conv($conv_type),
				'select_conv'						 => 'conv_type',
				'lang_select_conversion'			 => lang('Select the type of conversion:'),
				'lang_conv_statustext'				 => lang('You have to select the Conversion for this import'),
				'lang_auto_tax'						 => lang('Auto TAX'),
				'lang_auto_tax_statustext'			 => lang('Set tax during import'),
				'lang_art'							 => lang('Art'),
				'art_list'							 => $this->invoice->get_lisfm_ecoart($art),
				'select_art'						 => 'art',
				'lang_select_art'					 => lang('Select Invoice Type'),
				'lang_art_statustext'				 => lang('You have to select type of invoice'),
				'lang_type'							 => lang('Type invoice II'),
				'type_list'							 => $this->invoice->get_type_list($type),
				'select_type'						 => 'type',
				'lang_no_type'						 => lang('No type'),
				'lang_type_statustext'				 => lang('Select the type  invoice. To do not use type -  select NO TYPE'),
				'lang_dimb'							 => lang('Dim B'),
				'dimb_list'							 => $this->invoice->select_dimb_list($dim_b),
				'select_dimb'						 => 'dim_b',
				'lang_no_dimb'						 => lang('No Dim B'),
				'lang_dimb_statustext'				 => lang('Select the Dim B for this invoice. To do not use Dim B -  select NO DIM B'),
				'lang_janitor'						 => lang('Janitor'),
				'janitor_list'						 => $this->bocommon->get_user_list_right(32, $janitor, '.invoice'),
				'select_janitor'					 => 'janitor',
				'lang_no_janitor'					 => lang('No janitor'),
				'lang_janitor_statustext'			 => lang('Select the janitor responsible for this invoice. To do not use janitor -  select NO JANITOR'),
				'lang_supervisor'					 => lang('Supervisor'),
				'supervisor_list'					 => $this->bocommon->get_user_list_right(64, $supervisor, '.invoice'),
				'select_supervisor'					 => 'supervisor',
				'lang_no_supervisor'				 => lang('No supervisor'),
				'lang_supervisor_statustext'		 => lang('Select the supervisor responsible for this invoice. To do not use supervisor -  select NO SUPERVISOR'),
				'lang_budget_responsible'			 => lang('B - responsible'),
				'budget_responsible_list'			 => $this->bocommon->get_user_list_right(128, $budget_responsible, '.invoice'),
				'select_budget_responsible'			 => 'budget_responsible',
				'lang_select_budget_responsible'	 => lang('Select B-Responsible'),
				'lang_budget_responsible_statustext' => lang('You have to select a budget responsible for this invoice in order to make the import')
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice'));

			$appname		 = lang('Invoice');
			$function_msg	 = lang('Import from CSV');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('import' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function debug_import($buffer = '', $invoice_common = '')
		{
			$table	 = $buffer['table'];
			$header	 = $buffer['header'];
			$import	 = $buffer['import'];

			$sum = 0;

			$i = 0;
			foreach($table as $dummy => $record)
			{
				$k = 0;
				foreach($import as $text => $key)
				{
					$content[$i]['row'][$k]['value'] = $record[$key];
					$content[$i]['row'][$k]['align'] = 'center';
					if($key == 'belop')
					{
						$content[$i]['row'][$k]['align'] = 'right';
						$sum							 = $sum + $record[$key];
						$content[$i]['row'][$k]['value'] = number_format($record[$key], 2, ',', '');
					}
					else if($key == 'stedsnavn')
					{
						$content[$i]['row'][$k]['align'] = 'left';
					}

					$k++;
				}
				$i++;
			}

			foreach($import as $text => $key)
			{
				$table_header[] = array
					(
					'header' => $text,
					'width'	 => '5%',
					'align'	 => 'center'
				);
			}

			$link_data_add = array
				(
				'menuaction' => 'property.uiXport.import',
				'convert'	 => 'true'
			);

			$link_data_cancel = array
				(
				'menuaction' => 'property.uiXport.import',
				'cancel'	 => true
			);

			$link_data_add		 = $link_data_add + $invoice_common;
			$link_data_cancel	 = $link_data_cancel + $invoice_common;


			$table_add[] = array
				(
				'lang_add'				 => lang('Import'),
				'lang_add_statustext'	 => lang('Import this invoice'),
				'add_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data_add),
				'lang_cancel'			 => lang('cancel'),
				'lang_cancel_statustext' => lang('Do not import this invoice'),
				'cancel_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data_cancel)
			);

			$vendor = $this->contacts->read_single(array('id' => $invoice_common['vendor_id']), array('attributes' => array(array('column_name' => 'org_name'))));

			foreach($vendor['attributes'] as $attribute)
			{
				if($attribute['column_name'] == 'org_name')
				{
					$vendor_name = $attribute['value'];
					break;
				}
			}

			$data = array
				(
				'artid'						 => $invoice_common['art'],
				'lang_type'					 => lang('Type'),
				'lang_bilagsnr'				 => lang('bilagsnr'),
				'bilagsnr'					 => $invoice_common['bilagsnr'],
				'lang_vendor'				 => lang('Vendor'),
				'vendor_name'				 => $vendor_name,
				'spvend_code'				 => $invoice_common['vendor_id'],
				'lang_fakturadato'			 => lang('invoice date'),
				'fakturadato'				 => date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($table[0]['fakturadato'])),
				'lang_forfallsdato'			 => lang('Payment date'),
				'forfallsdato'				 => date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($table[0]['forfallsdato'])),
				'lang_janitor'				 => lang('Janitor'),
				'oppsynsmannid'				 => $invoice_common['janitor'],
				'lang_supervisor'			 => lang('Supervisor'),
				'saksbehandlerid'			 => $invoice_common['supervisor'],
				'lang_budget_responsible'	 => lang('Budget Responsible'),
				'budsjettansvarligid'		 => $invoice_common['budget_responsible'],
				'lang_sum'					 => lang('Sum'),
				'sum'						 => number_format($sum, 2, ',', ''),
				'table_header'				 => $table_header,
				'values'					 => $content,
				'table_add'					 => $table_add
			);

			unset($content);

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice', 'table_header'));
			$appname		 = lang('Invoice');
			$function_msg	 = lang('Debug');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('debug' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function export()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop', 'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::export';
			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice',
				'search_field'));

			$values	 = phpgw::get_var('values');
			$date	 = phpgw::get_var('date');
			$receipt = array();

			if($values['submit'])
			{
				if(!$values['conv_type'] && !$values['file'])
				{

					$receipt['error'][] = array('msg' => lang('No conversion type could be located.') . ' - ' . lang('Please choose a conversion type from the list'));
				}
				else if($values['conv_type'] && !$values['file'])
				{
					$receipt = $this->bo->export(array('conv_type' => $values['conv_type'], 'download' => $values['download'], 'force_period_year' => $values['force_period_year']));
					if(!$values['download'])
					{
						$GLOBALS['phpgw_info']['flags'][noheader]		 = true;
						$GLOBALS['phpgw_info']['flags'][nofooter]		 = true;
						$GLOBALS['phpgw_info']['flags']['xslt_app']		 = false;
						$GLOBALS['phpgw_info']['flags']['noframework']	 = true;
						echo '<pre>' . $receipt['message'][0]['msg'] . '</pre>';
						echo '&nbsp<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiXport.export')) . '">' . lang('Back') . '</a>';
					}
				}
			}
			else
			{
				$date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}
			//_debug_array($receipt);

			$link_data = array
				(
				'menuaction' => 'property.uiXport.export'
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'menu'					 => $this->bocommon->get_menu(),
				'msgbox_data'			 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_export_statustext' => lang('click this button to start the export'),
				'lang_select_conv'		 => lang('Select conversion'),
				'conv_list'				 => $this->bo->select_export_conv($values['conv_type']),
				'select_conv'			 => 'values[conv_type]',
				'lang_conv_statustext'	 => lang('Select conversion'),
				'lang_rollback_file'	 => lang('Roll back'),
				'link_rollback_file'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiXport.rollback')),
				'lang_export_to_file'	 => lang('Export to file'),
				'value_debug'			 => $values['debug'],
				'lang_debug_statustext'	 => lang('Uncheck to debug the result'),
				'lang_submit'			 => lang('Submit'),
				'lang_cancel'			 => lang('Cancel'),
				'form_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_save'				 => lang('save')
			);

			//_debug_array($data);
			$appname		 = lang('Invoice');
			$function_msg	 = lang('Export invoice');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('export' => $data));
		}

		function rollback()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop', 'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::invoice::rollback';

			$GLOBALS['phpgw']->xslttpl->add_file(array('invoice',
				'search_field'));

			$values	 = phpgw::get_var('values');
			$date	 = phpgw::get_var('date');
			//_debug_array($values);

			if($values['submit'])
			{
				if(!$values['conv_type'])
				{
					$receipt['error'][] = array('msg' => lang('No conversion type could be located.') . ' - ' . lang('Please choose a conversion type from the list'));
				}

				if(!$values['file'] && !$values['voucher_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please choose a file or a voucher'));
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->rollback($values['conv_type'], $values['file'], $date, $values['voucher_id']);
				}
			}
			else
			{
				$date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			$link_data = array('menuaction' => 'property.uiXport.rollback');

			//_debug_array($receipt);
			$GLOBALS['phpgw']->jqcal->add_listener('date');

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data'			 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_select_conv'		 => lang('Select conversion'),
				'conv_list'				 => $this->bo->select_export_conv($values['conv_type']),
				'select_conv'			 => 'values[conv_type]',
				'lang_conv_statustext'	 => lang('Select conversion'),
				'lang_select_file'		 => lang('Select file to roll back'),
				'lang_no_file'			 => lang('No file selected'),
				'lang_file_statustext'	 => lang('Select file to roll back'),
				'select_file'			 => 'values[file]',
				'rollback_file_list'	 => $this->bo->select_rollback_file($values['file']),
				'lang_export_to_file'	 => lang('Export to file'),
				'value_debug'			 => $values['debug'],
				'value_date'			 => $date,
				'lang_date'				 => lang('Export date'),
				'lang_date_statustext'	 => lang('Select date for the file to roll back'),
				'lang_submit'			 => lang('Submit'),
				'lang_cancel'			 => lang('Cancel'),
				'form_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_save'				 => lang('save')
			);

			//_debug_array($data);

			$appname		 = lang('Invoice');
			$function_msg	 = lang('Rollback invoice');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('rollback' => $data));
		}

	}	