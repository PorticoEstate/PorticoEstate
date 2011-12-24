<!-- $Id$ -->

<xsl:template name="app_data">
	<xsl:apply-templates select="list"></xsl:apply-templates>
</xsl:template>

<xsl:template match="list">
	<xsl:variable name="migrate_action"><xsl:value-of select="migrate_action"></xsl:value-of></xsl:variable>
	<table width="100%" cellpadding="2" cellspacing="2" align="center">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<tr>
					<td align="left" colspan="3">
						<xsl:call-template name="msgbox"></xsl:call-template>
					</td>
				</tr>
			</xsl:when>
		</xsl:choose>
		<tr>
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"></xsl:call-template>
			</td>
		</tr>
	</table>
	<form method="post" action="{$migrate_action}">
		<table width="100%" cellpadding="2" cellspacing="2" align="center">
			<xsl:apply-templates select="table_header"></xsl:apply-templates>
			<xsl:choose>
				<xsl:when test="values != ''">
					<xsl:apply-templates select="values"></xsl:apply-templates>
				</xsl:when>
			</xsl:choose>
			<xsl:apply-templates select="table_migrate"></xsl:apply-templates>
		</table>
	</form>
</xsl:template>

<xsl:template match="table_header">
	<xsl:variable name="sort_domain"><xsl:value-of select="sort_domain"></xsl:value-of></xsl:variable>
	<tr class="th">
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_domain}"><xsl:value-of select="lang_domain"></xsl:value-of></a>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_db_host"></xsl:value-of>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_db_name"></xsl:value-of>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_db_type"></xsl:value-of>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_select"></xsl:value-of>
		</td>
	</tr>
</xsl:template>

<xsl:template match="values">
	<tr>
		<xsl:attribute name="class">
			<xsl:choose>
				<xsl:when test="@class">
					<xsl:value-of select="@class"></xsl:value-of>
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
			<xsl:value-of select="domain"></xsl:value-of>
		</td>
		<td align="left">
			<xsl:value-of select="db_host"></xsl:value-of>
		</td>
		<td align="left">
			<xsl:value-of select="db_name"></xsl:value-of>
		</td>
		<td align="left">
			<xsl:value-of select="db_type"></xsl:value-of>
		</td>
		<xsl:choose>
			<xsl:when test="lang_select_migrate_text != ''">
				<td align="center" title="{lang_select_migrate_text}" style="cursor:help">
					<input type="checkbox" name="values[]" value="{domain}">
					</input>
				</td>
			</xsl:when>
		</xsl:choose>
	</tr>
</xsl:template>

<xsl:template match="table_migrate">
	<xsl:variable name="lang_migrate"><xsl:value-of select="lang_migrate"></xsl:value-of></xsl:variable>
	<tr>
		<td height="50">
			<input type="submit" name="migrate" value="{$lang_migrate}" onMouseout="window.status='';return true;">
				<xsl:attribute name="onMouseover">
					<xsl:text>window.status='</xsl:text>
					<xsl:value-of select="lang_migrate_statustext"></xsl:value-of>
					<xsl:text>'; return true;</xsl:text>
				</xsl:attribute>
			</input>
		</td>
	</tr>
</xsl:template>
