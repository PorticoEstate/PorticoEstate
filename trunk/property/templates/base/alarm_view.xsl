<!-- $Id$ -->

	<xsl:template name="alarm_view">
		<xsl:apply-templates select="alarm_data"/>
	</xsl:template>

	<xsl:template name="alarm_data">
		<table width="80%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="header"/>
			<xsl:apply-templates select="values"/>
		</table>
	</xsl:template>


	<xsl:template match="header">
		<tr class="th">
			<td class="th_text" width="20%" align="left">
				<xsl:value-of select="lang_time"/>
			</td>
			<td class="th_text" width="30%" align="left">
				<xsl:value-of select="lang_text"/>
			</td>
			<td class="th_text" width="10%" align="left">
				<xsl:value-of select="lang_user"/>
			</td>
			<td class="th_text" width="10%" align="center">
				<xsl:value-of select="lang_enabled"/>
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
				<xsl:value-of select="time"/>
			</td>
			<td align="left">
				<pre><xsl:value-of select="text"/></pre>
			</td>
			<td align="left">
				<xsl:value-of select="user"/>
			</td>
			<td align="center">
				<xsl:value-of select="enabled"/>
			</td>
		</tr>
	</xsl:template>
