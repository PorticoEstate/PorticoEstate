<?php
//include common logic for all templates
//	include("common.php");
	$act_so = activitycalendar_soactivity::get_instance();
	$contpers_so = activitycalendar_socontactperson::get_instance();
?>
<div class="yui-content">
	<div id="details">
		<?php if ($message) { ?>
			<div class="success">
				<?php echo $message; ?>
			</div>
		<?php } else if ($error) { ?>
			<div class="error">
				<?php echo $error; ?>
			</div>
		<?php } ?>
	</div>
	<DIV class="pageTop">
		<h1><?php echo lang('activity') ?></h1>
	</DIV>
	<form action="#" method="post">
		<input type="hidden" name="id" value="<?php
		if ($activity->get_id()) {
			echo $activity->get_id();
		} else {
			echo '0';
		}
		?>"/>
		<dl class="proplist-col">
			<div class="form-buttons">
				<?php if ($change_request) { ?>
					<input type="submit" name="activity_ok" value="<?php echo lang('activity_ok') ?>" />
					<input type="submit" name="change_request" value="<?php echo lang('change_activity') ?>" />
<?php }
?>
			</div>
			<FIELDSET title="Hva">
				<LEGEND>Hva</LEGEND>
				<dt>
					<label for="title"><?php echo lang('activity_title') ?></label>
				</dt>
				<dd>
					<?php echo $activity->get_title(); ?>
				</dd>
				<dt>
					<label for="description"><?php echo lang('description') ?></label>
				</dt>
				<dd>
					<?php echo $activity->get_description(); ?>
				</dd>

				<dt>
					<label for="category"><?php echo lang('category') ?></label>
				</dt>
				<dd>
					<?php
					if ($activity->get_category()) {
						echo $act_so->get_category_name($activity->get_category());
					}
					?>
				</dd>
			</FIELDSET>
			<FIELDSET id="hvem"><legend>For hvem</legend>
				<dt>
					<label for="target"><?php echo lang('target') ?></label>
				</dt>
				<dd>
					<?php
					if ($activity->get_target()) {
						$current_target_ids = $activity->get_target();
						$current_target_id_array = explode(",", $current_target_ids);
						foreach ($current_target_id_array as $curr_target) {
							echo $act_so->get_target_name($curr_target) . '<br/>';
						}
					}
					?>
				</dd>
				<dt>
				<input type="checkbox" name="special_adaptation" id="special_adaptation"<?php echo $activity->get_special_adaptation() ? ' checked="checked"' : '' ?> disabled="disabled" /><label for="special_adaptation"><?php echo lang('special_adaptation') ?></label>
				</dt>
			</FIELDSET>
			<FIELDSET title="hvor">
				<LEGEND>Hvor og når</LEGEND>
<?php if ($activity->get_internal_arena()) { ?>
					<dt>
						<label for="arena"><?php echo lang('building') ?></label>
					</dt>
					<dd>
						<?php echo activitycalendar_soarena::get_instance()->get_building_name($activity->get_internal_arena()); ?>
					</dd>
<?php } ?>
<?php if ($activity->get_arena()) { ?>
					<dt>
						<label for="arena"><?php echo lang('arena') ?></label>
					</dt>
					<dd>
						<?php echo activitycalendar_soarena::get_instance()->get_arena_name($activity->get_arena()); ?>
					</dd>
<?php } ?>
				<dt>
					<label for="district"><?php echo lang('district') ?></label>
				</dt>
				<dd>
					<?php
					if ($activity->get_district()) {
						$current_district_ids = $activity->get_district();
						$current_district_id_array = explode(",", $current_district_ids);
						foreach ($current_district_id_array as $curr_district) {
							echo $act_so->get_district_name($curr_district) . '<br/>';
						}
					}
					?>
				</dd>
				<dt>
					<label for="time"><?php echo lang('time') ?></label>
				</dt>
				<dd>
<?php echo $activity->get_time(); ?>
				</dd>
			</FIELDSET>
			<FIELDSET id="arr">
				<legend>Arrangør</legend>
				<dd>
					<?php echo $organization->get_name(); ?>
					<?php
					if (!$change_request) {
						if (!$activity->get_new_org()) {
							?>
							<a href="index.php?menuaction=activitycalendarfrontend.uiactivity.edit_organization_values&amp;organization_id=<?php echo $organization->get_id(); ?>" target="_blank"><?php echo lang('edit_organization'); ?></a>
						<?php
						}
					}
					?>
				</dd>
				<br/>
				<LEGEND>Kontaktperson</LEGEND>
				<dt>
<?php if ($activity->get_contact_person_1()) { ?>
					<label for="contact_person_1"><?php echo lang('contact_person') ?></label>
					<?php } ?>
				</dt>
				<dd>
					<label for="contact1_name">Navn</label>
					<?php echo isset($contact1) ? $contact1->get_name() : '' ?><br/>
					<label for="contact1_phone">Telefon</label>
<?php echo isset($contact1) ? $contact1->get_phone() : '' ?><br/>
					<label for="contact1_mail">E-post</label>
<?php echo isset($contact1) ? $contact1->get_email() : '' ?>
				</dd>
			</FIELDSET>
			<FIELDSET>
				<BR>
				<dt>
					<LABEL for="office">Kulturkontor</LABEL>
				</dt>
				<dd>
					<?php
					if ($activity->get_office()) {
						echo $act_so->get_office_name($activity->get_office());
					}
					?>
				</dd>
			</FIELDSET>
			<br/><br/>
			<div class="form-buttons">
				<?php if ($change_request) { ?>
					<input type="submit" name="activity_ok" value="<?php echo lang('activity_ok') ?>" />
					<input type="submit" name="change_request" value="<?php echo lang('change_activity') ?>" />
<?php }
?>
			</div>
		</dl>
	</form>
</div>