<xsl:template match="confirm">
	<dl>
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dt>
					<xsl:call-template name="msgbox"/>
				</dt>
			</xsl:when>
		</xsl:choose>
	</dl>
	<h2>
		<xsl:value-of select="lang_confirm_msg"/>
	</h2>
	<form method="POST" action="{update_action}" class="pure-form pure-form-aligned">
		<input type="submit" class="pure-button pure-button-primary" name="confirm" value="{lang_yes}">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_yes_statustext"/>
			</xsl:attribute>
		</input>
		<a href="{done_action}" class="pure-button pure-button-primary">
			<xsl:attribute name="title">
				<xsl:value-of select="lang_no_statustext"/>
			</xsl:attribute>
			<xsl:value-of select="lang_no"/>
		</a>
	</form>
</xsl:template>
