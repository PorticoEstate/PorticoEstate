<!-- $Id: invoice_list_sub.xsl 14701 2016-02-05 14:56:50Z sigurdne $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="list_sub">
			<xsl:apply-templates select="list_sub"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template match="list_sub">
	<div id="message"></div>
	<xsl:call-template name="top-toolbar" />
	<div>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dl>
					<dt>
						<xsl:call-template name="msgbox"/>
					</dt>
				</dl>
			</xsl:when>
		</xsl:choose>
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
</xsl:template>

<xsl:template name="top-toolbar">
	<div class="toolbar-container">
		<div class="pure-g">
			<div class="pure-u-1-3">
				<div id="vendorDiv"></div>
				<div id="voucheridDiv"></div>
			</div>
			<div class="pure-u-2-3">
				<xsl:for-each select="top_toolbar">
					<a class="pure-button pure-button-primary" href="{url}">
						<xsl:value-of select="value"/>
					</a>
				</xsl:for-each>
			</div>
		</div>
	</div>
</xsl:template>