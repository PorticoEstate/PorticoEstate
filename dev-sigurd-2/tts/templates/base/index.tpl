<!-- BEGIN index.tpl -->
<script type="text/javascript">
	//<![CDATA[
		var lang = 
		{
			invalid		: '{lang_invalid}',
			ticket_no	: '{lang_ticket_no}'
		};
	//]]>
</script>
<!-- BEGIN tts_search -->
<ul class="app_buttons">
	<li onclick="window.location='{url_new_ticket}';">
		<img src="{img_new}" alt="{lang_new}" /><br />
		<a href="{url_new_ticket}">{lang_new}</a>
	</li>
	<li onclick="window.location='{url_search}';">
		<img src="{img_search}" alt="{lang_search}" /><br />
		<a href="{url_search}">{lang_search}</a>
	</li>		
	<li onclick="window.location='{url_prefs}';">
		<img src="{img_prefs}" alt="{lang_preferences"}" /><br />
		<a href="{url_prefs}">{lang_preferences}</a>
	</li>
	<li onclick="goToPopup()">
		<img src="{img_goto}" alt="{lang_goto"}" /><br />
		<a href="#" onclick="goToPopup();">{lang_goto}</a>
	</li>
</ul>
<!-- END tts_search -->

<div class="tabsholder">
	<ul class="tabs">
		<li id="tab1"><a href="#" onclick="oTabs.display(1);"><span>{lang_overdue}</span></a></li>
		<li id="tab2"><a href="#" onclick="oTabs.display(2);"><span>{lang_open}</span></a></li>
	</ul><br />
</div>
<div id="tabcontent1">
	{overdue_list}
</div>
<div id="tabcontent2">
	{open_list}
</div>

<div id="tts_goto_dialog" class="panel">
	<div class="hd"><div class="lt"></div><span>{lang_goto}</span><div class="rt"></div></div>
	<div class="bd">
		<form method="post" action="{goto_action}" id="tts_goto_form" name="tts_goto_form">
			<div> <!-- STFU validator -->
				<label for="ticket_id">{lang_ticket_no}</label>
				<input type="text" name="ticket_id" id="ticket_id"><br>
			</div>
		</form>
	</div>
</div>
<!-- END index.tpl -->
