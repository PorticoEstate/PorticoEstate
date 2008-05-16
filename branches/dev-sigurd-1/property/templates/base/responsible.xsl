
	<xsl:template name="app_data">
		<xsl:apply-templates select="list"/>
	</xsl:template>

	<xsl:template match="list">
		<xsl:variable name="responsible_action"><xsl:value-of select="responsible_action"/></xsl:variable>
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<tr>
				<td align="left">
					<xsl:call-template name="filter_location"/>
				</td>

				<td align="right">
					<xsl:call-template name="search_field"/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<xsl:call-template name="nextmatchs"/>
				</td>
			</tr>
		</table>
		<form method="post" action="{$responsible_action}">
			<table width="100%" cellpadding="2" cellspacing="2" align="center">
				<xsl:apply-templates select="table_header"/>
				<xsl:choose>
					<xsl:when test="values != ''">
						<xsl:apply-templates select="values"/>
					</xsl:when>
				</xsl:choose>
				<xsl:apply-templates select="table_add"/>
			</table>
		</form>
	</xsl:template>

	<xsl:template match="table_header">
		<xsl:variable name="sort_location"><xsl:value-of select="sort_location"/></xsl:variable>
		<tr class="th">
			<td class="th_text" width="10%" align="left">
				<a href="{$sort_location}"><xsl:value-of select="lang_location"/></a>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_action"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_user"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_supervisor"/>
			</td>
			<td class="th_text" width="5%" align="center">
				<xsl:value-of select="lang_select"/>
			</td>
		</tr>
	</xsl:template>

	<xsl:template match="values">
			<tr>
				<xsl:attribute name="class">
					<xsl:choose>
						<xsl:when test="@class">
							<xsl:value-of select="@class"/>
						</xsl:when>
						<xsl:when test="position() mod 2 = 0">
							<xsl:text>row_off</xsl:text>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>row_on</xsl:text>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<td align="left">
					<xsl:value-of select="location"/>
				</td>
				<td align="left">
					<xsl:value-of select="action"/>
				</td>
				<td align="left">
					<xsl:value-of select="user"/>
				</td>
				<td align="left">
					<xsl:value-of select="supervisor"/>
				</td>
				<xsl:choose>
					<xsl:when test="lang_select_responsible_text != ''">
						<td align="center" title="{lang_select_responsible_text}" style="cursor:help">
							<input type="checkbox" name="values[]" value="{}" >
							</input>
						</td>
					</xsl:when>
				</xsl:choose>
			</tr>
	</xsl:template>

	<xsl:template match="table_add">
		<xsl:variable name="lang_add"><xsl:value-of select="lang_add"/></xsl:variable>
		<tr>
			<td height="50">
				<input type="submit" name="add" value="{$lang_add}" onMouseout="window.status='';return true;">
					<xsl:attribute name="onMouseover">
						<xsl:text>window.status='</xsl:text>
							<xsl:value-of select="lang_add_statustext"/>
						<xsl:text>'; return true;</xsl:text>
					</xsl:attribute>
				</input>
			</td>
		</tr>
	</xsl:template>
