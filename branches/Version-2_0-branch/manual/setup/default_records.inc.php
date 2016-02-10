<?php
// clean up from previous install

$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'manual'");
$GLOBALS['phpgw_setup']->oProc->next_record();
$app_id = $GLOBALS['phpgw_setup']->oProc->f('app_id');

$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_locations WHERE app_id = {$app_id} AND name != 'run'");


$GLOBALS['phpgw']->locations->add('.documents', 'Documents', 'manual');
