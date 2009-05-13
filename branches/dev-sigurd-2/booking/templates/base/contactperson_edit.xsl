<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="person/contactpersons_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Contacts')" />
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Contact')" /></li>
            <li>
                <a href="">
                    <xsl:value-of select="person/name"/>
                </a>
            </li>
        </ul>

    <xsl:call-template name="msgbox"/>

    <form action="" method="POST">
        <dl class="form">
            <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
            <dd>
                <input name="name" type="text">
                     <xsl:attribute name="value"><xsl:value-of select="person/name"/></xsl:attribute>
                </input>
            </dd>

            <dt><label for="field_ssn"><xsl:value-of select="php:function('lang', 'Social Security Number')" /></label></dt>
            <dd>
                <input id="field-ssn" name="ssn" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="person/ssn"/></xsl:attribute>
                </input>
            </dd>

            <dt><label for="field_homepage"><xsl:value-of select="php:function('lang', 'Homepage')" /></label></dt>
            <dd>
                <input id="field-homepage" name="homepage" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="person/homepage"/></xsl:attribute>
                </input>
            </dd>

            <dt><label for="field_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label></dt>
            <dd>
                <input id="field-phone" name="phone" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="person/phone"/></xsl:attribute>
                </input>
            </dd>

            <dt><label for="field_email"><xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
            <dd>
                <input id="field-email" name="email" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="person/email"/></xsl:attribute>
                </input>
            </dd>

            <dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
            <dd class="yui-skin-sam">
                <textarea id="field-description" name="description" type="text"><xsl:value-of select="person/description"/></textarea>
            </dd>

        </dl>
        <div class="form-buttons">
            <input type="submit">
                <xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="booking/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>

<script type="text/javascript">
<![CDATA[
var descEdit = new YAHOO.widget.SimpleEditor('field-description', {
    height: '300px',
    width: '522px',
    dompath: true,
    animate: true,
	handleSubmit: true
});
descEdit.render();
]]>
</script>
</xsl:template>

