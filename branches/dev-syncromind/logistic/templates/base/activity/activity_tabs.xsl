<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div id="activity-tabview">
	<xsl:choose>
		<xsl:when test="view = 'activity_details'">

			<!-- =========== HEADING ============== -->
			<xsl:choose>
				<xsl:when test="activity/id != '' or activity/id != 0">
					<h1 style="float:left;"> 
						<span>
							<xsl:value-of select="php:function('lang', 'Overview for activity')" />
						</span>
						<span style="margin-left:5px;">
							<xsl:value-of select="activity/name" />
						</span>
					</h1>
				</xsl:when>
				<xsl:otherwise>
					<h1 style="float:left;"> 
						<xsl:value-of select="php:function('lang', 'Requirement allocation')" />
					</h1>
				</xsl:otherwise>
			</xsl:choose>
			
			<!-- =========== BREADCRUMB ============== -->
		 <xsl:call-template name="breadcrumb" />
		 	<div id="tab-content">

			<xsl:value-of disable-output-escaping="yes" select="tabs" />
			<xsl:call-template name="activity_details" />
			</div>
		</xsl:when>
		<xsl:when test="view = 'requirement_overview'">
			
			<!-- =========== HEADING ============== -->
			<xsl:choose>
				<xsl:when test="activity/id != '' or activity/id != 0">
					<h1 style="float:left;"> 
						<span>
							<xsl:value-of select="php:function('lang', 'Overview of resources for activity')" />
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
			
			<!-- =========== BREADCRUMB ============== -->
		 	<xsl:call-template name="breadcrumb" />
		 	<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs" />
				<xsl:call-template name="requirement_overview" />
			</div>
		</xsl:when>
	</xsl:choose>
</div>
<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>

<!-- =========== BREADCRUMB TEMPLATE  ============== -->
<xsl:template name="breadcrumb">
  <div id="breadcrumb">
		<span class="intro">Du er her:</span>
		<xsl:for-each select="breadcrumb">
			<xsl:choose>
				<xsl:when test="current = 1">
					<span class="current">
						<xsl:value-of select="name"/>
					</span>
				</xsl:when>
				<xsl:otherwise>
					<a href="{link}">
						<xsl:value-of select="name"/>
					</a>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="not( position() = last() )">
      			<img src="logistic/images/arrow_right.png" />
    		</xsl:if>
      </xsl:for-each>
	</div>
</xsl:template>
