<?php
require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'common'.'/'.'Base.php');
//!! Abstract class representing the base of the API
//! An abstract class representing the API base
/*!
This class is derived by all the API classes so they get the
database connection, database methods and the Observable interface.
*/
class BaseManager extends Base {

  // Constructor receiving an ADODB database object.
  function BaseManager(&$db)
  {
    $this->child_name = 'BaseManager';
    parent::Base($db);
  }

} //end of class

?>
