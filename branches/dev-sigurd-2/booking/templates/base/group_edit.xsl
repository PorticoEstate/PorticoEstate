<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Organization')" />
                </a>
            </li>
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="group/organization_link"/></xsl:attribute>
                    <xsl:value-of select="group/organization_name"/>
                </a>
            </li>
            <li><xsl:value-of select="php:function('lang', 'Group')" /></li>
            <li>
                <a href="">
                    <xsl:value-of select="group/name"/>
                </a>
            </li>
        </ul>

    <xsl:call-template name="msgbox"/>

    <form action="" method="POST">
        <ul>
            <li style="display: inline; float: left;">
                <dl class="form">
                    <dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
                    <dd>
                        <input name="name" type="text">
                            <xsl:attribute name="value"><xsl:value-of select="group/name"/></xsl:attribute>
                        </input>
                    </dd>

                    <dt><label for="field_organization"><xsl:value-of select="php:function('lang', 'Organization')" /></label></dt>
                    <dd>
                        <div class="autocomplete">
                            <input id="field_organization_id" name="organization_id" type="hidden">
                                <xsl:attribute name="value"><xsl:value-of select="group/organization_id"/></xsl:attribute>
                            </input>
                            <input name="organization_name" type="text" id="field_organization_name" >
                                <xsl:attribute name="value"><xsl:value-of select="group/organization_name"/></xsl:attribute>
                            </input>
                            <div id="organization_container"/>
                        </div>
                    </dd>

                    <dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
                    <dd class="yui-skin-sam">
                        <textarea id="field-description" name="description" type="text"><xsl:value-of select="group/description"/></textarea>
                    </dd>
                </dl>
            </li>
            <li style="display: inline; float: left;">
                <dl class="form">
                    <dt>
                        <label for="field_contact_primary"><xsl:value-of select="php:function('lang', 'Primary contact')" /></label><br />
                        <a href="#" onclick="return createContact();">(<xsl:value-of select="php:function('lang', 'Create a new contact')" />)</a>
                    </dt>
                    <dd>
                        <ul>
                            <li style="float: left">
                                <div class="autocomplete">
                                    <input id="field_contact_primary" name="contact_primary" type="hidden">
                                        <xsl:attribute name="value"><xsl:value-of select="group/contact_primary/id"/></xsl:attribute>
                                    </input>
                                    <input name="contact_primary_name" type="text" id="field_contact_primary_name" >
                                        <xsl:attribute name="value"><xsl:value-of select="group/contact_primary/name"/></xsl:attribute>
                                    </input>
                                    <div id="primary_contact_container"/>
                                </div>
                            </li>
                            <li style="float: left; margin-left: 10px">
                                <a href="#" onclick="return editContact('field_contact_primary');">Edit</a>
                            </li>
                        </ul>
                    </dd>

                    <dt><label for="field_contact_secondary"><xsl:value-of select="php:function('lang', 'Secondary contact')" /></label></dt>
                    <dd>
                        <ul>
                            <li style="float: left">
                                <div class="autocomplete">
                                    <input id="field_contact_secondary" name="contact_secondary" type="hidden">
                                        <xsl:attribute name="value"><xsl:value-of select="group/contact_secondary/id"/></xsl:attribute>
                                    </input>
                                    <input name="contact_secondary_name" type="text" id="field_contact_secondary_name" >
                                        <xsl:attribute name="value"><xsl:value-of select="group/contact_secondary/name"/></xsl:attribute>
                                    </input>
                                    <div id="secondary_contact_container"/>
                                </div>
                            </li>
                            <li style="float: left; margin-left: 10px">
                                <a href="#" onclick="return editContact('field_contact_secondary');">Edit</a>
                            </li>
                        </ul>
                    </dd>
                </dl>
            </li>
        </ul>
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

    <xsl:call-template name="contactpersonmagic" />

<script type="text/javascript">
var endpoint = '<xsl:value-of select="module" />';
<![CDATA[
var descEdit = new YAHOO.widget.SimpleEditor('field-description', {
    height: '300px',
    width: '522px',
    dompath: true,
    animate: true,
	handleSubmit: true
});
descEdit.render();

// Autocomplete primary contact
YAHOO.booking.autocompleteHelper('index.php?menuaction=' + endpoint + '.uicontactperson.index&phpgw_return_as=json&', 
    'field_contact_primary_name',
    'field_contact_primary',
    'primary_contact_container'
);
// Autocomplete secondary contact
YAHOO.booking.autocompleteHelper('index.php?menuaction=' + endpoint + '.uicontactperson.index&phpgw_return_as=json&', 
    'field_contact_secondary_name',
    'field_contact_secondary',
    'secondary_contact_container'
);
YAHOO.booking.autocompleteHelper('index.php?menuaction=' + endpoint + '.uiorganization.index&phpgw_return_as=json&',
    'field_organization_name',
    'field_organization_id',
    'organization_container'
);
]]>
</script>
</xsl:template>

