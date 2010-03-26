<xsl:template match="contract_data" xmlns:php="http://php.net/xsl">
    <div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
        	<div id="contract_selector" style="float:left">
		       <form action="index.php?menuaction=frontend.uicontract.index" method="post">
		           <label><img src="frontend/templates/base/images/32x32/page_white_stack.png" class="list_image"/>
		           <em class="select_header">Velg kontrakt</em></label><br/>
		           <select name="contract_id" size="5" onchange="this.form.submit();" style="margin:5px;">
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
     	 			<div id="contract_essentials">
						<ul>
							<li><em><img src="frontend/templates/base/images/16x16/house.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'old_contract_id')"/>: <xsl:value-of select="old_contract_id"/></li>
		    				<li><em><img src="frontend/templates/base/images/16x16/resultset_first.png"  class="list_image"/></em><xsl:value-of select="php:function('lang', 'date_start')"/>: <xsl:value-of select="date_start"/></li>
		    				<li><em><img src="frontend/templates/base/images/16x16/resultset_last.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'date_end')"/>: 
		    					<xsl:choose>
									<xsl:when test="date_end != ''">
										<xsl:value-of select="date_end"/>
									</xsl:when>
									<xsl:otherwise >
										<xsl:value-of select="php:function('lang', 'no_end_date')"/>
									</xsl:otherwise>
								</xsl:choose>
							</li>
	    				</ul>
	    			</div>
					<div id="contract_price_and_area">
						<ul>
							<li><em><img src="frontend/templates/base/images/16x16/shading.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'rented_area')"/>: <xsl:value-of select="rented_area"/></li>	
							<li><em><img src="frontend/templates/base/images/16x16/coins.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'total_price')"/>: <xsl:value-of select="total_price"/></li>	
							<li><em><img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'service_id')"/>: <xsl:value-of select="service_id"/></li>	
							<li><em><img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'responsibility_id')"/>: <xsl:value-of select="responsibility_id"/></li>	
						</ul>
					</div>
					<div id="contract_parts">
						<xsl:for-each select="party">
							<ul>
								<li><em></em><xsl:value-of select="php:function('lang', 'name')"/>: <xsl:value-of select="name"/></li>
								<li><xsl:value-of select="php:function('lang', 'address')"/>: <br/>
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
								</li>
							</ul>
						</xsl:for-each>
					</div>
					<div id="composites">
						<xsl:for-each select="composite">
							<ul>
								<li><em><img src="frontend/templates/base/images/16x16/house.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'name')"/>: <xsl:value-of select="name"/><br/></li>
								<li>
									<xsl:if test="normalize-space(address)">
										<xsl:value-of select="address"/>
									</xsl:if>
								</li>
							</ul>
						
						</xsl:for-each>
					</div>
				</xsl:for-each>
			</div>
        </div>
    </div>
</xsl:template>

<xsl:template match="contract">
	<xsl:copy-of select="."/>
	
</xsl:template>


