<!-- $Id$ -->
<xsl:template match="drawings" xmlns:php="http://php.net/xsl">
	
    <xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
	<xsl:variable name="tab_selected"><xsl:value-of select="tab_selected"/></xsl:variable>
	
	<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<div id="{$tab_selected}">
				<xsl:choose>
					<xsl:when test="normalize-space(//header/selected_location) != ''">
						<div>
							<table cellpadding="2" cellspacing="2" width="95%" align="center">
								<xsl:choose>
									<xsl:when test="msgbox_data != ''">
										<tr>
											<td align="left" colspan="3">
												<xsl:call-template name="msgbox"/>
											</td>
										</tr>
									</xsl:when>
								</xsl:choose>
							</table>
		
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
					</xsl:when>
					<xsl:otherwise>
						<div class="entity">
							<xsl:value-of select="php:function('lang', 'no_buildings')"/>
						</div>				
					</xsl:otherwise>
				</xsl:choose>
			</div>
			<xsl:value-of disable-output-escaping="yes" select="tabs_content" />
		</div>
	</form>
</xsl:template>