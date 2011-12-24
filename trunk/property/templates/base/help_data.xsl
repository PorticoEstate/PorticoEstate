<!-- $Id$ -->

	<xsl:template name="help_data">
		<xsl:apply-templates select="xhelp"></xsl:apply-templates>
	</xsl:template>

	<xsl:template match="xhelp">
		<xsl:choose>
			<xsl:when test="overview">
				<xsl:apply-templates select="overview"></xsl:apply-templates>
			</xsl:when>
			<xsl:when test="list">
				<xsl:apply-templates select="list"></xsl:apply-templates>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="add"></xsl:apply-templates>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="overview">
		<table>
			<tr>
				<td>
					<xsl:value-of disable-output-escaping="yes" select="intro"></xsl:value-of>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:value-of disable-output-escaping="yes" select="prefs_settings"></xsl:value-of>
				</td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="list">
		<xsl:variable name="list_img" select="list_img"></xsl:variable>
		<table>
			<tr>
				<td colspan="2">
					<img src="{$list_img}"></img>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right">1</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_1"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">2</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_2"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">3</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_3"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">4</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_4"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">5</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_5"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">6</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_6"></xsl:value-of></td>
			</tr>
			<tr>
				<td colspan="2"><u><xsl:value-of disable-output-escaping="yes" select="h_data"></xsl:value-of></u></td>
			</tr>
			<tr>
				<td valign="top" align="right">7</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_7"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">8</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_8"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">9</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_9"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">10</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_10"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">11</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_11"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">12</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_12"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">13</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_13"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">14</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_14"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">15</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_15"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">16</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_16"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">17</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_17"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">18</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_18"></xsl:value-of></td>
			</tr>
		</table>
	</xsl:template>

	<xsl:template match="add">
		<xsl:variable name="add_img" select="add_img"></xsl:variable>
		<table>
			<tr>
				<td colspan="2">
					<img src="{$add_img}"></img>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right">1</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_1"></xsl:value-of></td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="80%" bgcolor="#ccddeb">
						<tr>
							<td><xsl:value-of select="lang_lastname"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_firstname"></xsl:value-of>:</td>
						</tr>
						<tr>
							<td><xsl:value-of select="lang_email"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_company"></xsl:value-of>:</td>
						</tr>
						<tr>
							<td><xsl:value-of select="lang_homephone"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_fax"></xsl:value-of>:</td>
						</tr>
						<tr>
							<td><xsl:value-of select="lang_workphone"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_pager"></xsl:value-of>:</td>
						</tr>
						<tr>
							<td><xsl:value-of select="lang_mobile"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_othernumber"></xsl:value-of>:</td>
						</tr>
						<tr>
							<td><xsl:value-of select="lang_street"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_city"></xsl:value-of>:</td>
						</tr>
						<tr>
							<td><xsl:value-of select="lang_state"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_zip"></xsl:value-of>:</td>
						</tr>
						<tr>
							<td><xsl:value-of select="lang_access"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_groupsettings"></xsl:value-of>:</td>
						</tr>
						<tr>
							<td><xsl:value-of select="lang_notes"></xsl:value-of>:</td>
							<td><xsl:value-of select="lang_birthday"></xsl:value-of>:</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><xsl:value-of disable-output-escaping="yes" select="access_descr"></xsl:value-of></td>
			</tr>
			<tr>
				<td valign="top" align="right">2</td>
				<td><xsl:value-of disable-output-escaping="yes" select="item_2"></xsl:value-of></td>
			</tr>
		</table>
	</xsl:template>
