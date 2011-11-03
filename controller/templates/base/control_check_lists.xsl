<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><xsl:value-of select="php:function('lang', 'Check_lists')" /></h1>
</div>

<div class="yui-content">
		<div id="view_check_lists">
		
		<ul class="th"><li>Tittel</li><li>Startdato</li><li>Planlagtdato</li><li>Utførtdato</li></ul>
		<ul class="check_list">
			<xsl:for-each select="check_list_array">
				<li>
					<ul class="row">
						<li>
							<a>
								<xsl:attribute name="href">
									<xsl:text>index.php?menuaction=controller.uicheck_list.view_check_list_for_control</xsl:text>
									<xsl:text>&amp;control_id=</xsl:text>
									<xsl:value-of select="id"/>
								</xsl:attribute>
								<span><xsl:value-of select="title"/></span>
							</a>
						</li>
						<li><xsl:value-of select="start_date"/></li>
						<li><xsl:value-of select="end_date"/></li>
					</ul>
				</li>
			</xsl:for-each>
		</ul>		
						
		</div>
	</div>
</xsl:template>