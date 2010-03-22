<xsl:template match="contract_data" xmlns:php="http://php.net/xsl">
    <div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
        	<div id="contract_selector" style="float:left">
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
 			<div id="contract_details" style="float:right">
     	 		<xsl:for-each select="contract">
					<dl class="proplist-col">
						<xsl:if test="old_contract_id">
							<dt><xsl:value-of select="php:function('lang', 'old_contract_id')"/></dt>
							<dd><xsl:value-of select="old_contract_id"/></dd>
						</xsl:if>
						<dt><xsl:value-of select="php:function('lang', 'date_start')"/></dt>
						<dd><xsl:value-of select="date_start"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'date_end')"/></dt>
						<dd>
							<xsl:choose>
								<xsl:when test="date_end != ''">
									<xsl:value-of select="date_end"/>
								</xsl:when>
								<xsl:otherwise >
									<xsl:value-of select="php:function('lang', 'no_end_date')"/>
								</xsl:otherwise>
							</xsl:choose>
							
						</dd>
						<dt><xsl:value-of select="php:function('lang', 'service_id')"/></dt>
						<dd><xsl:value-of select="service_id"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'responsibility_id')"/></dt>
						<dd><xsl:value-of select="responsibility_id"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'total_price')"/></dt>
						<dd><xsl:value-of select="total_price"/> kroner</dd>
						<dt><xsl:value-of select="php:function('lang', 'rented_area')"/></dt>
						<dd><xsl:value-of select="rented_area"/> kvm</dd>
						<xsl:if test="adjustment_year != 0">
							<dt><xsl:value-of select="php:function('lang', 'adjustment_year')"/></dt>
							<dd><xsl:value-of select="adjustment_year"/></dd>
						</xsl:if>
					</dl>
				</xsl:for-each>
				<dl class="proplist-col">
					<dt>Kontraktsparter</dt>
					<dd></dd>
					<xsl:for-each select="party">
						<dt><xsl:value-of select="php:function('lang', 'name')"/></dt>
						<dd><xsl:value-of select="name"/></dd>
						<dt><xsl:value-of select="php:function('lang', 'address')"/></dt>
						<dd>
							<xsl:choose>
								<xsl:when test="normalize-space(address)">
									<xsl:value-of select="address"/>
								</xsl:when>
								<xsl:when test="normalize-space(address1)">
									<xsl:value-of select="address1"/><br/>
									<xsl:value-of select="address2"/><br/>
									<xsl:value-of select="postal_code"/>&nbsp;
									<xsl:value-of select="place"/><br/>
								</xsl:when>
								<xsl:otherwise>
									No address
								</xsl:otherwise>
							</xsl:choose>
						</dd>
					</xsl:for-each>
				</dl>
				<dl class="proplist-col">
					<dt>Leieobjekt</dt>
					<dd></dd>
					<xsl:for-each select="composite">
						<dt><xsl:value-of select="php:function('lang', 'name')"/></dt>
						<dd>
							<xsl:value-of select="name"/><br/>
							<xsl:if test="normalize-space(address)">
								<xsl:value-of select="address"/>
							</xsl:if>
						</dd>
					</xsl:for-each>
				</dl>
			</div>
        </div>
    </div>
</xsl:template>

<xsl:template match="contract">
	<xsl:copy-of select="."/>
	
</xsl:template>


