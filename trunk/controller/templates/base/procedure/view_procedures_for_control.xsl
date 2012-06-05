<!-- $Id: procedure_item.xsl 8485 2012-01-05 08:21:03Z erikhl $ -->

<xsl:template match="data">
	
<div id="procedures">
	<h2>Prosedyre for <xsl:value-of select="control_procedure/title"/></h2>
	

		<a class="btn_m" id="print-control-items" target="_blank">
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
			Skriv ut prosedyre
		</a>
		
	<xsl:if test="control_procedure/documents/child::node()">
		<h4>Dokumenter</h4>
		<xsl:for-each select="control_procedure/documents">
			<div class="doc">
				<xsl:variable name="doc_link"><xsl:value-of select='document_link'/></xsl:variable>
				<span><a href="{$doc_link}"><xsl:value-of select="title"/></a></span>
				<span class="desc"><xsl:value-of select="description" disable-output-escaping="yes"/></span>
			</div>	
		</xsl:for-each>
	</xsl:if>
	
	<h3>Prosedyrer for grupper</h3>
	
	<ul id="groups">
		<xsl:for-each select="group_procedures_array">
			<li>
				<h4><xsl:value-of select="procedure/title"/></h4>
				<div class="group">
					<span>Gruppe: </span><xsl:value-of select="control_group/group_name"/>
					<a class="btn_sm" id="print-control-items" target="_blank">
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
				<h4>Dokumenter</h4>
					<xsl:for-each select="documents">
						<div class="doc">
							<xsl:variable name="doc_link"><xsl:value-of select='document_link'/></xsl:variable>
							<span><a href="{$doc_link}"><xsl:value-of select="title"/></a></span>
							<span class="desc"><xsl:value-of select="description" disable-output-escaping="yes"/></span>
						</div>	
					</xsl:for-each>
				</xsl:if>
			</li>
		</xsl:for-each>
	</ul>
</div>
</xsl:template>
