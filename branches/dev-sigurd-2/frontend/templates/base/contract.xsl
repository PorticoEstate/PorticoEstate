<xsl:template match="contract_data" xmlns:php="http://php.net/xsl">
    <div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
        	<div class="toolbar" style="display: block; padding-bottom: 1em;">
            	<div id="contract_selector">
			       <form action="index.php?menuaction=frontend.uicontract.index" method="post">
			           <img src="frontend/templates/base/images/16x16/page_white_stack.png" class="list_image"/>
			           <xsl:value-of select="php:function('lang', 'choose_contract')"/>: 
			           	<xsl:for-each select="select">
			           		<xsl:choose>
				           		<xsl:when test="id = //selected_contract">
			           				<input name="contract_id" type="radio" value="{id}" checked="" onchange="this.form.submit();"></input> 
			           			</xsl:when>
			           			<xsl:otherwise>
			           				<input name="contract_id" type="radio" value="{id}" onchange="this.form.submit();"></input>
			           			</xsl:otherwise>
			           		</xsl:choose>
			           		<label style="margin-right: 1em; padding-left: 5px;"> <xsl:value-of select="old_contract_id"/> (<xsl:value-of select="contract_status"/>)</label>
			           	</xsl:for-each>
			        </form>
	 			</div>
	 		</div>
	 		<div>
	 			<div id="contract_details">
	     	 		<xsl:for-each select="contract">
	     	 			<xsl:copy-of select="."/>
	     	 			<div id="contract_essentials">
							<ul>
								<li><em><img src="frontend/templates/base/images/16x16/page_white.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'old_contract_id')"/>: <xsl:value-of select="old_contract_id"/></li>
			    				<li><em><img src="frontend/templates/base/images/16x16/timeline_marker.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'contract_status')"/>: <xsl:value-of select="contract_status"/></li>
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
						<div id="contract_price_and_area" style="block:right;">
							<ul>
								<li><em><img src="frontend/templates/base/images/16x16/shading.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'rented_area')"/>: <xsl:value-of select="rented_area"/></li>	
								<li><em><img src="frontend/templates/base/images/16x16/coins.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'total_price')"/>: <xsl:value-of select="total_price"/></li>	
								<li><em><img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'service_id')"/>: <xsl:value-of select="service_id"/></li>	
								<li><em><img src="frontend/templates/base/images/16x16/page_white_edit.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'responsibility_id')"/>: <xsl:value-of select="responsibility_id"/></li>	
							</ul>
						</div>
						
						<div id="contract_parts">
							<ul>
							<li style="border-style: none none solid none; border-width: 1px; border-color: grey; margin-bottom: 5px; padding-bottom: 5px;" >
								<img src="frontend/templates/base/images/16x16/group.png" class="list_image" />
								<em>Kontraktsparter:</em>
							</li>
							<xsl:for-each select="../party">
									<li><em><img src="frontend/templates/base/images/16x16/user_gray.png" class="list_image" /></em><xsl:value-of select="name"/><br/>
									<xsl:value-of select="php:function('lang', 'address')"/>: <br/>
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
							</xsl:for-each>
							</ul>
						</div>
						<div id="composites">
							<ul>
							<li style="border-style: none none solid none; border-width: 1px; border-color: grey; margin-bottom: 5px; padding-bottom: 5px;" >
								<img src="frontend/templates/base/images/16x16/layers.png" class="list_image" />
								<em>Leieobjekt:</em>
							</li>
							<xsl:for-each select="../composite">
									<li style="margin-top: 1em;"><em class="bold"><img src="frontend/templates/base/images/16x16/layers.png" class="list_image" /></em> <xsl:value-of select="name" />:</li>
									<li style="margin-bottom: 1em;">
										<dl>
											<dt style="float: left;"><img src="frontend/templates/base/images/16x16/house.png" class="list_image" /></dt>
											<dd>
												<xsl:if test="normalize-space(address)">
													<xsl:value-of select="address" disable-output-escaping="yes"/>
												</xsl:if>
											</dd>
										</dl>
									</li>
							</xsl:for-each>
							</ul>
						</div>
						<div id="comment">
							<xsl:choose>
								<xsl:when test="publish_comment">
									<xsl:value-of select="comment" disable-output-escaping="yes"/>
								</xsl:when>
							</xsl:choose>
						</div>
					</xsl:for-each>
				</div>
        	</div>
    	</div>
    </div>
</xsl:template>

<xsl:template match="contract">
	<xsl:copy-of select="."/>
	
</xsl:template>


