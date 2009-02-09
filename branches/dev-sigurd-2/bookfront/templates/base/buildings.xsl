<xsl:template match="data">
    <xsl:variable name="add_link" select="add_link"></xsl:variable>
    <ul id="toolbar">
        <li><a href="{$add_link}">Add building</a></li>
    </ul>
    
    <h3>Buildings</h3>
    
    <div id="datatable-container"></div>

</xsl:template>
