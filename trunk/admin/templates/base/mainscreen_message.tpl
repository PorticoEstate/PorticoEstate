<!-- BEGIN form -->
<h1>{header_lang}</h1>
<p class="center">{error_message}</p>
<form method="post" action="{form_action}">
  	<table align="center">
    	<input type="hidden" name="select_lang" value="{select_lang}" />
    	<input type="hidden" name="section" value="{section}" />
    	 {rows}
  	</table>
</form>
<!-- END form -->

<!-- BEGIN row -->
    <tr class="{tr_class}">
      <td class="center">{label}</td>
      <td class="center">{value}</td>
    </tr>
<!-- END row -->

<!-- BEGIN row_2 -->
    <tr><td colspan="2" class="center">{value}</td></tr>
<!-- END row_2 -->
