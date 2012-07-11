<?php
	include("common.php");
?>

<script type="text/javascript">

	YAHOO.util.Event.addListener(
		'ctrl_add_activitycalendar_arena',
		'click',
		function(e)
		{
            YAHOO.util.Event.stopEvent(e);
            window.location = 'index.php?menuaction=activitycalendar.uiarena.add';
        }
   );
</script>

<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/x-office-address-book.png" /> <?php echo lang('arenas') ?></h1>

<fieldset>
	<input type="submit" name="ctrl_add_activitycalendar_arena" id="ctrl_add_activitycalendar_arena" value="<?php echo lang('f_new_arena') ?>" />
</fieldset>


<?php
	$list_form = true;
	$list_id = 'all_arenas';
	$url_add_on = '&amp;type=all_arenas';
	include('arena_list_partial.php');
?>