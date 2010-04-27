<xsl:template match="contract_data" xmlns:php="http://php.net/xsl">
	<xsl:copy-of select="."/>
    <div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
        <div class="yui-content">
        	<div class="toolbar" style="display: block; padding-bottom: 1em;">
            	<div id="contract_selector">
			           <img src="frontend/templates/base/images/16x16/page_white_stack.png" class="list_image"/>
			            <xsl:choose>
			           		<xsl:when test="not(normalize-space(select)) and (count(select) &lt;= 1)">
			           			 <xsl:value-of select="php:function('lang', 'no_contracts')"/>
			           		</xsl:when>
			           		<xsl:otherwise>
				           		<form action="{form_url}" method="post" style="float:left;">
				           			<select name="contract_filter" onchange="this.form.submit()">
				           				<xsl:choose>
				           					<xsl:when test="//contract_filter = 'active'">
				           						<option value="active" selected="selected"><xsl:value-of select="php:function('lang', 'active')"/></option>
				           					</xsl:when>
				           					<xsl:otherwise>
				           						<option value="active"><xsl:value-of select="php:function('lang', 'active')"/></option>
				           					</xsl:otherwise>
				           				</xsl:choose>
				           				<xsl:choose>
				           					<xsl:when test="//contract_filter = 'not_active'">
				           						<option value="not_active" selected="selected"><xsl:value-of select="php:function('lang', 'not_active')"/></option>
				           					</xsl:when>
				           					<xsl:otherwise>
				           						<option value="not_active"><xsl:value-of select="php:function('lang', 'not_active')"/></option>
				           					</xsl:otherwise>
				           				</xsl:choose>
				           				<xsl:choose>
				           					<xsl:when test="//contract_filter = 'all'">
				           						<option value="all" selected="selected"><xsl:value-of select="php:function('lang', 'all')"/></option>
				           					</xsl:when>
				           					<xsl:otherwise>
				           						<option value="all"><xsl:value-of select="php:function('lang', 'all')"/></option>
				           					</xsl:otherwise>
				           				</xsl:choose>
				           			</select>
				           		</form>
					             <form action="{form_url}" method="post" style="float: left;">
						           	<xsl:for-each select="select">
						           		<xsl:choose>
							           		<xsl:when test="id = //selected_contract">
						           				<input name="contract_id" type="radio" value="{id}" checked="" onclick="this.form.submit();" style="margin-left: 5px;"></input> 
						           			</xsl:when>
						           			<xsl:otherwise>	
						           				<input name="contract_id" type="radio" value="{id}" onclick	="this.form.submit();" style="margin-left: 5px;"></input>
						           			</xsl:otherwise>
						           		</xsl:choose>
						           		<label style="margin-right: 1em; padding-left: 5px;"> <xsl:value-of select="old_contract_id"/> (<xsl:value-of select="contract_status"/>)</label>
						           	</xsl:for-each>
					           	  </form>
					         </xsl:otherwise>
					      </xsl:choose>
	 			</div>
	 		</div>
	 		<div>
	 			<div id="contract_details">
	 				 <xsl:choose>
			           		<xsl:when test="not(normalize-space(contract))">
			           			 <!-- <xsl:value-of select="php:function('lang', 'no_contract_details')"/>:  -->
			           		</xsl:when>
			           		<xsl:otherwise>
				     	 		<xsl:for-each select="contract">
				     	 			<div id="contract_essentials">
										<ul>
											<li><em><img src="frontend/templates/base/images/16x16/page_white.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'old_contract_id')"/>: <xsl:value-of select="old_contract_id"/></li>
						    				<li><em><img src="frontend/templates/base/images/16x16/page_white.png" class="list_image"/></em><xsl:value-of select="php:function('lang', 'contract_type')"/>: <xsl:value-of select="type"/></li>
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
					    				<!-- </ul>
						    		</div>
									<div id="contract_price_and_area" style="block:right;">
										<ul> -->
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
											<em>Kontraktsparter</em>
										</li>
										<xsl:for-each select="../party">
												<li style="margin-bottom: 5px;"><em><img src="frontend/templates/base/images/16x16/user_gray.png" class="list_image" /></em><xsl:value-of select="name"/><br/>
													<ul style="margin-left: 2em;">
														<xsl:choose>
															<xsl:when test="normalize-space(address)">
																<li><xsl:value-of select="address"/></li>
															</xsl:when>
															<xsl:when test="normalize-space(address1)">
																<li><xsl:value-of select="address1"/><br/>
																<xsl:value-of select="address2"/><br/>
																<xsl:value-of select="postal_code"/>&nbsp;
																<xsl:value-of select="place"/></li>
															</xsl:when>
															<xsl:when test="normalize-space(department)">
																<li><xsl:value-of select="department"/></li>
															</xsl:when>
														</xsl:choose>
													</ul>
												</li>
										</xsl:for-each>
										<!-- </ul>
									</div>
									<div id="composites">
										<ul> -->
										<li style="border-style: none none solid none; border-width: 1px; border-color: grey; margin-bottom: 5px; margin-top: 2em; padding-bottom: 5px;" >
											<img src="frontend/templates/base/images/16x16/layers.png" class="list_image" />
											<em>Leieobjekt</em>
										</li>
										<xsl:for-each select="../composite">
												<li><img src="frontend/templates/base/images/16x16/application_home.png" class="list_image" /> <xsl:value-of select="name" /></li>
												<li>
													<dl style="padding-left: 1em;">
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
										<ul>
											<li style="border-style: none none solid none; border-width: 1px; border-color: grey; margin-bottom: 5px; padding-bottom: 5px; margin-right: 1em;" >
												<img src="frontend/templates/base/images/16x16/comment.png" class="list_image" />
												<em>Kommentar</em>
											</li>
											<li>
												<xsl:choose>
													<xsl:when test="publish_comment = 1">
														<xsl:value-of select="comment" disable-output-escaping="yes"/>
													</xsl:when>
												</xsl:choose>
											</li>
										</ul>
									</div>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
				</div>
        	</div>
    	</div>
    </div>
</xsl:template>

<xsl:template match="contract">
	<xsl:copy-of select="."/>
	
</xsl:template>


