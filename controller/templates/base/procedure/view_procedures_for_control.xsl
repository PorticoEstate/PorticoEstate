<!-- $Id: procedure_item.xsl 8485 2012-01-05 08:21:03Z erikhl $ -->

<xsl:template match="data">
	<h3 style="margin:5px 0;">Prosedyre for kontroll</h3>
	
	<div><span>Tittel: </span><xsl:value-of select="control_procedure/title"/>
		<a style="margin-left:5px;" id="print_control_items" target="_blank">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uiprocedure.print_procedure</xsl:text>
				<xsl:text>&amp;procedure_id=</xsl:text>
				<xsl:value-of select="control_procedure/id"/>
				<xsl:text>&amp;control_id=</xsl:text>
				<xsl:value-of select="control/id"/>
				<xsl:text>&amp;location_code=</xsl:text>
				<xsl:value-of select="location/location_code"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
			Skriv ut
		</a>
	</div>
	<xsl:if test="control_procedure/documents/child::node()">
		<h4 style="margin:5px 0;">Dokumenter</h4>
		<xsl:for-each select="control_procedure/documents">
			<div style="margin-left:10px;">
				<span><xsl:value-of select="title"/></span>
				<span style="margin-left:10px;"><xsl:value-of select="description"/></span>
			</div>	
		</xsl:for-each>
	</xsl:if>
	
	<h3 style="margin:15px 0 3px 0;">Prosedyrer for grupper</h3>
	
	<ul id="groups">
		<xsl:for-each select="group_procedures_array">
			<li>
				<h4 style="margin:2px 0;"><xsl:value-of select="procedure/title"/></h4>
				<div style="margin-bottom:10px;">
					<span>Gruppe: </span><xsl:value-of select="control_group/group_name"/>
					<a style="margin-left:5px;" id="print_control_items" target="_blank">
						<xsl:attribute name="href">
							<xsl:text>index.php?menuaction=controller.uiprocedure.print_procedure</xsl:text>
							<xsl:text>&amp;procedure_id=</xsl:text>
							<xsl:value-of select="procedure/id"/>
							<xsl:text>&amp;control_id=</xsl:text>
							<xsl:value-of select="//control/id"/>
							<xsl:text>&amp;control_group_id=</xsl:text>
							<xsl:value-of select="control_group/id"/>
							<xsl:text>&amp;location_code=</xsl:text>
							<xsl:value-of select="//location/location_code"/>
							<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
						</xsl:attribute>
						Skriv ut
					</a>
				</div>
				<xsl:if test="documents/child::node()">
				<h4 style="margin:5px 0;">Dokumenter</h4>
					<xsl:for-each select="documents">
						<div style="margin-left:10px;">
							<span><xsl:value-of select="title"/></span>
							<span style="margin-left:10px;"><xsl:value-of select="description"/></span>
						</div>	
					</xsl:for-each>
				</xsl:if>
			</li>
		</xsl:for-each>
	</ul>
</xsl:template>
