<div style="margin-bottom:1cm;font-weight:bold;text-align:center;text-decoration:underline">{translate} {pagename}</div>
<div style="text-align:center; color:#FF0000; font-weight:bold;"><b>{error_msg}</b></div>
<form name="translatepage" method="POST">
<input type="hidden" name="page_id" value="{pageid}">

<table style="border-width:2px;border-style:solid;" align="center" border="1" rules="all" width="80%" cellpadding="5">
	<tr>
		<td width="20%">{lang_refresh}</td><td width="40%">{showlang}</td><td width="40%">{savelang}</td>
	</tr>
	<tr>
		<td>{lang_pagetitle}:</td>
		<td>{showpagetitle}</td>
		<td><input type="text" size="50" name="savepagetitle" value="{savepagetitle}"></td>
	</tr>
	<tr>
		<td>{lang_pagesubtitle}:</td>
		<td>{showpagesubtitle}</td>
		<td><input type="text" size="50" name="savepagesubtitle" value="{savepagesubtitle}"></td>
	</tr>
	<tr>
		<td colspan="3" align="center"><input type="reset" name="reset" value="{lang_reset}"><input type="submit" name="btnSavePage" value="{lang_save}"></td>
	</tr>
</table>
</form>
<h4 style="text-align:center">Content blocks for page</h4>
{blocks}