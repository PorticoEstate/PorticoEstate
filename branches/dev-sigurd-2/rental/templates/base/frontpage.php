<?php 
	include("common.php");
	phpgwapi_yui::load_widget('tabview');        
  phpgwapi_yui::tabview_setup('composite_tabview');
?>

<h1><img src="<?= RENTAL_TEMPLATE_PATH ?>images/32x32/places/user-desktop.png" /> <?= lang('rental_dashboard_title') ?></h1>