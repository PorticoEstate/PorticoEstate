<?php

/**
 * phpGroupWare - controller: a part of a Facilities Management System.
 *
 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
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
 * @version $Id$
 */
include_class('controller', 'model', 'inc/model/');

class controller_document extends controller_model {

  protected $title;
  protected $description;
  protected $name;
  protected $type;
  protected $type_id;
  protected $procedure_id;

  public function __construct(int $id = null) {
    $doc_id = intval($id);
    parent::__construct($doc_id);
  }

  public function set_title($title) {
    $this->title = $title;
  }

  public function get_title() {
    return $this->title;
  }

  public function set_description($description) {
    $this->description = $description;
  }

  public function get_description() {
    return $this->description;
  }

  public function set_name($name) {
    $this->name = $name;
  }

  public function get_name() {
    return $this->name;
  }

  public function set_type($type) {
    $this->type = $type;
  }

  public function get_type() {
    return $this->type;
  }

  public function set_type_id($type_id) {
    $this->type_id = $type_id;
  }

  public function get_type_id() {
    return $this->type_id;
  }

  public function set_procedure_id($procedure_id) {
    $this->procedure_id = $procedure_id;
  }

  public function get_procedure_id() {
    return $this->procedure_id;
  }

  public function serialize() {
    return array(
        'id' => $this->get_id(),
        'title' => $this->get_title(),
        'description' => $this->get_description(),
        'name' => $this->get_name(),
        'type' => lang($this->get_type())
    );
  }

}

?>