<!-- $Id: control_tabs.xsl 9951 2012-08-31 10:14:12Z vator $ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div class="yui-navset yui-navset-top" id="activity-tabview">

	<xsl:choose>
		<xsl:when test="view = 'activity_details'">
			<xsl:call-template name="yui_phpgw_i18n"/>

			<xsl:choose>
				<xsl:when test="activity/id != '' or activity/id != 0">
					<h1 style="float:left;"> 
						<span>
							<xsl:value-of select="php:function('lang', 'Add requirement to activity')" />
						</span>
						<span style="margin-left:5px;">
							<xsl:value-of select="activity/name" />
						</span>
					</h1>
					<xsl:value-of disable-output-escaping="yes" select="breadcrumb" />	
				</xsl:when>
				<xsl:otherwise>
					<h1 style="float:left;"> 
						<xsl:value-of select="php:function('lang', 'Requirement allocation')" />
					</h1>
					<xsl:value-of disable-output-escaping="yes" select="breadcrumb" />	
				</xsl:otherwise>
			</xsl:choose>
		
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="activity_details" />
		</xsl:when>
		<xsl:when test="view = 'requirement_overview'">
			<xsl:choose>
				<xsl:when test="activity/id != '' or activity/id != 0">
					<h1 style="float:left;"> 
						<span>
							<xsl:value-of select="php:function('lang', 'Add criterias')" />
						</span>
						<span style="margin-left:5px;">
							<xsl:value-of select="activity/name" />
						</span>
					</h1>
				</xsl:when>
				<xsl:otherwise>
					<h1 style="float:left;"> 
						<xsl:value-of select="php:function('lang', 'Add criterias')" />
					</h1>
				</xsl:otherwise>
			</xsl:choose>
		
			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="requirement_overview" />
		</xsl:when>
	</xsl:choose>
</div>
	
</xsl:template>
