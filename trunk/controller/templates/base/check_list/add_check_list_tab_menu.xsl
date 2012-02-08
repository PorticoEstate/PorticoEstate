<xsl:template name="check_list_tab_menu" xmlns:php="http://php.net/xsl">

<xsl:param name="active_tab" />

<div id="edit_check_list_menu" class="hor_menu">
		<a>
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
			Vis detaljer for sjekkliste
		</a>
		<a href="#">
			Vis saker
		</a>			
		<a>
			<xsl:if test="$active_tab = 'view_control_info'">
				<xsl:attribute name="class">active</xsl:attribute>
			</xsl:if>
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_control_info</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
			Vis info om kontroll
		</a>
		<div>
			<a class="btn focus first">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list.register_case</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="check_list/id"/>
				</xsl:attribute>
				Registrer sak
			</a>
			<a class="btn focus">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicase.create_case_message</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="check_list/id"/>
				</xsl:attribute>
				Registrer melding
			</a>
		</div>
	</div>
		
</xsl:template>
