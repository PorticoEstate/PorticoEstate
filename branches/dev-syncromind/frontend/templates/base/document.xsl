<xsl:template match="contract_data" xmlns:php="http://php.net/xsl">

	<xsl:choose>
	    <xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
	    </xsl:when>
   </xsl:choose>
   
	<xsl:variable name="tab_selected"><xsl:value-of select="tab_selected"/></xsl:variable>
	
	<div class="frontend_body">
		<div class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<div id="{$tab_selected}">
					<div>
						<div class="pure-g">
							<div class="pure-u-1">
								<img src="frontend/templates/base/images/16x16/page_white_stack.png" class="list_image"/>
								<form action="{form_url}" method="post">
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
							</div>
							<xsl:choose>
								<xsl:when test="not(normalize-space(select)) and (count(select) &lt;= 1)">
									<div class="pure-u-1">
										<xsl:value-of select="php:function('lang', 'no_contracts')"/>
									</div>
								</xsl:when>
								<xsl:otherwise>
									<div class="pure-u-1">
										<form action="{form_url}" method="post">
										   <xsl:for-each select="select">
											   <xsl:choose>
												   <xsl:when test="id = //selected_contract">
													   <input name="contract_id" type="radio" value="{id}" checked="" onclick="this.form.submit();" style="margin-left: 1em;"></input> 
												   </xsl:when>
												   <xsl:otherwise>	
													   <input name="contract_id" type="radio" value="{id}" onclick	="this.form.submit();" style="margin-left: 1em;"></input>
												   </xsl:otherwise>
											   </xsl:choose>
											   <label style="margin-right: 1em; padding-left: 5px;"> <xsl:value-of select="old_contract_id"/> (<xsl:value-of select="contract_status"/>)</label>
										   </xsl:for-each>
										</form>
									</div>
								</xsl:otherwise>
							</xsl:choose>
						</div>
					</div>
					
					<div class="tickets">
						<xsl:for-each select="datatable_def">
							<xsl:if test="container = 'datatable-container_0'">
								<xsl:call-template name="table_setup">
									<xsl:with-param name="container" select ='container'/>
									<xsl:with-param name="requestUrl" select ='requestUrl' />
									<xsl:with-param name="ColumnDefs" select ='ColumnDefs' />
									<xsl:with-param name="tabletools" select ='tabletools' />
									<xsl:with-param name="data" select ='data' />
									<xsl:with-param name="config" select ='config' />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>	
					</div>
				</div>
				<xsl:value-of disable-output-escaping="yes" select="tabs_content" />
			</div>
		</div>
	</div>
</xsl:template>