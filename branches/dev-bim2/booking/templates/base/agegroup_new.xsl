<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
    <h3><xsl:value-of select="php:function('lang', 'Age group')" /></h3>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>
    <form action="" method="POST">
    
    
        <dl class="form-col">
            <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
            <dd>
                <input id="field_name" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="agegroup/name"/></xsl:attribute>
                </input>
            </dd>

            <dt><label for="field_sort"><xsl:value-of select="php:function('lang', 'Sort order')" /></label></dt>
            <dd>
                <input id="field_sort" name="sort" type="text" value="{agegroup/sort}"/>
            </dd>

            <dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
            <dd>
                <textarea cols="5" rows="5" id="field_description" name="description"><xsl:value-of select="agegroup/description"/></textarea>
            </dd>
        </dl>



        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')" /></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="agegroup/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>
</xsl:template>
