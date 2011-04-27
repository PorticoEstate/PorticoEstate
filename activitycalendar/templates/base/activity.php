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
							<option value="">Ingen organisasjon valgt</option>
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
							<option value="0">Ingen gruppe valgt</option>
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
							<option value="0">Ingen arena valgt</option>
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
				<dt>
					<?php if($activity->get_category() || $editable) { ?>
					<label for="category"><?php echo lang('category') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="category" id="category" value="<?php echo $activity->get_category() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_category();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_description() || $editable) { ?>
					<label for="description"><?php echo lang('description') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="description" id="description" value="<?php echo $activity->get_description() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_description();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_date_start() || $editable) { ?>
					<label for="start_date"><?php echo lang('date_start') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
						$start_date = $activity->get_date_start() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $activity->get_date_start()) : '-';
						$start_date_yui = $activity->get_date_start() ? date('Y-m-d', $activity->get_date_start()) : '';
						$start_date_cal = $GLOBALS['phpgw']->yuical->add_listener('start_date', $start_date);?>
					<?php if ($editable) {
							echo $GLOBALS['phpgw']->yuical->add_listener('start_date', $start_date);
						} else {
							echo $start_date;
						}
					?>
				</dd>
				<dt>
					<?php if($activity->get_date_end() || $editable) { ?>
					<label for="end_date"><?php echo lang('date_end') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
						$end_date = $activity->get_date_end() ? date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], $activity->get_date_end()) : '-';
						$end_date_yui = $activity->get_date_end() ? date('Y-m-d', $activity->get_date_end()) : '';
						$end_date_cal =  $GLOBALS['phpgw']->yuical->add_listener('end_date', $end_date);
					?>
					<?php if ($editable) {
							echo $GLOBALS['phpgw']->yuical->add_listener('end_date', $end_date);
						} else {
							echo $end_date;
					 }?>
					<br/>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_1() || $editable) { ?>
					<label for="contact_person_1"><?php echo lang('contact_person_1') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="contact_person_1" id="contact_person_1" value="<?php echo $activity->get_contact_person_1() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_contact_person_1();
					}
					?>
				</dd>
				<dt>
					<?php if($activity->get_contact_person_2() || $editable) { ?>
					<label for="contact_person_2"><?php echo lang('contact_person_2') ?></label>
					<?php  } ?>
				</dt>
				<dd>
					<?php
					if ($editable)
					{
					?>
						<input type="text" name="contact_person_2" id="contact_person_2" value="<?php echo $activity->get_contact_person_2() ?>" />
					<?php
					}
					else
					{
						echo $activity->get_contact_person_2();
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