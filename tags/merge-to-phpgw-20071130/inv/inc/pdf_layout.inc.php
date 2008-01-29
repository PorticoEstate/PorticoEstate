<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* (http://www.phpgroupware.org)                                            *
	* Written by Lars Kneschke  [lars@kneschke.de]                             *
	*            Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: pdf_layout.inc.php 4985 2001-05-22 01:55:01Z bettina $ */

	class deliveryPDF
	{
		var $id;
		var $orderText;
		var $date;

		function createAddress($_pdf,$_logo)
		{
			global $phpgw_info, $phpgw, $delivery_id, $order_id, $d;
			pdf_set_font($_pdf, "Helvetica" , 11,winansi);

			if (!$delivery_id)
			{
				$phpgw->db->query("SELECT phpgw_inv_orders.customer,phpgw_inv_orders.descr,phpgw_inv_delivery.num,phpgw_inv_delivery.order_id, "
								. "phpgw_inv_delivery.date FROM phpgw_inv_delivery,phpgw_inv_orders WHERE "
								. "phpgw_inv_delivery.order_id='$order_id' AND phpgw_inv_orders.num='$order_id'");
			}
			else
			{
				$phpgw->db->query("SELECT phpgw_inv_orders.customer,phpgw_inv_orders.descr,phpgw_inv_delivery.id,phpgw_inv_delivery.num, "
								. "phpgw_inv_delivery.order_id,phpgw_inv_delivery.date "                                                                          
								. "FROM phpgw_inv_delivery,phpgw_inv_orders WHERE "                                                                                                                          
								. "phpgw_inv_delivery.id='$delivery_id' AND phpgw_inv_delivery.order_id=phpgw_inv_orders.num");                                                                                  
			}

			$phpgw->db->next_record();

			$customer = $phpgw->db->f('customer');

			$cols = array('n_given' => 'n_given',
						'n_family' => 'n_family',
						'org_name' => 'org_name',
						'adr_street' => 'adr_street',
						'adr_locality' => 'adr_locality',
					'adr_postalcode' => 'adr_postalcode',
						'adr_region' => 'adr_region',
					'adr_countryname' => 'adr_countryname',
							'title' => 'title');

			$address = $d->read_single_entry($customer,$cols);

			pdf_show_xy($_pdf,$address[0]['org_name'],60,660);
			pdf_continue_text($_pdf,$address[0]['title'] . ' ' . $address[0]['n_given'] . ' ' . $address[0]['n_family']);
			pdf_continue_text($_pdf,$address[0]['adr_street']);
			pdf_continue_text($_pdf,'');
			pdf_continue_text($_pdf,$address[0]['adr_postalcode'] . ' ' . $address[0]['adr_locality']);
			pdf_continue_text($_pdf,$address[0]['adr_region']);
			pdf_continue_text($_pdf,$address[0]['adr_countryname']);		

			$this->id = $phpgw->db->f('num');
			$this->orderText = $phpgw->strip_html($phpgw->db->f('descr'));
			$this->date = date('j.n.Y',$phpgw->db->f('date'));

			$im = PDF_open_jpeg($_pdf, $_logo);
			pdf_place_image($_pdf, $im, 350, 640, 1);
			pdf_close_image($_pdf,$im);

			pdf_show_xy($_pdf,'--',1,550);
		}

		function createBody($_pdf)
		{ 
			global $delivery_id, $order_id, $phpgw, $phpgw_info;

			if ($phpgw_info['server']['db_type']=='pgsql') { $join = " JOIN "; }
			else { $join = " LEFT JOIN "; }

			pdf_set_font($_pdf,'Helvetica', 11,winansi);

			$texty = pdf_get_value($_pdf,'texty',0)-25; 
			pdf_show_xy($_pdf,lang('Delivery ID') . ': ' . $this->id,60,$texty);
			pdf_continue_text($_pdf,lang('Delivery date') . ': ' . $this->date);

			if ($this->orderText)
			{
				pdf_continue_text($_pdf,lang('Order') . ': ' . $this->orderText);
			}

			$texty -= 42;
			pdf_set_font($_pdf,'Helvetica',9,winansi);
			$pageNumber=1;

			pdf_show_xy($_pdf,lang('Pos.'),60,$texty);
			pdf_show_xy($_pdf,lang('Piece'),90,$texty);
			pdf_show_xy($_pdf,lang('Product ID'),130,$texty);
			pdf_show_xy($_pdf,lang('Name'),190,$texty);
			#pdf_show_xy($_pdf,lang("Description"),350,$texty);

			if (!$delivery_id)
			{
				$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.piece "                                                                                                        
								. "FROM phpgw_inv_products $join phpgw_inv_orderpos ON phpgw_inv_products.id=phpgw_inv_orderpos.product_id "                                                                                          
								. "WHERE phpgw_inv_orderpos.order_id='$order_id'");
			}
			else 
			{
				$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.order_id,phpgw_inv_orderpos.piece FROM phpgw_inv_products "
								. "$join phpgw_inv_orderpos ON "
								. "phpgw_inv_products.id=phpgw_inv_orderpos.product_id $join phpgw_inv_deliverypos ON "
								. "phpgw_inv_deliverypos.product_id=phpgw_inv_products.id $join phpgw_inv_delivery ON "
								. "phpgw_inv_delivery.order_id=phpgw_inv_orderpos.order_id "
								. "WHERE phpgw_inv_delivery.id='$delivery_id' AND phpgw_inv_deliverypos.delivery_id='$delivery_id'");
			}

			$texty = pdf_get_value($_pdf,'texty',0);

			while ($phpgw->db->next_record()) 
			{
				$pos++;
				$texty = $texty-20;

				pdf_show_xy($_pdf,$pos,60,$texty);
				pdf_show_xy($_pdf,$phpgw->db->f('piece'),90,$texty);
				pdf_show_xy($_pdf,$phpgw->db->f('id'),130,$texty);
				$name = $phpgw->strip_html($phpgw->db->f('name'));                                                                                                                            
				pdf_show_xy($_pdf,$name,190,$texty);
				$pro_descr = $phpgw->strip_html($phpgw->db->f('des'));                                                                                                                            
				pdf_show_boxed($_pdf,$pro_descr,200,$texty-50,340,48,'left');
				$texty = pdf_get_value($_pdf,'texty',0);

				if ($texty < 100)
				{
					//neue Seite beginnen
					$pageNumber++;
					createFooter($_pdf);
					PDF_end_page($_pdf);
					PDF_begin_page($_pdf,595,842);
					pdf_set_font($_pdf,'Helvetica',9,winansi);
					$texty = 770;
					pdf_show_xy($_pdf,lang('Pos.'),60,$texty);
					pdf_show_xy($_pdf,lang('Piece'),90,$texty);
					pdf_show_xy($_pdf,lang('Product ID'),130,$texty);
					pdf_show_xy($_pdf,lang('Name'),190,$texty);
					pdf_show_xy($_pdf,lang('Description'),350,$texty);
					PDF_add_outline($_pdf, lang('Page') . ' ' . $pageNumber);
				}
			}
		}

		function createFooter($_pdf)
		{
			global $phpgw_info, $phpgw, $d;

			if (isset($phpgw_info['user']['preferences']['inv']['abid']))
			{
				$myadress = $phpgw_info['user']['preferences']['inv']['abid'];

				$cols = array('n_given' => 'n_given',
							'n_family' => 'n_family',
							'org_name' => 'org_name',
							'adr_street' => 'adr_street',
						'adr_locality' => 'adr_locality',
						'adr_postalcode' => 'adr_postalcode',
							'adr_region' => 'adr_region',
						'adr_countryname' => 'adr_countryname',
							'a_tel_work' => 'a_tel_work',
							'a_tel_fax' => 'a_tel_fax',
							'd_email' => 'd_email',
								'url' => 'url',
							'ophone' => 'ophone');

				$footer = $d->read_single_entry($myaddress,$cols);
		#pdf_show_xy($_pdf,$line,50,20); ? kann das raus?
                
				pdf_show_xy($_pdf,$footer[0]['org_name'],50,50);
				pdf_show_xy($_pdf,lang('phone') . ': ' . $footer[0]['a_tel_work'],200,50);
				pdf_show_xy($_pdf,$footer[0]['ophone'],400,40);
				pdf_show_xy($_pdf,$footer[0]['adr_street'],50,40);
				pdf_show_xy($_pdf,lang('fax') . ': ' . $footer[0]['a_tel_fax'],200,40);
				pdf_show_xy($_pdf,$phpgw_info['user']['firstname'] . ' ' . $phpgw_info['user']['lastname'],400,50);
				pdf_show_xy($_pdf,$footer[0]['adr_postalcode'] . ' ' . $footer[0]['adr_locality'],50,30);
				pdf_show_xy($_pdf,lang('email') . ': ' . $footer[0]['d_email'],200,30);

				pdf_show_xy($_pdf,$footer[0]['adr_countryname'],50,20);
				pdf_show_xy($_pdf,lang('www') . ': ' . $footer[0]['url'],200,20);
			}
			else
			{
				pdf_show_xy($_pdf,lang('Please select your address in preferences !'),50,690);
			}
		}
	}

	class invoicePDF extends deliveryPDF
	{

		function createBody($_pdf)
		{
			global $invoice_id, $order_id, $phpgw, $phpgw_info;

			if ($phpgw_info['server']['db_type']=='pgsql') { $join = " JOIN "; }
			else { $join = " LEFT JOIN "; }

			pdf_set_font($_pdf,'Helvetica',11,winansi);

			$texty = pdf_get_value($_pdf,'texty',0)-25;
			pdf_show_xy($_pdf,lang('Invoice ID') . ': ' . $this->id,60,$texty);
			pdf_continue_text($_pdf,lang('Invoice date') . ': ' . $this->date);

			if ($this->orderText)
			{
				pdf_continue_text($_pdf,lang('Order') . ': ' . $this->orderText);
			}

			$texty -= 42;
			pdf_set_font($_pdf,'Helvetica',9,winansi);

			if (isset($phpgw_info['user']['preferences']['common']['currency']))
			{
				$currency = $phpgw_info['user']['preferences']['common']['currency'];
			}
			else
			{
				pdf_show_xy($_pdf,lang('Please select currency and your address in preferences !'),50,690);
			}
	
			$pageNumber=1;
			$texty = pdf_get_value($_pdf,'texty',0)-25; 

			pdf_show_xy($_pdf,lang('Pos.'),60,$texty);
			pdf_show_xy($_pdf,lang('Piece'),90,$texty);
			pdf_show_xy($_pdf,lang('Product ID'),130,$texty);
			pdf_show_xy($_pdf,lang('Name'),190,$texty);
			#pdf_show_xy($_pdf,lang('Description'),350,$texty);
			pdf_show_xy($_pdf,lang('a piece'),380,$texty);
			pdf_show_xy($_pdf,lang('Sum net'),430,$texty);
			pdf_show_xy($_pdf,lang('% tax'),480,$texty);
			pdf_show_xy($_pdf,lang('Sum'),520,$texty);

			$pos = 0;                                                                                                                                                                        
			$sum_netto = 0;                                                                                                                                                                  
			$sum_retail = 0;                                                                                                                                                                 
			$sum_piece = 0;

			if (!$invoice_id)
			{
				$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.piece,phpgw_inv_orderpos.tax "                                                                                                        
								. "FROM phpgw_inv_products $join phpgw_inv_orderpos ON phpgw_inv_products.id=phpgw_inv_orderpos.product_id "                                                                                          
								. "WHERE phpgw_inv_orderpos.order_id='$order_id'");
			}
			else
			{
				$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.order_id,phpgw_inv_orderpos.piece,phpgw_inv_orderpos.tax "
								. "FROM phpgw_inv_products $join phpgw_inv_orderpos ON "
								. "phpgw_inv_products.id=phpgw_inv_orderpos.product_id $join phpgw_inv_invoicepos ON "
								. "phpgw_inv_invoicepos.product_id=phpgw_inv_products.id $join phpgw_inv_invoice ON "
								. "phpgw_inv_invoice.order_id=phpgw_inv_orderpos.order_id "
								. "WHERE phpgw_inv_invoice.id='$invoice_id' AND phpgw_inv_invoicepos.invoice_id='$invoice_id'");
			}

			$texty = pdf_get_value($_pdf,'texty',0);

			while ($phpgw->db->next_record())
			{
				$pos++;
				$texty = $texty-20;
				pdf_show_xy($_pdf,$pos,60,$texty);
				pdf_show_xy($_pdf,$phpgw->db->f('piece'),90,$texty);
				pdf_show_xy($_pdf,$phpgw->db->f('id'),130,$texty);
				$name = $phpgw->strip_html($phpgw->db->f('name'));                                                                                                                            
				pdf_show_xy($_pdf,$name,190,$texty);
				pdf_show_xy($_pdf,$phpgw->db->f('price'),380,$texty);
				$price_net = (float)($phpgw->db->f('price'))*($phpgw->db->f('piece'));
				pdf_show_xy($_pdf,sprintf("%1.2f",$price_net),430,$texty);
				pdf_show_xy($_pdf,$phpgw->db->f('tax'),480,$texty);
				$retail = $phpgw->db->f('retail');
				$price_tax = (float)($retail*($phpgw->db->f('piece')));
				pdf_show_xy($_pdf,sprintf("%1.2f",$price_tax),520,$texty);
				$pro_descr = $phpgw->strip_html($phpgw->db->f('des'));                                                                                                                            
				#pdf_show_xy($_pdf,$pro_descr,350,$texty);
				pdf_show_boxed($_pdf,$pro_descr,200,$texty-50,170,48,'left');

				$texty = pdf_get_value($_pdf,'texty',0);

				if ($texty < 100)  
				{
					//neue Seite beginnen
					$pageNumber++;
					createFooter($_pdf);
					PDF_end_page($_pdf);
					PDF_begin_page($_pdf, 595, 842);
					pdf_set_font($_pdf,'Helvetica',9,winansi);
					$texty = 770;
					pdf_show_xy($_pdf,lang('Pos.'),60,$texty);
					pdf_show_xy($_pdf,lang('Piece'),90,$texty);
					pdf_show_xy($_pdf,lang('Product ID'),130,$texty);
					pdf_show_xy($_pdf,lang('Name'),190,$texty);
					pdf_show_xy($_pdf,lang('Description'),350,$texty);
					pdf_show_xy($_pdf,lang('a piece'),380,$texty);                                                                                                                                  
					pdf_show_xy($_pdf,lang('Sum net'),410,$texty);                                                                                                                                  
					pdf_show_xy($_pdf,lang('% tax'),440,$texty);                                                                                                                                    
					pdf_show_xy($_pdf,lang('Sum'),470,$texty);
					PDF_add_outline($_pdf, lang('Page') . ' ' . $pageNumber);
				}
				$summ_net += $price_net;
				$summ += $price_tax;
			}
			#$sum_price += ((float)$sum_piece);
			#$sum_sum += ((float)$sum_retail);
			pdf_show_xy($_pdf,sprintf("%s %s",lang('Summary in'),$currency),340,$texty-20);
			pdf_show_xy($_pdf,sprintf("%01.2f",$summ_net),430,$texty-20);
			pdf_show_xy($_pdf,sprintf("%01.2f",$summ),520,$texty-20);
		}
	}
?>
