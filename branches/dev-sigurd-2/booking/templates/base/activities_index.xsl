
<xsl:template match="data">
<div style="padding: 2em;">
            <a class="add" style="text-decoration: none;font-size: 14px;">
                <xsl:attribute name="href"><xsl:value-of select="navi/add"/></xsl:attribute>
                <xsl:value-of select="lang/add" />
            </a>
</div>
<div style='padding: 2em;'>

<h3>Current Activities</h3>

<script type="text/javascript">
YAHOO.util.Event.addListener(window, "load", function() {
	var tree = new YAHOO.widget.TreeView("treeDiv1", <xsl:value-of select="treedata"/>); 
	tree.render(); 

});
</script>
	<div id="treeDiv1"></div>
</div>
</xsl:template>
