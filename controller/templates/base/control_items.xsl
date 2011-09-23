<xsl:template name="control_items" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
	<xsl:value-of select="php:function('lang', 'Control_items')" />
</h1>
</div>

<div class="yui-content">
	<div id="details">
		<form action="#" method="post">	
		
		<xsl:for-each select="//control_items">
			<ul>
				<h4><xsl:value-of select="group_name"/></h4>		
				<xsl:for-each select="control_item">
					<xsl:variable name="control_items_id"><xsl:value-of select="id"/></xsl:variable>
      				<li><input type="checkbox"  name="control_items_ids[]" value="{$control_items_id}" /><xsl:value-of select="title"/></li>	
				</xsl:for-each>
			</ul>
		</xsl:for-each>
		
		<div class="form-buttons">
			<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
			<input type="submit" name="save_control_items" value="{$lang_save}" title = "{$lang_save}" />
			</div>
		</form>					
	</div>
</div>
</xsl:template>
