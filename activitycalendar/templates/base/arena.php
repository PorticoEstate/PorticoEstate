<?php
	//include common logic for all templates
	include("common.php");
?>

<script xmlns:php="http://php.net/xsl" type="text/javascript">
var endpoint = 'activitycalendar';

YAHOO.activitycalendar.autocompleteHelper('index.php?menuaction=' + endpoint + '.uiarena.get_address&address=true&phpgw_return_as=json&',
    'field_address_txt',
    'field_address_hidden',
    'address_container',
    'descr'
);
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
						<input type="text" name="address" id="address" value="<?php echo $arena->get_address() ?>" />
						<div class="autocomplete">
						<input id="field_address_hidden" name="address_hidden" type="hidden" value=""><input name="address_txt" type="text" id="field_address_txt" value=""><div id="address_container"></div>
						</div>
					<?php
					}
					else
					{
						echo $arena->get_address();
					}
					?>
				</dd>
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

