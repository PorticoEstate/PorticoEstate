<xsl:template match="contracts" xmlns:php="http://php.net/xsl">
    <xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
    <div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
            <div class="toolbar-container">
                <div class="toolbar">
                    <xsl:apply-templates select="datatable/actions" />
                </div>
            </div>
        </div>
    </div>
	<div id="contract_selector">
        <form action="index.php?menuaction=frontend.uicontract.index" method="post">
            <label>Velg kontrakt</label>
            <br/>
            <select name="contract_id" size="7" onchange="this.form.submit();">
            	
            	<xsl:for-each select="child::*">
            		<xsl:choose>
            			<xsl:when test="id = //selected_contract">
            				<option value="{id}" selected="selected"><xsl:value-of select="old_contract_id"/></option>
            			</xsl:when>
            			<xsl:otherwise>
            				<option value="{id}"><xsl:value-of select="old_contract_id"/></option>
            			</xsl:otherwise>
            		</xsl:choose>
            	</xsl:for-each>
            	
            </select>
        </form>
    </div>
</xsl:template>

<xsl:template match="contract" xmlns:php="http://php.net/xsl">
	<xsl:value-of select="date_start"/>
</xsl:template>	

