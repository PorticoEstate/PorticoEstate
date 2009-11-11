<xsl:template match="data" xmlns:php="http://php.net/xsl">
	
<xsl:call-template name="yui_booking_i18n"/>



<form id="queryForm" method="GET" action="">
	<div id="toolbar">
		<table class="yui-skin-sam" border="0" cellspacing="0" cellpadding="0" style="padding:0px; margin:0px;">
			<tr>
				<xsl:if test="links/add">
					<td valign="top"><input id="new-button" type="link" value="{php:function('lang', 'Add Activity')}" href="{links/add}"/></td>
				</xsl:if>
				<xsl:if test="not(show_all='1')">
					<td valign="top"><input id="show-hide" type="link" value="{php:function('lang', 'Show all')}" href="{links/show_inactive}"/></td>
				</xsl:if>
				<xsl:if test="show_all='1'">
					<td valign="top"><input id="show-hide" type="link" value="{php:function('lang', 'Show only active')}" href="{links/hide_inactive}"/></td>
				</xsl:if>
			</tr>
		</table>
	</div>
</form>

<div style="padding: 0 2em">

<h3><xsl:value-of select="php:function('lang', 'Current Activities')" /></h3>

<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
	var newButton = YAHOO.util.Dom.get('new-button');
	if(newButton)
		new YAHOO.widget.Button(newButton, 
		                        {type: 'link', 
		                         href: newButton.getAttribute('href')});
	var showHideButton = YAHOO.util.Dom.get('show-hide');
	new YAHOO.widget.Button(showHideButton, 
	                        {type: 'link', 
	                         href: showHideButton.getAttribute('href')});



	var tree = new YAHOO.widget.TreeView("tree_container", <xsl:value-of select="treedata"/>); 
<xsl:if test="navi/add">
	tree.subscribe("labelClick", function(node) {
		window.location.href = node.href;
	});
</xsl:if>
	tree.render(); 
});
</script>
	<div id="tree_container"></div>
</div>
</xsl:template>
