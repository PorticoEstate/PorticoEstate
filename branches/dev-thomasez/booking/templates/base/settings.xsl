<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

   	<dl class="form">
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking system settings')"/></dt>
   	</dl>

    <form action="" method="POST">

       <dl class="form-col">
            <dt><label for="field_layout_settings"><xsl:value-of select="php:function('lang', 'Layout')"/></label></dt>
			<dd>
				<select id="field_layout_settings" name="layout_settings">
                    <option value="bergen">
                        <xsl:if test="config_data/layout_settings='bergen'">
                            <xsl:attribute name="selected">checked</xsl:attribute>
                        </xsl:if>
						Bergen Kommune
                    </option>
                    <option value="nsf">
                        <xsl:if test="config_data/layout_settings='nsf'">
                            <xsl:attribute name="selected">checked</xsl:attribute>
                        </xsl:if>
						Norsk Speider forbund
		           </option>
		        </select>
			</dd>
        </dl>
		<div class="form-buttons">
			<input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
		</div>
    </form>
    </div>
</xsl:template>
