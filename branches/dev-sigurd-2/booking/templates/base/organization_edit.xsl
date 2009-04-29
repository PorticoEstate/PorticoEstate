<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        
    <h3><xsl:value-of select="php:function('lang', 'Edit Organization')" /></h3>
    <xsl:call-template name="msgbox"/>

    <form action="" method="POST">
        <dl class="form">
            <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
            <dd>
                <input id="inputs" name="name" type="text">
                    <xsl:attribute name="value"><xsl:value-of select="organization/name"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_homepage"><xsl:value-of select="php:function('lang', 'Homepage')" /></label></dt>
            <dd>
                <input id="field_homepage" name="homepage" type="text">
                       <xsl:attribute name="value"><xsl:value-of select="organization/homepage"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label></dt>
            <dd>
                <input id="field_phone" name="phone" type="text">
                       <xsl:attribute name="value"><xsl:value-of select="organization/phone"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_email"><xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
            <dd>
                <input id="field_email" name="email" type="text">
                       <xsl:attribute name="value"><xsl:value-of select="organization/email"/></xsl:attribute>
                </input>
            </dd>
            <dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
            <dd class="yui-skin-sam">
                <textarea id="field-description" name="description" type="text"><xsl:value-of select="organization/description"/></textarea>
            </dd>
        </dl>
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
        <div class="form-buttons">
            <input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="save_or_create_text"/></xsl:attribute>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="organization/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>
</xsl:template>


