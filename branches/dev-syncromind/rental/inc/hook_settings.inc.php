<?php
	/**
	* phpGroupWare - rental
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
	* @package rental
	* @subpackage core
 	* @version $Id$
	*/


	create_input_box('Area decimal places','area_decimal_places', '0 - 2');
	create_input_box('currency decimal places','currency_decimal_places', '0 - 2');
	create_input_box('Count decimal places','count_decimal_places', '0 - 2');
	create_input_box('Thousands separator','thousands_separator', 'As in "." or ","');
	create_input_box('Decimal separator','decimal_separator', 'As in "." or ","');
	create_input_box('responsibility','responsibility', '6 characters');
	create_input_box('project_id','project_id', '1-6 characters');
	