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
	/* $Id$ */

	class bostock
	{
		function bostock()
		{
			$this->sostock	= CreateObject('stocks.sostock');
			$this->network	= CreateObject('phpgwapi.network');

			$this->read_sessiondata();

			$country = get_var('country',Array('POST','GET'));

			if ($country)
			{
				$this->country = $country;
			}
			else
			{
				$prefs = $this->read_prefs();
				if ($prefs['country'])
				{
					$this->country = $prefs['country'];
				}
				else
				{
					$this->country = 'US';
				}
			}
		}

		function save_sessiondata($data)
		{
			$GLOBALS['phpgw']->session->appsession('session_data','stocks',$data);
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','stocks');

			$this->country	= $data['country'];
		}

		// return content of a url as a string array
		function http_fetch($url,$post,$port,$proxy)
		{
	 		return $this->network->gethttpsocketfile($url);
		}

		function get_url()
		{
			switch($this->country)
			{
				case 'DE':	$url = 'http://de.finance.yahoo.com/d/quotes_de.csv?f=sl1d1t1c1ohgv&e=.csv&s='; break;
				default :	$url = 'http://finance.yahoo.com/d/quotes.csv?f=sl1d1t1c1ohgv&e=.csv&s='; break;
			}
			return $url;
		}

		function get_quotes($stocklist)
		{
			switch($this->country)
			{
				case 'US':	$sep = ','; break;
				case 'DE':	$sep = ';'; break;
				default :	$sep = ','; break;
			}

			if (! $stocklist)
			{
				return array();
			}

			while (list($symbol,$name) = each($stocklist))
			{
				$symbollist[] = $symbol;
			//	$symbol = rawurlencode($symbol);
				$symbolstr .= $symbol;

				if ($i++<count($stocklist)-1)
				{
					$symbolstr .= '+';
				}
			}

			if ($this->country == 'US')
			{
				$regexp_stocks = '/^\"(' . implode('|',$symbollist) . ')/';
			}
			else
			{
				$regexp_stocks = '/(' . implode('|',$symbollist) . ')/';
			}

			$url = $this->get_url($this->country) . $symbolstr;
			$lines = $this->http_fetch($url,false,80,'');

			$quotes = array();
			$i = 0;

			if ($lines)
			{
				while ($line = each($lines))
				{
					$line = $lines[$i];

					if (preg_match($regexp_stocks,$line))
					{
						$line = ereg_replace('"','',$line);

						if ($this->country == 'DE')
						{
							$line = str_replace(',','.',$line);
						}

						list($symbol,$price0,$date,$time,$dchange,$price1,$price2) = split($sep,$line);

						if ($price1>0 && $dchange!=0)
						{
							$pchange = round(10000*($dchange)/$price1)/100;
						}
						else
						{
							$pchange = 0;
						}

						if ($pchange>0)
						{
							$pchange = '+' . $pchange;
						}

						$name = $stocklist[$symbol];

						if (! $name)
						{
							$name = $symbol;
						}

						$quotes[] = array
						(
							'symbol'	=> $symbol,
							'price0'	=> $price0,
							'date'		=> $date,
							'time'		=> $time,
							'dchange'	=> $dchange,
							'price1'	=> $price1,
							'price2'	=> $price2,
							'pchange'	=> $pchange,
							'name'		=> $name
						);
					}
					$i++;
				}
				return $quotes;
			}
		}

		function read_prefs()
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			$prefs = array();

			if ($GLOBALS['phpgw_info']['user']['preferences']['stocks'])
			{
				$prefs['mainscreen']	= $GLOBALS['phpgw_info']['user']['preferences']['stocks']['mainscreen'];
				$prefs['country']		= $GLOBALS['phpgw_info']['user']['preferences']['stocks']['country'];
			}
			else
			{
				$prefs['mainscreen']	= 'disabled';
				$prefs['country']		= 'us';
			}
			return $prefs;
		}

		function get_default()
		{
			$def = array();
			$def['LNUX']	= 'VA Linux';
			$def['RHAT']	= 'Redhat';
			return $def;
		}

		function save_prefs($prefs)
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			if (is_array($prefs))
			{
				if ($prefs['mainscreen'])
				{
					$GLOBALS['phpgw']->preferences->change('stocks','mainscreen','enabled');
				}
				else
				{
					$GLOBALS['phpgw']->preferences->change('stocks','mainscreen','disabled');
				}
				$GLOBALS['phpgw']->preferences->change('stocks','country',$prefs['country']);
				$GLOBALS['phpgw']->preferences->save_repository(True);
			}
		}

		function read_stocks()
		{
			return $this->sostock->read_stocks($this->country);
		}

		function read_single($stock_id)
		{
			return $this->sostock->read_single($stock_id);
		}

		function save_stock($values)
		{
			if (!$values['name'])
			{
				$values['name'] = $values['symbol'];
			}

			$values['symbol']	= strtoupper($values['symbol']);

			if ($values['id'] && $values['id'] != 0)
			{
				$this->sostock->edit_stock($values);
			}
			else
			{
				$this->sostock->add_stock($values);
			}
		}

		function delete_stock($stock_id)
		{
			$this->sostock->delete_stock($stock_id);
		}

		function get_savedstocks()
		{
			$stocks = $this->read_stocks();

			if (is_array($stocks))
			{
				while (list($null,$stock) = each($stocks))
				{
					$symbol	= rawurlencode($GLOBALS['phpgw']->strip_html($stock['symbol']));
					$name	= $GLOBALS['phpgw']->strip_html($stock['name']);

					if ($symbol)
					{
						if (! $name)
						{
							$name = $GLOBALS['phpgw']->strip_html($stock['symbol']);
						}
						$stocklist[$symbol] = $name;
					}
				}
			}
			else
			{
				$def = $this->get_default();
				$stocklist['LNUX'] = $def['LNUX'];
				$stocklist['RHAT'] = $def['RHAT'];
			}
			return $stocklist;
		}
	}
?>
