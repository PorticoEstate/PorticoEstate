<!-- $Id: procedure_item.xsl 8485 2012-01-05 08:21:03Z erikhl $ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:variable name="session_url"><xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<div id="view-procedures">
		<h2>Prosedyrer</h2>
		<div class="box">
			<h3>Prosedyre for kontroll</h3>
			<h4>
				<xsl:value-of select="control_procedure/title"/>
				<a class="btn-sm" id="print-control-items" target="_blank">
					<xsl:attribute name="href">
						<xsl:text>index.php?menuaction=controller.uiprocedure.print_procedure</xsl:text>
						<xsl:text>&amp;procedure_id=</xsl:text>
						<xsl:value-of select="control_procedure/id"/>
						<xsl:text>&amp;control_id=</xsl:text>
						<xsl:value-of select="control/id"/>
						<xsl:text>&amp;location_code=</xsl:text>
						<xsl:value-of select="location/location_code"/>
						<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
						<xsl:value-of select="$session_url"/>
					</xsl:attribute>
					Skriv ut
				</a>
			</h4>
			
			<xsl:if test="control_procedure/documents/child::node()">
				<h4>Dokumenter</h4>
				<xsl:for-each select="control_procedure/documents">
					<div class="doc">
						<xsl:variable name="doc_link">
							<xsl:value-of select='document_link'/>
						</xsl:variable>
						<span>
							<a href="{$doc_link}">
								<xsl:value-of select="title"/>
							</a>
						</span>
						<span class="desc">
							<xsl:value-of select="description" disable-output-escaping="yes"/>
						</span>
					</div>
				</xsl:for-each>
			</xsl:if>
		</div>
	
		<div class="box">
			<h3>Prosedyrer for grupper</h3>
			<table class="pure-table">
				<thead>
					<tr>
						<th>Gruppe</th>
						<th>Prosedyre</th>
						<th>Skriv ut</th>
					</tr>
				</thead>
				<tbody>
					<xsl:for-each select="group_procedures_array">
						<tr>
							<td>
								<xsl:value-of select="control_group/group_name"/>
							</td>
							<td>
								<xsl:value-of select="procedure/title"/>
								<xsl:if test="documents/child::node()">
									<br/>
									<h4>Dokumenter</h4>
									<ul>
										<xsl:for-each select="documents">
											<li>
												<div class="doc">
													<xsl:variable name="doc_link">
														<xsl:value-of select='document_link'/>
													</xsl:variable>
													<span>
														<a href="{$doc_link}">
															<xsl:value-of select="title"/>
														</a>
													</span>
													<span class="desc">
														<xsl:value-of select="description" disable-output-escaping="yes"/>
													</span>
												</div>
											</li>
										</xsl:for-each>
									</ul>

								</xsl:if>
							</td>
							<td>
								<a class="btn-sm" id="print-control-items" target="_blank">
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
										<xsl:value-of select="$session_url"/>
									</xsl:attribute>
									Skriv ut
								</a>
							</td>
						</tr>
					</xsl:for-each>
				</tbody>
			</table>

		</div>
	</div>
</xsl:template>
