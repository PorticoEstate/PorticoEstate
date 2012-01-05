<!-- $Id: choose_control_items.xsl 8267 2011-12-11 12:27:18Z sigurdne $ -->
<xsl:template name="view_control_items" xmlns:php="http://php.net/xsl">
<div>
	<ul class="control_items">
		<xsl:for-each select="control_items_array">
			<li>
			 	<h4><xsl:value-of select="control_item/title"/></h4>
			</li>
		</xsl:for-each>
	</ul>
		
	<div>
		<input class="btn" type="submit" name="save_control_items" value="Print" title = "{$lang_save}" />
	</div>				
</div>
</xsl:template>
