<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
	    <h3><xsl:value-of select="php:function('lang', 'Active applications')" /></h3>
    
	    <div id="apps_container"/>

	</div>
	<script type="text/javascript">
		    <![CDATA[
		YAHOO.util.Event.addListener(window, "load", function() {
		    var url = 'index.php?menuaction=booking.uiapplication.index&phpgw_return_as=json&sort=modified&';
		    var colDefs = [{key: 'id', label: 'ID', formatter: YAHOO.booking.formatLink, sortable: true},
						   {key: 'status', label: 'Status', sortable: true},
						   {key: 'modified', label: 'Modified', sortable: true},
						   {key: 'created', label: 'Created', sortable: true}
						];
		    YAHOO.booking.inlineTableHelper('apps_container', url, colDefs);
		});
		]]>
	</script>
</xsl:template>
