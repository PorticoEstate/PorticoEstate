<!-- $Id$ -->
<!-- attrib_history -->
	<xsl:template match="attrib_history">
		<div><br></br></div>
		<!--  DATATABLE -->	
		<div align="left" id="paging_0"> </div>
		<div id="datatable-container_0"></div>
		<div id="contextmenu_0"></div>
		<div><br></br></div>
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"></xsl:value-of>;
			var base_java_url = <xsl:value-of select="base_java_url"></xsl:value-of>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"></xsl:value-of>] = [
				{
				values			:	<xsl:value-of select="values"></xsl:value-of>,
				total_records	: 	<xsl:value-of select="total_records"></xsl:value-of>,
				is_paginator	:  	<xsl:value-of select="is_paginator"></xsl:value-of>,
				permission  	: <xsl:value-of select="permission"></xsl:value-of>,
				footer			:	<xsl:value-of select="footer"></xsl:value-of>
				}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"></xsl:value-of>] = <xsl:value-of select="values"></xsl:value-of>
			</xsl:for-each>

		</script>			

		<!--  
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/> 
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header"/>
				<xsl:apply-templates select="values"/>
		</table>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_time_created" select="sort_time_created"/>
		<xsl:variable name="sort_value" select="sort_value"/>

			<tr class="th">
				<td width="40%">
					<a href="{$sort_value}" class="th_text"><xsl:value-of select="lang_value"/></a>
				</td>
				<td width="10%" align="center">
					<a href="{$sort_time_created}" class="th_text"><xsl:value-of select="lang_time_created"/></a>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_user"/>
				</td>
				<td width="5%" align="center">
					<xsl:value-of select="lang_delete"/>
				</td>
			</tr>
	</xsl:template>

	<xsl:template match="values">
		<xsl:variable name="lang_delete_statustext"><xsl:value-of select="lang_delete_statustext"/></xsl:variable>
			<tr>
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"/>
						</xsl:when>
						<xsl:when test="position() mod 2 = 0">
							<xsl:text>row_off</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>row_on</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>

				<td align="left">
					<xsl:value-of select="value"/>
				</td>
				<td align="left">
					<xsl:value-of select="time_created"/>
				</td>
				<td align="left">
					<xsl:value-of select="user"/>
				</td>
				<td align="center">
					<xsl:variable name="link_delete"><xsl:value-of select="link_delete"/></xsl:variable>
					<a href="{$link_delete}" onMouseover="window.status='{$lang_delete_statustext}';return true;" onMouseout="window.status='';return true;"><xsl:value-of select="text_delete"/></a>
				</td>
			</tr>
	-->
	</xsl:template>
