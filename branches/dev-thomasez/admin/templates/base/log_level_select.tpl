<!-- BEGIN log_level_select -->

   <form action="{select_link}" method="post" name="{select_name}_form">
		<input type="hidden" name="level_type" value="{level_type}" />
		<input type="hidden" name="level_key" value="{level_key}" />
		<select name="{select_name}" size="1"  onchange="document.{select_name}_form.submit()">
			<option value="F" {F_selected}>{lang_fatal}</option>
			<option value="E" {E_selected}>{lang_error}</option>
			<option value="W" {W_selected}>{lang_warn}</option>
			<option value="N" {N_selected}>{lang_notice}</option>
			<option value="I" {I_selected}>{lang_info}</option>
			<option value="D" {D_selected}>{lang_debug}</option>
			<option value="S" {S_selected}>{lang_strict}</option>
			<option value="DP" {DP_selected}>{lang_deprecated}</option>
		</select>
		<noscript><input type="submit"></noscript>
   </form>
   
<!-- END log_level_select -->
