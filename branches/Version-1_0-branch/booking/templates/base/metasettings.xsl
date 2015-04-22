<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

   	<dl class="form">
   		<dt class="heading"><xsl:value-of select="php:function('lang', 'Booking system settings')"/></dt>
   	</dl>

    <form action="" method="POST">

       <dl class="form-col">
            <dt><label for="field_metatag_author"><xsl:value-of select="php:function('lang', 'Author')"/></label></dt>
            <dd>
				<input id="field_metatag_author" name="metatag_author" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/metatag_author"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_metatag_robots"><xsl:value-of select="php:function('lang', 'Robots')"/></label></dt>
            <dd>
				<input id="field_metatag_robots" name="metatag_robots" type="text" size="50">
					<xsl:attribute name="value"><xsl:value-of select="config_data/metatag_robots"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_frontpagetext"><xsl:value-of select="php:function('lang', 'Frontpage text')"/></label></dt>
			<dd>
				<textarea id="field_frontpagetext" class="full-width" name="frontpagetext"><xsl:value-of select="config_data/frontpagetext"/></textarea>
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








