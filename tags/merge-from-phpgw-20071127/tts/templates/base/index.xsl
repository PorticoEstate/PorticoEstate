<xsl:template match="index">
	<script type="text/javascript">
		var lang = 
		{
			invalid		: '{lang_invalid}',
			ticket_no	: '{lang_ticket_no}'
		};

	</script>
	<ul class="app_buttons">
		<li onclick="window.location='{url/new_ticket}';">
			<img src="{img/new}" alt="{lang/new}" /><br />
			<a href="{url/new_ticket}"><xsl:value-of select="lang/new" /></a>
		</li>
		<li onclick="window.location='{url/search}';">
			<img src="{img/search}" alt="{lang/search}" /><br />
			<a href="{url/search}"><xsl:value-of select="lang/search" /></a>
		</li>
		<!--
		<li onclick="window.location='{url/prefs}';">
			<img src="{img/prefs}" alt="{lang/preferences}" /><br />
			<a href="{url/prefs}"><xsl:value-of select="lang/preferences" /></a>
		</li>
		-->
		<li onclick="goToPopup()">
			<img src="{img/goto}" alt="{lang/goto}" /><br />
			<a href="#" onclick="goToPopup();"><xsl:value-of select="lang/goto" /></a>
		</li>
	</ul>

	<div class="tabsholder">
		<ul class="tabs">
			<li id="tab1"><a href="#" onclick="oTabs.display(1);"><span><xsl:value-of select="lang/overdue" /></span></a></li>
			<li id="tab2"><a href="#" onclick="oTabs.display(2);"><span><xsl:value-of select="lang/open" /></span></a></li>
		</ul><br />
	</div>
	<xsl:for-each select="//overdue_tickets">
		<div id="tabcontent1">
			<xsl:call-template name="ticket_list" />
		</div>
	</xsl:for-each>
	<xsl:for-each select="//open_tickets">
		<div id="tabcontent2">
			<xsl:call-template name="ticket_list" />
		</div>
	</xsl:for-each>

	<div id="tts_goto_dialog" class="panel">
		<div class="hd"><div class="lt"></div><span><xsl:value-of select="lang/goto" /></span><div class="rt"></div></div>
		<div class="bd">
			<form method="post" action="{url/goto_action}" id="tts_goto_form" name="tts_goto_form">
				<div>
					<label for="ticket_id"><xsl:value-of select="lang/ticket_no" /></label>
					<input type="text" name="ticket_id" id="ticket_id" /><br />
				</div>
			</form>
		</div>
	</div>
</xsl:template>
