<!-- BEGIN header -->
<form method="POST" action="{action_url}">
	<table class="pure-table pure-table-bordered pure-table-striped pure-form">
		<tr class="th">
			<td colspan="2"><font color="{th_text}">&nbsp;<b>{title}</b></font></td>
		</tr>
		<!-- END header -->
		<!-- BEGIN body -->
		<tbody>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;<b>{lang_sms}</b></td>
			</tr>
			<tr>
				<td>{lang_receipt_on_code_miss}:</td>
				<td>
					<textarea cols="60" rows="10" name="newsettings[receipt_on_code_miss]" wrap="virtual">{value_receipt_on_code_miss}</textarea>
				</td>
			</tr>
		</tbody>
		<!-- END body -->

		<!-- BEGIN footer -->
		<tfoot>
			<tr class="th">
				<td colspan="2">
					&nbsp;
				</td>
			</tr>

			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submit" value="{lang_submit}" class="pure-button"/>
					<input type="submit" name="cancel" value="{lang_cancel}" class="pure-button"/>
				</td>
			</tr>
		</tfoot>
	</table>
</form>
<!-- END footer -->
