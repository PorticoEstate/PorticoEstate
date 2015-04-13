<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div id="requirement_tabview">

	<xsl:choose>
		<xsl:when test="view = 'requirement_details'">

			<xsl:choose>
				<xsl:when test="requirement/id != '' or requirement/id != 0">
					<h1 style="float:left;"> 
						<span>
							<xsl:value-of select="php:function('lang', 'Add requirement to activity')" />
						</span>
						<span style="margin-left:5px;">
							<xsl:value-of select="activity/name" />
						</span>
					</h1>
				</xsl:when>
				<xsl:when test="activity/id != '' or activity/id != 0">
					<h1 style="float:left;"> 
						<span>
							<xsl:value-of select="php:function('lang', 'Add requirement to activity')" />
						</span>
						<span style="margin-left:5px;">
							<xsl:value-of select="activity/name" />
						</span>
					</h1>
				</xsl:when>
				<xsl:otherwise>
					<h1 style="float:left;"> 
						<xsl:value-of select="php:function('lang', 'Add requirement')" />
					</h1>
				</xsl:otherwise>
			</xsl:choose>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<xsl:call-template name="requirement_details" />
			</div>
		</xsl:when>
		<xsl:when test="view = 'requirement_values'">
		
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
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<xsl:call-template name="requirement_values" />
			</div>
		</xsl:when>
	</xsl:choose>
</div>
</xsl:template>
