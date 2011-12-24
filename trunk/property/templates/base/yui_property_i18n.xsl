<!-- $Id$ -->
	<xsl:template xmlns:php="http://php.net/xsl" name="yui_property_i18n">
		<xsl:if test="yui_property_i18n">
			<script type="text/javascript">
				YAHOO.booking.i18n = {};
				<xsl:for-each select="yui_property_i18n/*">
					YAHOO.booking.i18n.<xsl:value-of select="local-name()"/> = function(cfg)
					{
						cfg = cfg || {};
						<xsl:for-each select="./*">
							cfg["<xsl:value-of select="local-name()"/>"] = <xsl:value-of disable-output-escaping="yes" select="."/>;
						</xsl:for-each>
					};
				</xsl:for-each>
			</script>
		</xsl:if>
	</xsl:template>
