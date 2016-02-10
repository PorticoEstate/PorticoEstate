<!-- BEGIN TABHOLDER -->
<script language="JavaScript" type="text/javascript">
				function changetab(selectedtype)
				{
					document.body_form.bname.value = selectedtype;
					document.body_form.submit();
				}
				function submit_form(selectedtype)
				{
					process_list('{onsubjs1}', '{onsubjs2}');
					document.body_form._submit.value = selectedtype;
					document.body_form.submit();
				}
</script>

<form action="{action}" method="post" name="body_form" cellspacing="0" cellpadding="0">
	<div id="tab-content">
		{principal_tabs_inc}
		<input type="hidden" name="bname" />
		<input type="hidden" name="_submit" />
	</div>
	<div id="tab-content2">
				{tab}
				<input type="hidden" name="{old_tab_name}" value="{old_tab}">
				<input type="hidden" name="referer" value="{referer}">
				<input type="hidden" name="ab_id" value="{ab_id}">
				<input type="hidden" name="owner" value="{owner}">
				<input type="hidden" name="record_name" value="{record_name}">
				{current_tab_body}
			{control_buttons}
	</div>
</form>
<!-- END TABHOLDER -->
