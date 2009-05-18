<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
       <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/buildings_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Buildings')" />
				</a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="building/building_link"/></xsl:attribute>
                    <xsl:value-of select="building/name"/>
                </a>
            </li>
        </ul>

    <xsl:call-template name="msgbox"/>

    <form action="" method="POST">
        <dl class="form">
            <dt><label for="field_name">
                    <xsl:value-of select="php:function('lang', 'Activity')" /></label></dt>
            <dd>
                <input name="name" type="text">
                    <xsl:attribute id="field_name" name="value"><xsl:value-of select="building/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_description">
                    <xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
            <dd>
                <textarea class="full-width" id="field_description" name="description"><xsl:value-of select="building/description"/></textarea>
            </dd>
        </dl>
        <dl class="form-col">
            <dt><label for="field_phone">
                    <xsl:value-of select="php:function('lang', 'Telephone')" /></label></dt>
            <dd>
                <input id="field_phone" name="phone" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="building/phone"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_email">
                    <xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
            <dd>
                <input id="field_email" name="email" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="building/email"/></xsl:attribute>
                </input>
            </dd>
        </dl>
        <dl class="form-col">
            <dt><label for="homepage">
                    <xsl:value-of select="php:function('lang', 'Homepage')" /></label></dt>
            <dd>
                <input name="homepage" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="building/homepage"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_address"><xsl:value-of select="php:function('lang', 'Street')"/></label></dt>
            <dd>
                <input id="field_address" name="address" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="building/address"/></xsl:attribute>
				</input>
            </dd>
            <dt><label for="field_address"><xsl:value-of select="php:function('lang', 'Zip code')"/></label></dt>
            <dd><input type="text"/></dd>
            <dt><label><xsl:value-of select="php:function('lang', 'City')"/></label></dt>
            <dd><input type="text"/></dd>
            <dt><label><xsl:value-of select="php:function('lang', 'District')"/></label></dt>
            <dd><input type="text"/></dd>
        </dl>
        <div class="clr"/>
        <div class="form-buttons">
            <input type="submit">
			<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="building/cancel_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>
</xsl:template>
