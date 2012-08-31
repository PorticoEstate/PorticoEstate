<?php

/**
 * phpGroupWare - controller: a part of a Facilities Management System.
 *
 * @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
 * @author Torstein Vadla <torstein.vadla@bouvet.no>
 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
 * @internal Development of this application was funded by http://www.bergen.kommune.no/
 * @package property
 * @subpackage controller
 * @version $Id: class.uicontrol.inc.php 8744 2012-01-31 18:38:02Z vator $
 */
phpgw::import_class('controller.uicase');

include_class('controller', 'control', 'inc/model/');
include_class('controller', 'check_list', 'inc/model/');

class controller_uitest {

  public $public_functions = array
      (
      'index' => true
  );

  public function index() {
    $uicase = new controller_uicase();

    $uicase->updateStatusForCases(506, 17230, 0);
  }

}

