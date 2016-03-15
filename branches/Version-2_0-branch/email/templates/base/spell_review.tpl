<!-- begin spell_review.tpl -->

<style type="text/css">
<!--
	P.spell_review
	{
		/* display: inline;*/
		/* font-size: 1.2em;*/
		vertical-align: middle;
		/* line-height: 1.8em;*/
		/* padding: 1.8em;*/
		/* margin: 1.8em;*/
	}
-->
</style>

<!-- BEGIN B_before_echo -->
<table border="0" cellpadding="1" cellspacing="1" width="95%" align="center">
{form_open_tags}
<tr>
	<td width="100%">
		<h2>{page_desc}</h2>
		Review spell check and choose replacements if desired.
	</td>
</tr>
<tr>
	<td width="100%">
		&nbsp;<br />&nbsp;<br />
		<hr size="1">
		&nbsp;<br />&nbsp;<br />
	</td>
</tr>
<tr>
	<td width="100%">
		<p class="spell_review">
		{body_with_suggestions}
		</p>
	</td>
</tr>
<tr>
	<td width="100%">
		&nbsp;<br />&nbsp;<br />
		<hr size="1">
		&nbsp;<br />&nbsp;<br />
	</td>
</tr>
<tr>
	<td width="100%" align="center">
		{btn_apply}&nbsp; &nbsp;{btn_cancel}
	</td>
</tr>
</form>
</table>

<!-- end spell_review.tpl -->
