<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	<div id="content">
	    <h3><xsl:value-of select="php:function('lang', 'Active applications')" /></h3>
    
	    <div id="apps_container"/>

	</div>
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'ID', 'Type', 'Status', 'Modified', 'Created')"/>;
		    <![CDATA[
		YAHOO.util.Event.addListener(window, "load", function() {
		    var url = 'index.php?menuaction=booking.uiapplication.index&phpgw_return_as=json&sort=modified&';
		    var colDefs = [{key: 'id', label: lang['ID'], formatter: YAHOO.booking.formatLink, sortable: true},
						   {key: 'type', label: lang['Type'], sortable: true},
						   {key: 'status', label: lang['Status'], sortable: true},
						   {key: 'modified', label: lang['Modified'], sortable: true},
						   {key: 'created', label: lang['Created'], sortable: true}
						];
		    YAHOO.booking.inlineTableHelper('apps_container', url, colDefs);
		});
		]]>
	</script>
</xsl:template>
