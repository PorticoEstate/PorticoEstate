<xsl:template name="control_group_items" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
	<xsl:value-of select="php:function('lang', 'Control_items')" />
</h1>
</div>

<div class="yui-content">
	<div>
		<xsl:if test="selected_control_items">
			<h2>Valgte kontrollpunkt</h2>
			<form action="#" method="post">	
	   			<ul class="control_items">
					<xsl:for-each select="selected_control_items">
						<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
						<li><xsl:if test="//editable"><input type="checkbox"  name="item_remove_ids[]" value="{$control_item_id}"/></xsl:if><xsl:value-of select="title"/></li>
					</xsl:for-each>
				</ul>
				<xsl:if test="//editable">
					<div class="form-buttons">
						<xsl:variable name="lang_remove"><xsl:value-of select="php:function('lang', 'remove')" /></xsl:variable>
						<input type="submit" name="remove_control_group_items" value="{$lang_remove}" title = "{$lang_remove}" />
					</div>
				</xsl:if>
			</form>
		</xsl:if>
	   <!-- ===========================  CHOOSE CONTROL ITEMS  =============================== -->
	   <xsl:choose>
	   	<xsl:when test="editable">
	   		<h2>Velg kontrollpunkt</h2>
			<form action="#" method="post">	
			
			<xsl:variable name="control_group_id"><xsl:value-of select="value_id"/></xsl:variable>
			<input type="hidden" name="control_group_id" value="{control_group_id}" />
			
			<ul class="control_items">
				<xsl:for-each select="control_items">
					<xsl:variable name="control_item_id"><xsl:value-of select="id"/></xsl:variable>
	   				<li><input type="checkbox"  name="control_tag_ids[]" value="{$control_item_id}" /><xsl:value-of select="title"/></li>
				</xsl:for-each>
			</ul>		
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
				<input type="submit" name="save_control_group_items" value="{$lang_save}" title = "{$lang_save}" />
			</div>
			</form>
		</xsl:when>
	   </xsl:choose>
	</div>
</div>
</xsl:template>