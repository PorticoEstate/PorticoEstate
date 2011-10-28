<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><xsl:value-of select="php:function('lang', 'Check_lists')" /></h1>
</div>

<div class="yui-content">
		<div id="details">
			
		<ul class="check_list">
			<xsl:for-each select="check_list_array">
				<li>
			        <span>Tittel:<xsl:value-of select="title"/></span><span>Start dato:<xsl:value-of select="start_date"/></span>
				</li>
			</xsl:for-each>
		</ul>		
						
		</div>
	</div>
</xsl:template>