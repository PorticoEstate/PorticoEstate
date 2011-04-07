<?php
	//include common logic for all templates
	include("common.php");
?>

<div class="yui-content">
	<div id="details">
		<h1><img src="<?php echo ACTIVITYCALENDAR_TEMPLATE_PATH ?>images/32x32/custom/contact.png" /><?php echo lang('activity') ?></h1>
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?php if($activity->get_id()){ echo $activity->get_id(); } else { echo '0'; }  ?>"/>
			<dl class="proplist-col">
				<dt>
					<?php if($activity->get_organization_id() || $editable) { ?>
					<label for="organization_id"><?php echo lang('organization') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<?php
					$current_organization_id = $activity->get_organization_id();
					if ($editable)
					{
						?>
						<select name="organization_id">
							<option>Ingen organisasjon valgt</option>
							<?php
							foreach($organizations as $organization)
							{
								echo "<option ".($current_organization_id == $organization->get_id() ? 'selected="selected"' : "")." value=\"{$organization->get_id()}\">".$organization->get_name()."</option>";
							}
							?>
						</select>
						<?php
					?>
					<?php
					}
					else
					{
						echo $activity->get_organization_id();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_group_id() || $editable) { ?>
					<label for="group_id"><?php echo lang('group') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<?php
					$current_group_id = $activity->get_group_id();
					if ($editable)
					{
						?>
						<select name="group_id">
							<option>Ingen gruppe valgt</option>
							<?php
							foreach($groups as $group)
							{
								echo "<option ".($current_group_id == $group->get_id() ? 'selected="selected"' : "")." value=\"{$group->get_id()}\">".$group->get_name()."</option>";
							}
							?>
						</select>
						<?php
					?>
					<?php
					}
					else
					{
						echo $activity->get_group_id();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_arena() || $editable) { ?>
					<label for="arena"><?php echo lang('arena') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					$current_arena_id = $activity->get_arena();
					if ($editable)
					{
						?>
						<select name="arena_id">
							<option>Ingen arena valgt</option>
							<?php
							foreach($arenas as $arena)
							{
								echo "<option ".($current_arena_id == $arena->get_id() ? 'selected="selected"' : "")." value=\"{$arena->get_id()}\">".$arena->get_arena_name()."</option>";
							}
							?>
						</select>
						<?php
					}
					else
					{
						echo $activity->get_arena();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_district() || $editable) { ?>
					<label for="district"><?php echo lang('district') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="district" id="district" value="<?php echo $activity->get_district() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_district();
					}
					?>
				</dd>
			</dl>
			<div class="form-buttons">
				<?php
					if ($editable) {
						echo '<input type="submit" name="save_activity" value="' . lang('save') . '"/>';
					}
				?>
			</div>
			
		</form>
		
	</div>
</div>