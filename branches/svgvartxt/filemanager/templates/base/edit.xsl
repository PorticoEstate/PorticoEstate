<!-- $Id$ -->

	<xsl:template match="edit">
		<form method="post">
			<xsl:attribute name="action"><xsl:value-of select="form_action"/></xsl:attribute>
			<table border="0" width="100%">
				<tr>
					<td>
						<xsl:apply-templates select="filemanager_nav" />
					</td>
				</tr>
				<xsl:apply-templates select="filemanager_edit" />
			</table>
		</form>
	</xsl:template>

	<xsl:template match="filemanager_nav">
		<table width="100%" border="0">
			<tr>
				<td width="5%">
					<nobr>
						<xsl:apply-templates select="img_up/widget" />
						<xsl:apply-templates select="help_up/widget" />
					</nobr>
				</td>
				<td width="5%">
					<nobr>
						<xsl:apply-templates select="img_home/widget" />
						<xsl:apply-templates select="help_home/widget" />
					</nobr>
				</td>
				<td align="left" valign="middle">
					<font size="+1" color="maroon"><b><xsl:value-of select="current_dir" /></b></font>
				</td>
				<td align="center" width="33%">
					<b><xsl:value-of select="lang_edit"/>&nbsp;<font size="+1" color="maroon"><xsl:value-of select="filename"/></font></b>
				</td>
				<td align="right" width="33%">
					<table border="0" cellpadding="2" cellspacing="0">
						<tr>
							<xsl:for-each select="nav_data">
								<td>
									<xsl:apply-templates />
								</td>
							</xsl:for-each>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<hr />
	</xsl:template>

	<xsl:template match="filemanager_edit">
		<tr>
			<td align="center">
				<b><xsl:value-of select="output"/></b>
			</td>
		</tr>
		<tr>
			<xsl:apply-templates select="form_data/*" />
			<xsl:choose>
				<xsl:when test="preview">
					<xsl:apply-templates select="preview" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="file_content" /> 
				</xsl:otherwise>
			</xsl:choose>
		</tr>
	</xsl:template>

	<xsl:template match="file_content">
		<td align="center">
			<textarea name="edit_file_content" rows="30" cols="80" class="fileeditor" wrap="VIRTUAL"><xsl:value-of select="." /></textarea>
		</td>
	</xsl:template>

	<xsl:template match="preview">
		<td>
			<xsl:value-of disable-output-escaping="yes" select="."/>
		</td>
	</xsl:template>
