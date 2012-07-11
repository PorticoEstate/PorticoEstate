<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

    <form action="" method="POST">
		<input type="hidden" name="step" value="{step}"/>
		<input type="hidden" name="seasons" value="{season}"/>
		<input type="hidden" name="building_id" value="{building}"/>
		<input type="hidden" name="mailbody" value="{mailbody}"/>
		<input type="hidden" name="mailsubject" value="{mailsubject}"/>

		<dl class="form-col">
			<dt><label><xsl:value-of select="php:function('lang', 'Recipients')"/> - (<xsl:value-of select="count(contacts)" />)</label></dt>
			<dd>
				<select id="field_contacts" name="contacts" size="10">
					<xsl:for-each select="contacts">
						<xsl:sort select="name"/>
						<option>
							<xsl:attribute name="value"><xsl:value-of select="email"/></xsl:attribute>
							<xsl:value-of select="name"/> &lt;<xsl:value-of select="email"/>&gt;
						</option>
					</xsl:for-each>
				</select>
			</dd>
		</dl>
		<div class="form-buttons">
			<input type="submit" name="sendmail">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Send e-mails')"/></xsl:attribute>
			</input>
		</div>
    </form>
    </div>
</xsl:template>
