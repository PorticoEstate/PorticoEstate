<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage test
 	* @version $Id$
	*/




	/**
	 * Description
	 * @package property
	 */


	class property_test
	{
		var $public_functions = array
		(
			'date' => true
		);


		function __construct()
		{
		  	$GLOBALS['phpgw']->css->add_external_file('rental/templates/base/css/base.css');
		}


		function date()
		{
			$date1 = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

			$start_field = $GLOBALS['phpgw']->yuical->add_listener('start_date', $date1);
			$end_field = $GLOBALS['phpgw']->yuical->add_listener('end_date');

			//Only if not xslt_app
			$GLOBALS['phpgw']->common->phpgw_header(true);

			$html = <<<HTML
			<div class="yui-content">
				<div class="details">
					<form action="#" method="post">
						<dl class="proplist-col">
							<dt>
								<label for="name">Gjelder fra</label>
							</dt>
							<dd>
								{$start_field}
							</dd>
							<dt>
								<label for="name">Gjelder til</label>
							</dt>
							<dd>
								{$end_field}
							</dd>
					</form>
				</div>
			</div>
HTML;

			echo $html;
//			$GLOBALS['phpgw']->common->phpgw_exit();
		}
	}
