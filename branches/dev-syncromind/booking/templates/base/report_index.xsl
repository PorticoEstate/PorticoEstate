<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">

		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

		<dl class="form">
			<dt class="heading">
				<xsl:value-of select="php:function('lang', 'Reports')" />
			</dt>
		</dl>

		<ul>
			<xsl:for-each select="reports">
				<li>
					<a class="cancel">
						<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
						<xsl:value-of select="name" />
					</a>
				</li>
			</xsl:for-each>
		</ul>

	</div>
</xsl:template>
