<?php
	//include common logic for all templates
	include("common.php");
?>

<script type="text/javascript">

function get_address_search()
{
	var address = document.getElementById('address_txt').value;
	var div_address = document.getElementById('address_container');

	url = "index.php?menuaction=activitycalendar.uiarena.get_address_search&amp;phpgw_return_as=json&amp;search=" + address;

var divcontent_start = "<select name=\"address\" id=\"address\" size\"5\">";
var divcontent_end = "</select>";
	
	var callback = {
		success: function(response){
					div_address.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
				},
		failure: function(o) {
					 alert("AJAX doesn't work"); //FAILURE
				 }
	}
	var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
}
</script>

<div class="identifier-header">
	<h1><img src="<?php echo ACTIVITYCALENDAR_IMAGE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('arena') ?></h1>
	<div>
		<label><?php echo lang('name'); ?></label>
		 <?php if($arena->get_arena_name()){ echo $arena->get_arena_name(); } else { echo lang('no_value'); }?>
	</div>
</div>
<div class="yui-content">
	<div id="details">
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($arena->get_id()){ echo $arena->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col">
				<dt>
					<?php if($arena->get_arena_name() || $editable) { ?>
					<label for="name"><?php echo lang('name') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="arena_name" id="arena_name" value="<?php echo $arena->get_arena_name() ?>" />
					<?php
					}
					else
					{
						echo $arena->get_arena_name();
					}
					?>
				</dd>
				<dt>
					<?php if($arena->get_internal_arena_id() || $editable) { ?>
					<label for="internal_arena_id"><?php echo lang('internal_arena') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					$current_building_id = $arena->get_internal_arena_id();
					if ($editable)
					{
						?>
						<select name="internal_arena_id">
							<option value="0">Ingen arena valgt</option>
							<?php
							foreach($buildings as $building_id => $building_name)
							{
								echo "<option ".($current_building_id == $building_id? 'selected="selected"' : "")." value=\"{$building_id}\">".$building_name."</option>";
							}
							?>
						</select>
						<?php
					}
					else
					{
						echo activitycalendar_soarena::get_instance()->get_building_name($arena->get_internal_arena_id());
					}
					?>
				</dd>
				<dt>
					<?php if($arena->get_address() || $editable) { ?>
					<label for="address"><?php echo lang('address') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
					 	<input type="text" name="address" id="address_txt" value="<?php echo $arena->get_address() ?>" onkeyup="javascript:get_address_search()"/>
					 	<div id="address_container"></div>
					 	<label for="address_number"><?php echo lang('address_number') ?></label><input type="text" name="address_no" id="address_no"/>
					<?php
					}
					else
					{
						echo $arena->get_address();
					}
					?>
				</dd>
				<?php if($editable) {?>
					<dt>
						<label for="arena_active"><?php echo lang('active_arena') ?></label>
					</dt>
					<dd>
						<input type="checkbox" name="arena_active" id="arena_active" <?php if($arena->is_active()) { echo "checked='checked'";} ?>/>
					</dd>
				<?php 
				}else{ 
				?>
					<dt><label><?php if($arena->is_active()){?><font style="color: green;"><?php echo lang('active_arena');?></font><?php }else{ ?><font style="color: red;"><?php echo lang('inactive_arena');?></font><?php } ?></label></dt>
					<dd>&nbsp;</dd>
				<?php }?>
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_arena" value="' . lang('save') . '"/>';
					}
				?>
			</div>
			
		</form>
		
	</div>
</div>

