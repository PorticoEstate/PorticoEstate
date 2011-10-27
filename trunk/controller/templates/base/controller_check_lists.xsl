<xsl:template name="heck_lists" xmlns:php="http://php.net/xsl">

<div class="yui-content tab_content">
		
	  <!-- ===========================  SHOWS CHECK LIST   =============================== -->
<h3>dsfdsfdsfdsfdsfds</h3>
		<ul class="check_list">
			<xsl:for-each select="check_list_array">
				<li>
				
			        <span>Tittel:</span><xsl:value-of select="title"/><span>Start dato:</span><xsl:value-of select="start_date"/>
				</li>
			</xsl:for-each>
		</ul>					
</div>
</xsl:template>