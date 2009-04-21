
<xsl:template match="data">
<div style="padding: 2em;">
            <a class="add" style="text-decoration: none;font-size: 14px;">
                <xsl:attribute name="href"><xsl:value-of select="navi/add"/></xsl:attribute>
                <xsl:value-of select="lang/add" />
            </a>
</div>
<div style="padding: 0 2em">

<h3>Current Activities</h3>

<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
	var tree = new YAHOO.widget.TreeView("tree_container", <xsl:value-of select="treedata"/>); 
	tree.subscribe("labelClick", function(node) {
		window.location.href = node.href;
	});
	tree.render(); 
});
</script>
	<div id="tree_container"></div>
</div>
</xsl:template>
