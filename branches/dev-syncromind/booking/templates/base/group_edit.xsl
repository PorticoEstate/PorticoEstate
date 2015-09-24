<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <!--div id="content"-->

	<!--dl class="form">
    	<dt class="heading">
			<xsl:if test="not(group/id)">
				<xsl:value-of select="php:function('lang', 'New Group')" />
			</xsl:if>
			<xsl:if test="group/id">
				<xsl:value-of select="php:function('lang', 'Edit Group')" />
			</xsl:if>
		</dt>
	</dl-->

    <xsl:call-template name="msgbox"/>
    <!--xsl:call-template name="yui_booking_i18n"/-->
    <form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
        <input type="hidden" name="tab" value=""/>
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="group/tabs"/>
            <div id="group_edit"> 
                <fieldset>
                    <div class="heading">
                        <legend><h3>
                            <xsl:if test="not(group/id)">
                                <xsl:value-of select="php:function('lang', 'New Group')" />
                            </xsl:if>
                            <xsl:if test="group/id">
                                <xsl:value-of select="php:function('lang', 'Edit Group')" />
                            </xsl:if>
                        </h3></legend>
                    </div>                
                    <div class="pure-control-group">
                        <label for="name">
                            <h4><xsl:value-of select="php:function('lang', 'Group')" /></h4>
                        </label>
                        <input id="name" name="name" type="text" value="{group/name}" />
                    </div>
                    <div class="pure-control-group">
                        <label for="shortname">
                            <h4><xsl:value-of select="php:function('lang', 'Group shortname')" /></h4>
                        </label>
                        <input id="shortname" name="shortname" type="text" value="{group/shortname}" />
                    </div>
                    <div class="pure-control-group">
                        <label for="field_organization_name">
                            <h4><xsl:value-of select="php:function('lang', 'Organization')" /></h4>
                        </label>
                        <!--div class="autocomplete"-->
                        <input id="field_organization_id" name="organization_id" type="hidden" value="{group/organization_id}"/>
                        <input name="organization_name" type="text" id="field_organization_name" value="{group/organization_name}">
                            <xsl:if test="group/organization_id">
                                <xsl:attribute name='disabled'>disabled</xsl:attribute>
                            </xsl:if>
                        </input>
                        <div id="organization_container"></div>
                        <!--/div-->
                    </div>
                    <div class="pure-control-group">
                        <label for="field_activity">
                            <h4><xsl:value-of select="php:function('lang', 'Activity')" /></h4>
                        </label>
                        <select name="activity_id" id="field_activity">
                            <option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
                            <xsl:for-each select="activities">
                                <option>
                                    <xsl:if test="../group/activity_id = id">
                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                    </xsl:if>
                                    <xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
                                    <xsl:value-of select="name"/>
                                </option>
                            </xsl:for-each>
                        </select>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_description" style="vertical-align:top;">
                            <h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
                        </label>
                        <div style="display:inline-block;max-width:80%;">
                            <textarea id="field_description" name="description" type="text"><xsl:value-of select="group/description"/></textarea>
                        </div>
                    </div>
                    <div class="pure-control-group">
                        <xsl:if test="group/id">
                            <label for="field_active">
                                <h4><xsl:value-of select="php:function('lang', 'Active')"/></h4>
                            </label>
                            <select id="field_active" name="active">
                                <option value="1">
                                    <xsl:if test="group/active=1">
                                        <xsl:attribute name="selected">checked</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="php:function('lang', 'Active')"/>
                                </option>
                                <option value="0">
                                    <xsl:if test="group/active=0">
                                        <xsl:attribute name="selected">checked</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="php:function('lang', 'Inactive')"/>
                                </option>
                            </select>
                        </xsl:if>
                    </div>
                    <div class="pure-control-group">
                        <!--<xsl:if test="not(new_form) and (currentapp = 'booking')">-->
                        <label for="field_show_in_portal">
                            <h4><xsl:value-of select="php:function('lang', 'Show in portal')"/></h4>
                        </label>
                        <select id="field_show_in_portal" name="show_in_portal">
                            <option value="0">
                                <xsl:if test="group/show_in_portal=0">
                                   <xsl:attribute name="selected">checked</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="php:function('lang', 'No')"/>
                            </option>
                            <option value="1">
                                <xsl:if test="group/show_in_portal=1">
                                   <xsl:attribute name="selected">checked</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="php:function('lang', 'Yes')"/>
                            </option>
                        </select>
                        <!--</xsl:if>-->
                    </div>
                    <div class="heading">
                        <legend><h3><xsl:value-of select="php:function('lang', 'Team leader 1')" /></h3></legend>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_admin_name_1">
                            <h4><xsl:value-of select="php:function('lang', 'Name')" /></h4>
                        </label>
                        <input type='text' id='field_admin_name_1' name="contacts[0][name]" value='{group/contacts[1]/name}'/>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_admin_email_1">
                            <h4><xsl:value-of select="php:function('lang', 'Email')" /></h4>
                        </label>
                        <input type='text' id='field_admin_email_1' name="contacts[0][email]" value='{group/contacts[1]/email}'/>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_admin_phone_1">
                            <h4><xsl:value-of select="php:function('lang', 'Phone')" /></h4>
                        </label>
                        <input type='text' id='field_admin_phone_1' name="contacts[0][phone]" value='{group/contacts[1]/phone}'/>
                    </div>
                    <div class="heading">
                        <legend><h3><xsl:value-of select="php:function('lang', 'Team leader 2')" /></h3></legend>
                    </div>  
                    <div class="pure-control-group">
                        <label for="field_admin_name_2">
                            <h4><xsl:value-of select="php:function('lang', 'Name')" /></h4>
                        </label>
                        <input type='text' id='field_admin_name_2' name="contacts[1][name]" value='{group/contacts[2]/name}'/>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_admin_email_2">
                            <h4><xsl:value-of select="php:function('lang', 'Email')" /></h4>
                        </label>
                        <input type='text' id='field_admin_email_2' name="contacts[1][email]" value='{group/contacts[2]/email}'/>
                    </div>
                    <div class="pure-control-group">
                        <label for="field_admin_phone_2">
                            <h4><xsl:value-of select="php:function('lang', 'Phone')" /></h4>
                        </label>
                        <input type='text' id='field_admin_phone_2' name="contacts[1][phone]" value='{group/contacts[2]/phone}'/>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="form-buttons">
            <xsl:if test="not(group/id)"><input type="submit" value="{php:function('lang', 'Add')}" class="button pure-button pure-button-primary" /></xsl:if>
            <xsl:if test="group/id"><input type="submit" value="{php:function('lang', 'Save')}" class="button pure-button pure-button-primary"/></xsl:if>
            <a class="cancel" href="{group/cancel_link}">
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
<!--/div-->

<script type="text/javascript">
var endpoint = '<xsl:value-of select="module" />';
/*
<![CDATA[
var descEdit = new YAHOO.widget.SimpleEditor('field-description', {
    height: '300px',
    width: '522px',
    dompath: true,
    animate: true,
	handleSubmit: true,
        toolbar: {
            titlebar: '',
            buttons: [
               { group: 'textstyle', label: ' ',
                    buttons: [
                        { type: 'push', label: 'Bold', value: 'bold' },
                        { type: 'separator' },
                        { type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink'}
                    ]
                }
            ]
        }
});
descEdit.render();
]]>
*/

<![CDATA[
$(document).ready(function() {
    JqueryPortico.autocompleteHelper('index.php?menuaction=' + endpoint + '.uiorganization.index&phpgw_return_as=json&', 
                                                  'field_organization_name', 'field_organization_id', 'organization_container');    
});
]]>
</script>
</xsl:template>

