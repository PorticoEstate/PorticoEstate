<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->
        <!--ul class="pathway">
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
        </ul-->
        <xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->

	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
            <input type="hidden" name="tab" value=""/>
            <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="system_message/tabs"/>
                <div id="system_message">
            
			<input name="title" type="hidden" value="{system_message/title}" />
			<input name="message" type="hidden" value="{system_message/message}" />
  			<input name="created" type="hidden" value="{system_message/created}" />
  			<input name="name" type="hidden" value="{system_message/name}" />
  			<input name="phone" type="hidden" value="{system_message/phone}" />
  			<input name="email" type="hidden" value="{system_message/email}" />
  			<input name="status" type="hidden" value="CLOSED" />

                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Created')" /></h4>
                        </label>
                        <xsl:value-of select="system_message/created"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Message')" /></h4>
                        </label>
                        <xsl:value-of select="system_message/message" disable-output-escaping="yes"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Name')" /></h4>
                        </label>
                        <xsl:value-of select="system_message/name"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Phone')" /></h4>
                        </label>
                        <xsl:value-of select="system_message/phone"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Email')" /></h4>
                        </label>
                        <xsl:value-of select="system_message/email"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Type')" /></h4>
                        </label>
                        <xsl:value-of select="system_message/type"/>
                    </div>
                    <div class="pure-control-group">
                        <label>
                            <h4><xsl:value-of select="php:function('lang', 'Status')" /></h4>
                        </label>
                        <xsl:value-of select="system_message/status" />
                    </div>

                </div>
            </div>
            <div class="form-buttons">
                <xsl:if test="system_message/status = php:function('lang','NEW')"><input style="margin-right: 10px;" type="submit" value="{php:function('lang', 'Close')}"/></xsl:if>
                <a class="button">
                    <xsl:attribute name="href"><xsl:value-of select="system_message/back_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Back')" />
                </a>
            </div>
	</form>

    <!---/div-->

</xsl:template>
