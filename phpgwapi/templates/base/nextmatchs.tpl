<!-- BEGIN nextmatchs -->
 <table width="{table_width}" border="0" bgcolor="{th_bg}" cellspacing="0" cellpadding="0" cols="5">
  <tr>   {left}
   <td align="center" bgcolor="{th_bg}" valign="top" width="92%">{cats_search_filter_data}</td>   {right}
  </tr>
 </table>

<br>

<!-- END nextmatchs -->


<!-- BEGIN nm_filter -->
       <td>
{select}
        <noscript>
         <input type="submit" value="{lang_filter}">
        </noscript>
       </td>
<!-- END nm_filter -->


<!-- BEGIN nm_form -->
<td width="2%" align="{align}" valign="top">
	<form method="POST" action="{action}" name="{form_name}">
	{hidden}
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="{align}">
			<input type="image" src="{img}" alt="{label}" name="start" value="{start}">
		</td>
	</tr>
	</table>
</form>
</td>
<!-- END nm_form -->


<!-- BEGIN nm_icon -->
<td width="2%" align="{align}">&nbsp;{_link}</td>
<!-- END nm_icon -->


<!-- BEGIN nm_link -->
<td width="2%" align="{align}" valign="top">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="{align}">
			<img src="{img}" width="12" height="12" alt="{label}">
		</td>
	</tr>
	</table>
</td>
<!-- END nm_link -->


<!-- BEGIN nm_search -->
        <input type="text" name="query" value="{query_value}">&nbsp;{searchby}<input type="submit" name="Search" value="{lang_search}">
<!-- END nm_search -->

<!-- BEGIN nm_cats -->
       <td>
        {lang_category}&nbsp;&nbsp;<select name="{cat_field}" onChange="this.form.submit();">
         <option value="0">{lang_all}</option>
         {categories}
        </select>
        <noscript><input type="submit" name="cats" value="{lang_select}"></noscript>
       </td>
<!-- END nm_cats -->

<!-- BEGIN nm_search_filter -->
    <form method="POST" action="{form_action}" name="filter">
     <table border="0" bgcolor="{th_bg}" cellspacing="0" cellpadding="0">
      <input type="hidden" name="filter" value="{filter_value}">
      <input type="hidden" name="qfield" value="{qfield_value}">
      <input type="hidden" name="start" value="{start_value}">
      <input type="hidden" name="order" value="{order_value}">
      <input type="hidden" name="sort" value="{sort_value}">
      <input type="hidden" name="query" value="{query_value}">
      <tr>
		<td>{search}</td>
		<td>&nbsp;</td>
		{filter}
      </tr>
     </table>
    </form>
<!-- END nm_search_filter -->

<!-- BEGIN nm_cats_search_filter -->
    <form method="POST" action="{form_action}" name="filter">
     <table border="0" bgcolor="{th_bg}" cellspacing="0" cellpadding="0">
      <input type="hidden" name="filter" value="{filter_value}">
      <input type="hidden" name="qfield" value="{qfield_value}">
      <input type="hidden" name="start" value="{start_value}">
      <input type="hidden" name="order" value="{order_value}">
      <input type="hidden" name="sort" value="{sort_value}">
      <input type="hidden" name="query" value="{query_value}">
      <tr>
		{cats}
		<td>&nbsp;</td>
		<td>{search}</td>
		<td>&nbsp;&nbsp;</td>
		{filter}
      </tr>
     </table>
    </form>
<!-- END nm_cats_search_filter -->
