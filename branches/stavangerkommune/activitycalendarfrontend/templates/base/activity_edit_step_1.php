<?php ?>
<script type="text/javascript">
	function isOK()
	{
		if(document.getElementById('activity_id').value == null || document.getElementById('activity_id').value == '' || document.getElementById('activity_id').value == 0)
		{
			alert("Du må velge en aktivitet som skal endres!");
			return false;
		}
		else
		{
			return true;
		}
	}
	function get_activities()
	{
		var org_id = document.getElementById('organization_id').value;
		var div_select = document.getElementById('activity_select');

		url = "<?php echo $ajaxURL ?>index.php?menuaction=activitycalendarfrontend.uiactivity.get_organization_activities&amp;phpgw_return_as=json&amp;orgid=" + org_id;

		var divcontent_start = "<select name=\"activity_id\" id=\"activity_id\">";
		var divcontent_end = "</select>";
	
		var callback = {
			success: function(response){
				div_select.innerHTML = divcontent_start + JSON.parse(response.responseText) + divcontent_end; 
			},
			failure: function(o) {
				alert("AJAX doesn't work"); //FAILURE
			}
		}
		var trans = YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
	
	}

	YAHOO.util.Event.onDOMReady(function()
	{
		get_activities();
	});
</script>

<div class="yui-content" style="width: 100%;">
	<div class="pageTop">
		<h1><?php echo lang('edit_activity'); ?></h1>
		<form action="#" method="post">
			<dl class="proplist-col" style="width: 200%">
				<dt>
				<?php if ($message) { ?>
					<?php echo $message; ?>
				<?php } else { ?>
					<?php echo lang('activity_edit_helptext_step1') ?><br/><br/>
				<?php } ?>
				</dt>
				<?php if (!$message) { ?>
					<dd>
						<select name="organization_id" id="organization_id" onchange="javascript: get_activities();">
							<option value="">Ingen organisasjon valgt</option>
							<?php
							foreach ($organizations as $organization) {
								echo "<option value=\"{$organization->get_id()}\">" . $organization->get_name() . "</option>";
							}
							?>
						</select>
					</dd>
					<dt>
						&nbsp;
					</dt>
					<dd>
						<div id="activity_select">
							<select name="activity_id" id="activity_id">
								<option value="0">Ingen aktivitet valgt</option>
							</select>
						</div>
						<br/><br/>
					</dd>
					<div class="form-buttons">
						<input type="submit" name="step_1" value="<?php echo lang('send_change_request') ?>" onclick="return isOK();"/>
					</div>
				<?php } ?>
			</dl>

		</form>
	</div>
</div>