<!-- $Id: columns.xsl,v 1.1 2005/01/17 10:03:18 sigurdne Exp $ -->

	<xsl:template name="app_data">
		<xsl:choose>
			<xsl:when test="columns">
				<xsl:apply-templates select="columns"/>
			</xsl:when>
		</xsl:choose>
	</xsl:template>


	<xsl:template match="columns">
		<div align="left">
		<table cellpadding="2" cellspacing="2" width="80%" align="center">
			<xsl:choose>
				<xsl:when test="msgbox_data != ''">
					<tr>
						<td align="left" colspan="3">
							<xsl:call-template name="msgbox"/>
						</td>
					</tr>
				</xsl:when>
			</xsl:choose>
			<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
			<form method="post" name="form" action="{$form_action}">
			<tr>
				<td valign="top">
					<xsl:value-of select="lang_columns"/>
				</td>
				<td>
					<xsl:variable name="lang_columns_statustext"><xsl:value-of select="lang_columns_statustext"/></xsl:variable>
						<select name="values[columns][]" class="forms" multiple="multiple" onMouseover="window.status='{$lang_columns_statustext}'; return true;" onMouseout="window.status='';return true;">
							<option value=""><xsl:value-of select="lang_none"/></option>
							<xsl:apply-templates select="column_list"/>
						</select>
						
				</td>
			</tr>
			<tr height="50">
				<td>
					<xsl:variable name="lang_save"><xsl:value-of select="lang_save"/></xsl:variable>
					<input type="submit" name="values[save]" value="{$lang_save}" onMouseout="window.status='';return true;">
						<xsl:attribute name="onMouseover">
							<xsl:text>window.status='</xsl:text>
								<xsl:value-of select="lang_save_statustext"/>
							<xsl:text>'; return true;</xsl:text>
						</xsl:attribute>
					</input>
				</td>
			</tr>


			</form>
		</table>
		</div>
	</xsl:template>

	<xsl:template match="column_list">
	<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="selected">
				<option value="{$id}" selected="selected"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$id}"><xsl:value-of disable-output-escaping="yes" select="name"/></option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


