<xsl:template name="cases_tab_menu">
<div class="tab_menu">
		<a id="view_open_errors">					
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_open_cases</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
			Vis åpne saker
		</a>
		<a class="active" id="view_closed_errors">					
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_closed_cases</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
			Vis lukkede saker
		</a>
		<a id="view_measurements">					
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_measurements</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
			Vis målinger
		</a>
	</div>
</xsl:template>
