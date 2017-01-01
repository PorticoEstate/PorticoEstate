
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
	<div class='body'>
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
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="values[save]">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')"/>
					</xsl:attribute>
				</input>
			</div>

			<fieldset>
				<legend>
					<xsl:value-of select="lang_columns"/>
				</legend>
				<div class="pure-g">
					<xsl:apply-templates select="column_list"/>
				</div>
			</fieldset>
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
