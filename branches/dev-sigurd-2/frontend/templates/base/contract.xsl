<xsl:template match="contract_data" xmlns:php="http://php.net/xsl">
    <div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
        	<div id="contract_selector">
		       <form action="index.php?menuaction=frontend.uicontract.index" method="post">
		           <label>Velg kontrakt</label><br/>
		           <select name="contract_id" size="5" onchange="this.form.submit();">
		           	<xsl:for-each select="select">
		           		<xsl:choose>
		           			<xsl:when test="id = //selected_contract">
		           				<option value="{id}" selected="selected"><xsl:value-of select="old_contract_id"/> - <xsl:value-of select="contract_status"/></option>
		           			</xsl:when>
		           			<xsl:otherwise>
		           				<option value="{id}"><xsl:value-of select="old_contract_id"/> - <xsl:value-of select="contract_status"/></option>
		           			</xsl:otherwise>
		           		</xsl:choose>
		           	</xsl:for-each>
		           </select>
		       </form>
 			</div>
 			<div id="contract_details">
     	 		<xsl:for-each select="contract">
					<dl class="proplist-col">
						<dt><xsl:value-of select="php:function('lang', 'old_contract_id')"/></dt>
						<dd><xsl:value-of select="old_contract_id"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'date_start')"/></dt>
						<dd><xsl:value-of select="date_start"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'date_end')"/></dt>
						<dd><xsl:value-of select="date_end"/></dd>
					</dl>
					<dl class="proplist-col">
						<dt><xsl:value-of select="php:function('lang', 'old_contract_id')"/></dt>
						<dd><xsl:value-of select="old_contract_id"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'date_start')"/></dt>
						<dd><xsl:value-of select="date_start"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'date_end')"/></dt>
						<dd><xsl:value-of select="date_end"/></dd>
					</dl>
					<dl class="proplist-col">
						<dt><xsl:value-of select="php:function('lang', 'old_contract_id')"/></dt>
						<dd><xsl:value-of select="old_contract_id"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'date_start')"/></dt>
						<dd><xsl:value-of select="date_start"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'date_end')"/></dt>
						<dd><xsl:value-of select="date_end"/></dd>
					</dl>	
				</xsl:for-each>
			</div>
        </div>
    </div>
</xsl:template>

<xsl:template match="contract">
	<xsl:copy-of select="."/>
	
</xsl:template>


