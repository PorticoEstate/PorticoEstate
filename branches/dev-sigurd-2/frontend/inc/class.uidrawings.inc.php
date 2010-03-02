<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

    phpgw::import_class('frontend.uifrontend');

	/**
	 * Drawings
	 *
	 * @package Frontend
	 */

    class frontend_uidrawings extends frontend_uifrontend
    {

        public $public_functions = array
        (
            'index'     	=> true,
        );

		public function __construct()
		{
			parent::__construct();
		}

		public function index()
		{
			$receipt = array();

			$receipt['error'][]=array('msg'=>'Eksempel på feilmelding');
			$receipt['message'][]=array('msg'=>'Eksempel på gladmelding');

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
				'tabs'			=> $this->tabs,
				'date_start'	=> $GLOBALS['phpgw']->yuical->add_listener('date_start', $date_start),
				'date_end'		=> $GLOBALS['phpgw']->yuical->add_listener('date_end', $date_end),
			);

            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'header'));
            $GLOBALS['phpgw']->xslttpl->add_file(array('frontend', 'demo'));
	      	$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array(
                'header'    => $this->header_state,
                'demo_2'    => $data
            ));
		}
    }
