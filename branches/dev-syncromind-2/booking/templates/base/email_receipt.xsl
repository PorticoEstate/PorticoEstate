<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">

		<xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->

		<p>
			<xsl:value-of select="php:function('lang', '%1 e-mails were sent successfully', ok_count)" />
		</p>
		<xsl:if test="fail_count &gt; 0">
			<p>
				<xsl:value-of select="php:function('lang', '%1 e-mails were not sent successfully', fail_count)" />
			</p>
		</xsl:if>
	</div>
</xsl:template>
