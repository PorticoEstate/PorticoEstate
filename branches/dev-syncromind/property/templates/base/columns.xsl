
<!-- $Id$ -->
<xsl:template name="app_data">
	<xsl:choose>
		<xsl:when test="columns">
			<xsl:apply-templates select="columns"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template match="columns">
	<div align="left">
		<form method="post" name="form" action="{form_action}" class= "pure-form pure-form-aligned">
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
			</table>

			<fieldset>
				<legend>
							<xsl:value-of select="lang_columns"/>
				</legend>
				<div class="pure-g">
				<xsl:apply-templates select="column_list"/>
				</div>
			</fieldset>

				<tr height="50">
					<td>
						<xsl:variable name="lang_save">
							<xsl:value-of select="lang_save"/>
						</xsl:variable>
						<input type="submit" name="values[save]" value="{$lang_save}">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_save_statustext"/>
							</xsl:attribute>
						</input>
					</td>
				</tr>
			</form>
	</div>
</xsl:template>

<!-- New template-->
<xsl:template match="column_list">
	<xsl:variable name="id">
		<xsl:value-of select="id"/>
	</xsl:variable>
	<div class="pure-u-1 pure-u-sm-1-3">
		<div class="pure-u-1">
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="name"/>
				</label>
			<xsl:choose>
				<xsl:when test="selected">
					<input id="column{$id}" name="values[columns][]" value="{$id}" checked="checked" type="checkbox"/>
				</xsl:when>
				<xsl:otherwise>
					<input id="column{$id}" name="values[columns][]" value="{$id}" type="checkbox"/>
				</xsl:otherwise>
			</xsl:choose>
			</div>
		</div>
	</div>
</xsl:template>
