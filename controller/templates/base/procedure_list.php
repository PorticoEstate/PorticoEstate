<?php
	include("common.php");
?>

<script>

	YAHOO.util.Event.addListener(
	'ctrl_add_controller_procedure',
	'click',
	function(e)
	{
  		YAHOO.util.Event.stopEvent(e);
		window.location = 'index.php?menuaction=controller.uiprocedure.add';
	}
);
</script>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/go-home.png" /> <?php echo lang('rc') ?></h1>


<fieldset>
	<!-- Create new precedure -->
	<h3><?php echo lang('t_new_procedure') ?></h3>
	<input type="submit" name="ctrl_add_controller_procedure" id="ctrl_add_controller_procedure" value="<?php echo lang('f_new_procedure') ?>" />
</fieldset>

<?php
/* TODO: Fix column selector:
<fieldset>
	<!-- Select table columns -->
	<h3><?php echo lang('t_functions') ?></h3>
	<input type="button" id="dt-options-link" name="dt-options-link" value="<?php echo lang('f_select_columns') ?>" />
</fieldset>
*/
?>
<?php
$list_form = true;
$list_id = 'all_procedures';
$url_add_on = '&amp;type=all_procedures';
include('procedure_list_partial.php');
?>