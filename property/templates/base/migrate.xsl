
<!-- $Id$ -->
<xsl:template name="app_data">
	<xsl:apply-templates select="list"/>
</xsl:template>

<!-- New template-->
<xsl:template match="list">
	<xsl:variable name="migrate_action">
		<xsl:value-of select="migrate_action"/>
	</xsl:variable>
	<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
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
			<td colspan="3" width="100%">
				<xsl:call-template name="nextmatchs"/>
			</td>
		</tr>
	</table>
	<form method="post" action="{$migrate_action}">
		<table class="pure-table pure-table-bordered" border="0" cellspacing="2" cellpadding="2">
			<thead>
				<xsl:apply-templates select="table_header"/>
			</thead>
			<tbody>
				<xsl:choose>
					<xsl:when test="values != ''">
						<xsl:apply-templates select="values"/>
					</xsl:when>
				</xsl:choose>
				<xsl:apply-templates select="table_migrate"/>
			</tbody>
		</table>
	</form>
</xsl:template>

<!-- New template-->
<xsl:template match="table_header">
	<xsl:variable name="sort_domain">
		<xsl:value-of select="sort_domain"/>
	</xsl:variable>
	<tr class="th">
		<td class="th_text" width="10%" align="left">
			<a href="{$sort_domain}">
				<xsl:value-of select="lang_domain"/>
			</a>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_db_host"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_db_name"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_db_type"/>
		</td>
		<td class="th_text" width="5%" align="center">
			<xsl:value-of select="lang_select"/>
		</td>
	</tr>
</xsl:template>

<!-- New template-->
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
			<xsl:value-of select="domain"/>
		</td>
		<td align="left">
			<xsl:value-of select="db_host"/>
		</td>
		<td align="left">
			<xsl:value-of select="db_name"/>
		</td>
		<td align="left">
			<xsl:value-of select="db_type"/>
		</td>
		<xsl:choose>
			<xsl:when test="lang_select_migrate_text != ''">
				<td align="center" title="{lang_select_migrate_text}">
					<input type="radio" name="values[]" value="{domain}">
					</input>
				</td>
			</xsl:when>
		</xsl:choose>
	</tr>
</xsl:template>

<!-- New template-->
<xsl:template match="table_migrate">
	<xsl:variable name="lang_migrate">
		<xsl:value-of select="lang_migrate"/>
	</xsl:variable>
	<tr>
		<td height="50">
			<button onclick="return confirm('{php:function('lang', 'Are you really sure?')}')" type="submit" name="migrate" value="1" class="pure-button pure-button-primary">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_migrate_statustext"/>
				</xsl:attribute>
					<xsl:value-of select="lang_migrate"/>
			</button>
		</td>
	</tr>
</xsl:template>
