<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
			<li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="system_message/system_messages_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'System messages')" />
                </a>
			</li>
            <li>
                <a href="">
                    <xsl:value-of select="system_message/title"/>
                </a>
            </li>
        </ul>
        <xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

	<form action="" method="POST">
			<input name="title" type="hidden" value="{system_message/title}" />
			<input name="message" type="hidden" value="{system_message/message}" />
  			<input name="created" type="hidden" value="{system_message/created}" />
  			<input name="name" type="hidden" value="{system_message/name}" />
  			<input name="phone" type="hidden" value="{system_message/phone}" />
  			<input name="email" type="hidden" value="{system_message/email}" />
  			<input name="status" type="hidden" value="CLOSED" />

        <dl class="proplist">
            <dt><xsl:value-of select="php:function('lang', 'Created')" /></dt>
            <dd><xsl:value-of select="system_message/created"/></dd>

            <dt><xsl:value-of select="php:function('lang', 'Message')" /></dt>
            <dd><xsl:value-of select="system_message/message" disable-output-escaping="yes"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
            <dd><xsl:value-of select="system_message/name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Phone')" /></dt>
            <dd><xsl:value-of select="system_message/phone"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Email')" /></dt>
            <dd><xsl:value-of select="system_message/email"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Type')" /></dt>
            <dd><xsl:value-of select="system_message/type"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Status')" /></dt>
            <dd><xsl:value-of select="system_message/status" /></dd>

        </dl>

		<div class="form-buttons">
		<xsl:if test="system_message/status = php:function('lang','NEW')"><input style="margin-right: 10px;" type="submit" value="{php:function('lang', 'Close')}"/></xsl:if>
        <a class="button">
            <xsl:attribute name="href"><xsl:value-of select="system_message/back_link"/></xsl:attribute>
            <xsl:value-of select="php:function('lang', 'Back')" />
        </a>
		</div>
	</form>

    </div>

</xsl:template>
