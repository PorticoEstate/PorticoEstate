<!-- $Id: control_groups.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<xsl:template name="control_groups" xmlns:php="http://php.net/xsl">

<div class="yui-content tab_content">
	<div id="control_groups">
	
		<h2>Velg kontrollgrupper fra <xsl:value-of select="control_area/name"/></h2>
		
		<xsl:variable name="action_url"><xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicontrol.save_control_groups')" /></xsl:variable>

		<form id="frm_save_control_groups" action="{$action_url}" method="post">
			<xsl:variable name="control_area_id"><xsl:value-of select="control_area/id"/></xsl:variable>
			<input type="hidden" name="control_area_id" value="{$control_area_id}" />
			
			<xsl:variable name="control_id"><xsl:value-of select="control/id"/></xsl:variable>
			<input type="hidden" name="control_id" value="{$control_id}" />
			
			<ul class="itemlist">
				<xsl:for-each select="//control_groups">
					<xsl:variable name="control_group_id"><xsl:value-of select="control_group/id"/></xsl:variable>
					
					<xsl:choose>
						<xsl:when test="checked = 1">
							<li><input type="checkbox" checked="checked" name="control_group_ids[]" value="{$control_group_id}" /><xsl:value-of select="control_group/group_name"/></li>
						</xsl:when>
						<xsl:otherwise>
							<li><input type="checkbox" name="control_group_ids[]" value="{$control_group_id}" /><xsl:value-of select="control_group/group_name"/></li>
						</xsl:otherwise>
					</xsl:choose>
			  		
				</xsl:for-each>
			</ul>
			<div>
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<input type="submit" name="save_control_groups" value="{$lang_save}" title = "{$lang_save}" />
			</div>
		</form>					
	</div>
</div>
</xsl:template>
