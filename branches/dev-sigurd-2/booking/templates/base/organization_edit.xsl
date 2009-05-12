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

            <dt><label for="field_admin_primary"><xsl:value-of select="php:function('lang', 'Primary Admin')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_admin_primary" name="admin_primary" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="organization/admin_primary/id"/></xsl:attribute>
                    </input>
                    <input name="admin_primary_name" type="text" id="field_admin_primary_name" >
                        <xsl:attribute name="value"><xsl:value-of select="organization/admin_primary/name"/></xsl:attribute>
                    </input>
                    <div id="primary_admin_container"/>
                </div>
            </dd>

            <dt><label for="field_admin_secondary"><xsl:value-of select="php:function('lang', 'Secondary Admin')" /></label></dt>
            <dd>
                <div class="autocomplete">
                    <input id="field_admin_secondary" name="admin_secondary" type="hidden">
                        <xsl:attribute name="value"><xsl:value-of select="organization/admin_secondary/id"/></xsl:attribute>
                    </input>
                    <input name="admin_secondary_name" type="text" id="field_admin_secondary_name" >
                        <xsl:attribute name="value"><xsl:value-of select="organization/admin_secondary/name"/></xsl:attribute>
                    </input>
                    <div id="secondary_admin_container"/>
                </div>
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

// Autocomplete primary admin
YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uicontactperson.index&phpgw_return_as=json&', 
    'field_admin_primary_name',
    'field_admin_primary',
    'primary_admin_container'
);
// Autocomplete secondary contact
YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uicontactperson.index&phpgw_return_as=json&', 
    'field_admin_secondary_name',
    'field_admin_secondary',
    'secondary_admin_container'
);
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


