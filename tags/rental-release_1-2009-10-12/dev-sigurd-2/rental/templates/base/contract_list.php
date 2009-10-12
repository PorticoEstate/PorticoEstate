<?php
	include("common.php");
?>

<script>
	YAHOO.util.Event.addListener(
		'ctrl_add_rental_contract',
		'click',
		function(e)
		{
	  	YAHOO.util.Event.stopEvent(e);
	  	
		window.location = 'index.php?menuaction=rental.uicontract.add&amp;location_id=' + document.getElementById('location_id').value;
    }
   );
</script>

<h1><img src="<?php echo RENTAL_TEMPLATE_PATH ?>images/32x32/text-x-generic.png" /> <?php echo lang('contracts') ?></h1>

<?php
if($this->isAdministrator() || $this->isExecutiveOfficer())
{
?>
<fieldset>
	<!-- New contract -->
	<h3><?php echo lang('t_new_contract') ?></h3>
	<select name="location_id" id="location_id">
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
	<input type="submit" name="ctrl_add_rental_contract" id="ctrl_add_rental_contract" value="<?php echo lang('f_new_contract') ?>" />
</fieldset>
<?php
}

$list_form = true; 
$list_id = 'all_contracts';
$url_add_on = '&amp;type='.$list_id;
$editable = false;
$extra_cols = array(
	array("key" => "type", "label" => lang('title'), "index" => 3),
	array("key" => "composite", "label" => lang('composite'), "index" => 4),
	array("key" => "party", "label" => lang('party'), "index" => 5),
	array("key" => "old_contract_id", "label" => lang('old_contract_id'), "index" => 6)
);
include('contract_list_partial.php');
?>