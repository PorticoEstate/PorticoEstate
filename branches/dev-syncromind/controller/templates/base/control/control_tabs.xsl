<!-- $Id$ -->
<!-- separate tabs and  inline tables-->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="control_tabview">
		<xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div id ='details'>
			<xsl:choose>
				<xsl:when test="view = 'control_details'">
					<!--xsl:call-template name="yui_phpgw_i18n"/-->
					<div class="identifier-header">
						<h1>
							<xsl:value-of select="php:function('lang', 'Control')"/>
						</h1>
					</div>
					<xsl:call-template name="control" />
				</xsl:when>
			</xsl:choose>
		</div>
		<div id ='control_groups'>
			<xsl:choose>
				<xsl:when test="view = 'control_groups'">
					<div class="identifier-header">
						<h1>
							<xsl:value-of select="php:function('lang', 'Control_groups')"/> for <xsl:value-of select="control/title" />
						</h1>
					</div>
					<xsl:call-template name="control_groups" />
				</xsl:when>
			</xsl:choose>
		</div>
		<!--div id ='control_locations'>
			<xsl:choose>
				<xsl:when test="view = 'control_locations'">
					<div class="identifier-header">
						<h1>
							<xsl:value-of select="php:function('lang', 'Control_locations')"/> for <xsl:value-of select="control/title" />
						</h1>
					</div>
					<xsl:call-template name="control_locations" />
				</xsl:when>
			</xsl:choose>
		</div>
		<div id ='control_component'>
			<xsl:choose>
				<xsl:when test="view = 'control_component'">
					<div class="identifier-header">
						<h1>
							<xsl:value-of select="php:function('lang', 'Control_component')"/> for <xsl:value-of select="control/title" />
						</h1>
					</div>
					<xsl:call-template name="control_component" />
				</xsl:when>
			</xsl:choose>
		</div-->
		<div id ='control_items'>
			<xsl:choose>
				<xsl:when test="view = 'control_items'">
					<div class="identifier-header">
						<h1>
							<xsl:value-of select="php:function('lang', 'Control_items')"/> for <xsl:value-of select="control/title" />
						</h1>
					</div>
					<xsl:call-template name="control_items" />
				</xsl:when>
			</xsl:choose>
		</div>
		<div id ='check_list'>
			<xsl:choose>
				<xsl:when test="view = 'sort_check_list'">
					<div class="identifier-header">
						<h1>
							<xsl:value-of select="php:function('lang', 'Check_list')"/> for <xsl:value-of select="control/title" />
						</h1>
					</div>
					<xsl:call-template name="sort_check_list" />
				</xsl:when>
			</xsl:choose>
		</div>
	</div>
	
</xsl:template>
