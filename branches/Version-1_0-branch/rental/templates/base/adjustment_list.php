<?php
	include("common.php");
?>

<script>
	YAHOO.util.Event.addListener(
		'ctrl_add_adjustment',
		'click',
		function(e)
		{
	  	YAHOO.util.Event.stopEvent(e);
	  	resp_id = document.getElementById('responsibility_id').value;
			window.location = 'index.php?menuaction=rental.uiadjustment.add&amp;responsibility_id=' + resp_id;
		}
	);
</script>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/mimetypes/x-office-spreadsheet.png" /> <?php echo lang('adjustment_list') ?></h1>
<?php
if($this->isAdministrator() || $this->isExecutiveOfficer())
{
?>
<form action="#" method="GET">
	<fieldset>
		<!-- Create new adjustment -->
		<h3><?php echo lang('new_adjustment') ?></h3>
		<select name="responsibility_id" id="responsibility_id">
			<?php
			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			foreach($types as $id => $label)
			{
	
				$names = $this->locations->get_name($id);
				if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
					{
					?>
						<option 
							value="<?php echo $id ?>"
						>
							<?php echo lang($label) ?>
						</option>
					<?php
					}
				}
			}
			?>
		</select>
		<input type="submit" name="ctrl_add_adjustment" id="ctrl_add_adjustment" value="<?php echo lang('new_adjustment') ?>" />
	</fieldset>
</form>

<?php
}
$list_form = true; 
$list_id = 'non_manual_adjustments';
$url_add_on = '&amp;type='.$list_id;
$editable = false;
$extra_cols = array();
$hide_cols = array('price_item_id','new_price');
include('adjustment_list_partial.php');
?>