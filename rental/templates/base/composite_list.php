<?php
	include("common.php");
?>

<script>

	YAHOO.util.Event.addListener(
	'ctrl_add_rental_composite',
	'click',
	function(e)
	{
  		YAHOO.util.Event.stopEvent(e);
		window.location = 'index.php?menuaction=rental.uicomposite.add';
	}
);
</script>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/go-home.png" /> <?php echo lang('rc') ?></h1>


<?php
if($this->isExecutiveOfficer())
{
?>
<fieldset>
	<!-- Create new rental composite -->
	<h3><?php echo lang('t_new_composite') ?></h3>
	<input type="submit" name="ctrl_add_rental_composite" id="ctrl_add_rental_composite" value="<?php echo lang('f_new_rc') ?>" />
</fieldset>
<?php
}
?>
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
$list_id = 'all_composites';
$url_add_on = '&amp;type=all_composites';
include('composite_list_partial.php');
?>