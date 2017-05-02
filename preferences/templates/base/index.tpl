<!-- BEGIN list -->
<div id="preferences-index" class="content">
	<div id="tab-content">
	{tabs}
		<div id="user">
	{rows}
		</div>
		<div id="default">
				{rows}
		</div>
		<div id="forced">
				{rows}
		</div>

	</div>
</div>
<!-- END list -->

<!-- BEGIN app_row -->
	<h2><a name="{a_name}"></a><img src="{app_icon}" alt="{app_name}"> {app_name}</h2>
	<ul>
<!-- END app_row -->

<!-- BEGIN app_row_noicon -->
	<h2><a name="{a_name}"></a> {app_name}</h2>
	<ul>
<!-- END app_row_noicon -->

<!-- BEGIN link_row -->
		<li class="{pref_class}"><a href="{pref_link}">{pref_text}</a></li>
<!-- END link_row -->

<!-- BEGIN spacer_row -->
	</ul>
<!-- END spacer_row -->
