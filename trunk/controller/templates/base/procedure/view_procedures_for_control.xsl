<!-- $Id: procedure_item.xsl 8485 2012-01-05 08:21:03Z erikhl $ -->

<xsl:template match="view_procedures_for_control">
	<h3 style="margin:5px 0;">Prosedyre for kontroll</h3>
	
	<span>Tittel: </span><xsl:value-of select="control_procedure/title"/>
	<a style="display:block;margin-top:5px;" id="print_control_items" target="_blank">
		<xsl:attribute name="href">
			<xsl:text>index.php?menuaction=controller.uiprocedure.print_procedure</xsl:text>
			<xsl:text>&amp;procedure_id=</xsl:text>
			<xsl:value-of select="control_procedure/id"/>
		</xsl:attribute>
		Skriv ut
	</a>
	
	<h3 style="margin:15px 0 3px 0;">Prosedyrer for grupper</h3>
	
	<ul class="groups">
		<xsl:for-each select="group_procedures_array">
			<li class="list_item">
				<h4 style="margin:2px 0;"><xsl:value-of select="procedure/title"/></h4>
				<div style="margin-left:10px;">
					<span>Gruppe: </span><xsl:value-of select="control_group/group_name"/>
					<a style="display:block;margin-top:5px;" id="print_control_items" target="_blank">
						<xsl:attribute name="href">
							<xsl:text>index.php?menuaction=controller.uiprocedure.print_procedure</xsl:text>
							<xsl:text>&amp;procedure_id=</xsl:text>
							<xsl:value-of select="procedure/id"/>
						</xsl:attribute>
						Skriv ut
					</a>
				</div>	
			</li>
		</xsl:for-each>
	</ul>
</xsl:template>
