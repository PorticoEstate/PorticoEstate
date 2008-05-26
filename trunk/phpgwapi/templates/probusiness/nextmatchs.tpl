<!-- BEGIN nextmatchs -->
<table>
  <tr>
    {left}
    <td width="90%"><font>{cats_search_filter_data}</font></td>
    {right}
  </tr>
</table>
<br />
<!-- END nextmatchs -->

<!-- BEGIN filter -->
    <td>
      {select}
      <noscript>
        <input type="submit" value="{lang_filter}" />
      </noscript>
    </td>
<!-- END filter -->

<!-- BEGIN form -->
<form method="post" action="{action}" name="{form_name}">
	<td width="2%" align="{align}" valign="top">
		{hidden}
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="{align}"><input type="image" src="{img}" alt="{label}" name="start" value="{start}"></td>
			</tr>
		</table>
	</td>
</form>
<!-- END form -->

<!-- BEGIN icon -->
    <td width="2%" align="{align}">&nbsp;{_link}</td>
<!-- END icon -->

<!-- BEGIN link -->
    <td width="2%" align="{align}" valign="top">
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
        	<td align="{align}">
        		<img src="{img}" alt="{label}" />
        	</td>
        </tr>
      </table>
    </td>
<!-- END link -->

<!-- BEGIN search -->
    <input class="text" type="text" name="query" value="{query_value}" />&nbsp;{searchby}
    <input class="button" type="submit" name="Search" value="{lang_search}" />
<!-- END search -->

<!-- BEGIN cats -->
      <td>
        <font>{lang_category}&nbsp;&nbsp;</font>
        <select name="{cat_field}" onChange="this.form.submit();">
          <option value="0">{lang_all}</option>
          {categories}
        </select>
        <noscript>
          <input type="submit" name="cats" value="{lang_select}" />
        </noscript>
      </td>
<!-- END cats -->

<!-- BEGIN search_filter -->
<form method="post" action="{form_action}" name="filter">
  <input type="hidden" name="filter" value="{filter_value}" />
  <input type="hidden" name="qfield" value="{qfield_value}" />
  <input type="hidden" name="start" value="{start_value}" />
  <input type="hidden" name="order" value="{order_value}" />
  <input type="hidden" name="sort" value="{sort_value}" />
  <input type="hidden" name="query" value="{query_value}" />
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>{search}</td>
      <td>&nbsp;</td>
      {filter}
    </tr>
  </table>
</form>
<!-- END search_filter -->

<!-- BEGIN cats_search_filter -->
<form method="post" action="{form_action}" name="filter">
  <input type="hidden" name="filter" value="{filter_value}" />
  <input type="hidden" name="qfield" value="{qfield_value}" />
  <input type="hidden" name="start" value="0" />
  <input type="hidden" name="order" value="{order_value}" />
  <input type="hidden" name="sort" value="{sort_value}" />
  <input type="hidden" name="query" value="{query_value}" />
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      {cats}
      <td>&nbsp;</td>
      <td>{search}</td>
      <td>&nbsp;&nbsp;</td>
      {filter}
    </tr>
  </table>
</form>
<!-- END cats_search_filter -->
