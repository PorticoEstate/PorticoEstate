<!-- $Id: app_delete.xsl 16579 2006-03-26 20:00:56Z sigurdne $ -->

	<xsl:template name="confirm_delete">
		<xsl:apply-templates select="delete"/>
	</xsl:template>

	<xsl:template match="delete">
			<h1><xsl:value-of select="lang_confirm_msg"/></h1>
			<div class="button_group">
				<form method="post" action="{form_action}">
					<input type="submit" name="cancel" value="{lang_no}" />

					<input type="submit" name="confirm" value="{lang_yes}"/>
				</form>
			</div>
	</xsl:template>
