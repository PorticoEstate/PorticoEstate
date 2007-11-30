<?php
	/*******************************************************************\
	* phpGroupWare - Stock Quotes                                       *
	* http://www.phpgroupware.org                                       *
	*                                                                   *
	* based on PStocks v.0.1                                            *
	* http://www.dansteinman.com/php/pstocks/                           *
	* Copyright (C) 1999 Dan Steinman (dan@dansteinman.com)             *
	*                                                                   *
	* Written by Bettina Gille [ceb@phpgroupware.org]                   *
	* -----------------------------------------------                   *
	* Copyright 2001 - 2003 Free Software Foundation, Inc               *
	*                                                                   *
	* This program is free software; you can redistribute it and/or     *
	* modify it under the terms of the GNU General Public License as    *
	* published by the Free Software Foundation; either version 2 of    *
	* the License, or (at your option) any later version.               *
	*                                                                   *
	* This program is distributed in the hope that it will be useful,   *
	* but WITHOUT ANY WARRANTY; without even the implied warranty of    *
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU  *
	* General Public License for more details.                          *
	*                                                                   *
	* You should have received a copy of the GNU General Public License *
	* along with this program; if not, write to the Free Software       *
	* Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.         *
	\*******************************************************************/
	/* $Id: class.uistock.inc.php 17904 2007-01-24 16:13:29Z Caeies $ */

	class uistock
	{
		var $public_functions = array
		(
			'index'			=> True,
			'preferences'	=> True,
			'edit_stock'	=> True,
			'list_stocks'	=> True
		);

		function uistock()
		{
			$this->bostock		= CreateObject('stocks.bostock');
			$this->sbox			= CreateObject('phpgwapi.sbox');
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->country		= $this->bostock->country;
		}

		function save_sessiondata()
		{
			$data = array
			(
				'country' => $this->country
			);
			$this->bostock->save_sessiondata($data);
		}

		function set_app_langs()
		{
			$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
			$GLOBALS['phpgw']->template->set_var('tr_color1',$GLOBALS['phpgw_info']['theme']['row_on']);
			$GLOBALS['phpgw']->template->set_var('tr_color2',$GLOBALS['phpgw_info']['theme']['row_off']);
			$GLOBALS['phpgw']->template->set_var('lang_company',lang('Company name'));
			$GLOBALS['phpgw']->template->set_var('lang_symbol',lang('Symbol'));
			$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
			$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
			$GLOBALS['phpgw']->template->set_var('lang_country',lang('Country'));
			$GLOBALS['phpgw']->template->set_var('lang_add_stock',lang('Add new stock'));
			$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
			$GLOBALS['phpgw']->template->set_var('lang_save',lang('Save'));
			$GLOBALS['phpgw']->template->set_var('lang_stocks',lang('Stock Quotes'));
			$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
			$GLOBALS['phpgw']->template->set_var('lang_submit',lang('Submit'));
			$GLOBALS['phpgw']->template->set_var('lang_select_country',lang('Select country'));
			$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('cancel'));
		}

		function display_app_header()
		{
			$GLOBALS['phpgw']->template->set_file(array('header' => 'header.tpl'));
			$GLOBALS['phpgw']->template->set_block('header','stock_header');

			$this->set_app_langs();

			$GLOBALS['phpgw']->template->set_var('link_stocks',$GLOBALS['phpgw']->link('/index.php','menuaction=stocks.ui.list_stocks&country=' . $this->country));
			$GLOBALS['phpgw']->template->set_var('lang_select_stocks',lang('Select stocks to display'));
			$GLOBALS['phpgw']->template->fp('app_header','stock_header');
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
		}

		function return_html($quotes)
		{
			$return_html = '<table cellspacing="1" cellpadding="0" border="0" bgcolor="black"><tr><td>'
			. '<table cellspacing="1" cellpadding="2" border="0" bgcolor="white">'
			. '<tr><td><b>' . lang('Name') . '</b></td><td><b>' . lang('Symbol') . '</b></td><td align="right"><b>' . lang('Price') . '</b></td><td align="right">'
			. '<b>&nbsp;' . lang('Change') . '</b></td><td align="right"><b>' . lang('%') . '&nbsp;' . lang('Change') . '</b></td><td align="center"><b>' . lang('Date') . '</b></td><td align="center">'
					. '<b>' . lang('Time') . '</b></td></tr>';

			for ($i=0;$i<count($quotes);$i++)
			{
				$q = $quotes[$i];
				$symbol = $q['symbol'];
				$name = $q['name'];
				$price0 = $q['price0']; // today's price
				$price1 = $q['price1'];
				$price2 = $q['price2'];
				$dollarchange = $q['dchange'];
				$percentchange = $q['pchange'];
				$date = $q['date'];
				$time = $q['time'];
				$volume = $q['volume'];

				if ($dollarchange < 0)
				{
					$color = 'red';
				}
				else
				{
					$color = 'green';
				}

				$return_html .= '<tr><td>' . $name . '</td><td>' . $symbol . '</td><td align="right">' . $price0 . '</td><td align="right"><font color="'
								. $color . '">' . $dollarchange . '</font></td><td align="right"><font color="' . $color . '">' . $percentchange
								. '</font></td><td align="center">' . $date . '</td><td align="center">' . $time . '</td></tr>';
			}

			$return_html .= '</table></td></tr></table>';
			return $return_html;
		}

		function return_values($quotes)
		{
			for ($i=0;$i<count($quotes);$i++)
			{
				$data[] = array
				(
					'symbol'		=> $quotes[$i]['symbol'],
					'name'			=> $quotes[$i]['name'],
					'price0'		=> $quotes[$i]['price0'],
					'price1'		=> $quotes[$i]['price1'],
					'price2'		=> $quotes[$i]['price2'],
					'dollarchange'	=> $quotes[$i]['dchange'],
					'percentchange'	=> $quotes[$i]['pchange'],
					'date'			=> $quotes[$i]['date'],
					'time'			=> $quotes[$i]['time'],
					//'volume'		=> $quotes[$i]['volume'],
					'color'			=> ($quotes[$i]['dchange'] < 0?'red':'green')
				);
			}

			//_debug_array($data);

			$values = array
			(
				'lang_name'		=> lang('Name'),
				'lang_symbol'	=> lang('Symbol'),
				'lang_price'	=> lang('Price'),
				'lang_change'	=> lang('Change'),
				'lang_date'		=> lang('Date'),
				'lang_time'		=> lang('Time'),
				'values'		=> $data
			);
			return $values;
		}

		function return_quotes($ui = 'home')
		{
			$stocklist = $this->bostock->get_savedstocks();
			$quotes = $this->bostock->get_quotes($stocklist);

			switch($ui)
			{
				case 'page':	return $this->return_html($quotes); break;
				default:		return $this->return_values($quotes); break;
			}
		}

		function selected_country()
		{
			switch($this->country)
			{
				case 'US': $country_sel[0]=' selected'; break;
				case 'DE': $country_sel[1]=' selected'; break;
			}

			$country_list = '<option value="US"' . $country_sel[0] . '>' . lang('united states') . '</option>' . "\n"
				. '<option value="DE"' . $country_sel[1] . '>' . lang('germany') . '</option>' . "\n"
				. '<option value="">' . lang('Select country') . '</option>' . "\n";

			return $country_list;
		}

		function index()
		{
			$this->display_app_header();
			$GLOBALS['phpgw']->template->set_file(array('quotes_list' => 'main.tpl'));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Stock Quotes') . ': ' . lang('display stock quotes');
			$GLOBALS['phpgw']->template->set_var('country_list',$this->selected_country());
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'stocks.ui.index')));
			$GLOBALS['phpgw']->template->set_var('quotes',$this->return_quotes('page'));
			$GLOBALS['phpgw']->template->pfp('out','quotes_list');
			$this->save_sessiondata();
		}

		function list_stocks()
		{
			$action 	= get_var('action',Array('GET'));
			$stock_id	= get_var('stock_id',Array('GET'));

			$link_data = array
			(
				'menuaction'	=> 'stocks.ui.list_stocks',
				'country'		=> $this->country
			);

			if ($action == 'delete')
			{
				$this->bostock->delete_stock($stock_id);
				Header('Location: ' . $GLOBALS['phpgw']->link('/index.php',$link_data));
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('stock_list_t' => 'list.tpl'));
			$GLOBALS['phpgw']->template->set_block('stock_list_t','stock_list','list');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Stock Quotes') . ': ' . lang('stock quotes to display');

			$GLOBALS['phpgw']->template->set_var('h_lang_edit',lang('Edit'));
			$GLOBALS['phpgw']->template->set_var('h_lang_delete',lang('Delete'));
			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw']->template->set_var('country_list',$this->selected_country($this->country));

			$stocks = $this->bostock->read_stocks();

			if (is_array($stocks))
			{
				while (list($null,$stock) = each($stocks))
				{
					$this->nextmatchs->template_alternate_row_color($this->t);

					$GLOBALS['phpgw']->template->set_var(array
					(
						'ssymbol' => $GLOBALS['phpgw']->strip_html($stock['symbol']),
						'sname' => $GLOBALS['phpgw']->strip_html($stock['name']),
						'scountry' => $this->sbox->get_full_name($stock['country'])
					));

					$link_data['stock_id']	= $stock['id'];
					$link_data['action']	= 'delete';
					$link_data['menuaction'] = 'stocks.ui.list_stocks';
					$GLOBALS['phpgw']->template->set_var('delete',$GLOBALS['phpgw']->link('/index.php',$link_data));

					$link_data['menuaction'] = 'stocks.ui.edit_stock';
					unset($link_data['action']);
					$GLOBALS['phpgw']->template->set_var('edit',$GLOBALS['phpgw']->link('/index.php',$link_data));
					$GLOBALS['phpgw']->template->fp('list','stock_list',True);
				}
			}
			$link_data['menuaction'] = 'stocks.ui.edit_stock';
			unset($link_data['stock_id']);
			$GLOBALS['phpgw']->template->set_var('addurl',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'stocks.ui.index')));
			$this->save_sessiondata();
			$GLOBALS['phpgw']->template->pfp('out','stock_list_t',True);
		}

		function preferences()
		{
			$prefs = get_var('prefs',Array('POST'));

			$link_data = array
			(
				'menuaction' => 'stocks.ui.preferences'
			);

			if ($prefs['save'])
			{
				$this->bostock->save_prefs($prefs);
				$GLOBALS['phpgw']->redirect_link('/preferences/index.php');
			}

			$GLOBALS['phpgw']->common->phpgw_header();
			$GLOBALS['phpgw']->template->set_file(array('stock_prefs' => 'preferences.tpl'));
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Stock Quotes') . ': ' . lang('preferences');

			$this->set_app_langs();
			$prefs = $this->bostock->read_prefs();

			$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('lang_def_country',lang('Default country'));
			$GLOBALS['phpgw']->template->set_var('lang_display',lang('Display stocks on main screen is enabled'));
			$GLOBALS['phpgw']->template->set_var('mainscreen', '<input type="checkbox" name="prefs[mainscreen]" value="True"'
										. ($prefs['mainscreen'] == 'enabled'?' checked':'') . '>');

			$GLOBALS['phpgw']->template->set_var('country_list',$this->selected_country($prefs['country']));

			$GLOBALS['phpgw']->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/preferences/index.php'));
			$GLOBALS['phpgw']->template->pfp('out','stock_prefs',True);
		}

		function edit_stock()
		{
			$values		= get_var('values',Array('POST'));
			$stock_id	= get_var('stock_id',Array('GET','POST'));

			$link_data = array
			(
				'menuaction'	=> 'stocks.ui.edit_stock',
				'country'		=> $this->country
			);

			if ($values['save'])
			{
				if($stock_id)
				{
					$values['id']		= $stock_id;
				}
				$values['symbol']	= strtoupper($values['symbol']);
				$values['access']	= 'public';

				$this->bostock->save_stock($values);
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'stocks.ui.list_stocks'));
			}

			$this->display_app_header();

			$GLOBALS['phpgw']->template->set_file(array('edit' => 'preferences_edit.tpl'));

			$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php',$link_data));

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Stock Quotes') . ': ' . ($stock_id?lang('edit stock quote'):lang('add stock quote'));

			$GLOBALS['phpgw']->template->set_var('hidden_vars','<input type="hidden" name="stock_id" value="' . $stock_id . '">' . "\n");

			if ($stock_id)
			{
				$stock = $this->bostock->read_single($stock_id);
				$this->country	= $stock['country'];
				$link_data['stock_id']	= $stock_id;
			}

			$GLOBALS['phpgw']->template->set_var('country_list',$this->selected_country($this->country));
			$GLOBALS['phpgw']->template->set_var('symbol',$GLOBALS['phpgw']->strip_html($stock['symbol']));
			$GLOBALS['phpgw']->template->set_var('name',$GLOBALS['phpgw']->strip_html($stock['name']));

			$GLOBALS['phpgw']->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'stocks.ui.list_stocks')));
			$this->save_sessiondata();
			$GLOBALS['phpgw']->template->pfp('out','edit');
		}
	}
?>
