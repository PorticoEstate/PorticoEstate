<xsl:template name="control_groups" xmlns:php="http://php.net/xsl">

<div class="yui-content">
	<div id="control_groups">
	
		<h2><xsl:value-of select="control_area/title"/></h2>
			
		<form action="#" method="post">
		<xsl:variable name="control_area_id"><xsl:value-of select="control_area/id"/></xsl:variable>
		<input type="hidden" name="control_area_id" value="{$control_area_id}" />
		
		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>
		<input type="hidden" name="control_id" value="{control_id}" />
		
		<ul class="itemlist">
		<xsl:for-each select="//control_groups">
			<xsl:variable name="control_group_id"><xsl:value-of select="id"/></xsl:variable>
      		<li><input type="checkbox"  name="control_group_ids[]" value="{$control_group_id}" /><xsl:value-of select="group_name"/></li>
		</xsl:for-each>
		</ul>
		<div class="form-buttons">
			<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
			<input type="submit" name="save_control_groups" value="{$lang_save}" title = "{$lang_save}" />
			</div>
		</form>					
	</div>
</div>
</xsl:template>