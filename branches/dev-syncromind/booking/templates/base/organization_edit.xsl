<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        
	<!--dl class="form">
    	<dt class="heading">
			<xsl:if test="new_form">
				<xsl:value-of select="php:function('lang', 'New Organization')" />
			</xsl:if>
			<xsl:if test="not(new_form)">
				<xsl:value-of select="php:function('lang', 'Edit Organization')" />
			</xsl:if>
		</dt>
	</dl-->
	
    <xsl:call-template name="msgbox"/>
	<!--xsl:call-template name="yui_booking_i18n"/-->

	<form action="" method="POST" class="pure-form pure-form-stacked" id="form" name="form" >
		<input type="hidden" name="tab" value=""/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="organization/tabs"/>
				<div id="generic">
					<fieldset>
						<div class="pure-g">
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label>
									<label for="field_name"><xsl:value-of select="php:function('lang', 'Organization')" /></label>
								</label>
								 <xsl:if test="currentapp = 'booking'">
									<input id="inputs" name="name" type="text">
										<xsl:attribute name="value"><xsl:value-of select="organization/name"/></xsl:attribute>
									</input>
								</xsl:if>
								<xsl:if test="currentapp != 'booking'">
									<input id="inputs" name="name" readonly="true" type="text">
										<xsl:attribute name="value"><xsl:value-of select="organization/name"/></xsl:attribute>
									</input>
								</xsl:if>
							</div>
							<!--2-->
							<div class="pure-u-1 pure-u-md-1-5">
								<xsl:if test="currentapp = 'booking'">
									<xsl:copy-of select="phpgw:booking_customer_identifier(organization)"/>
								</xsl:if>
								<xsl:if test="currentapp != 'booking'">
									<xsl:copy-of select="phpgw:booking_customer_identifier_show(organization)"/>
								</xsl:if>
								 <xsl:if test="currentapp = 'booking'">
									<label for="field_customer_internal"><xsl:value-of select="php:function('lang', 'Internal Customer')"/></label>
									<xsl:copy-of select="phpgw:option_checkbox(organization/customer_internal, 'customer_internal')"/>
								</xsl:if>			
							</div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label>
									<label for="field_shortname"><xsl:value-of select="php:function('lang', 'Organization shortname')" /></label>
								</label>
								<xsl:if test="currentapp = 'booking'">
									<input id="field_shortname" name="shortname" type="text">
										<xsl:attribute name="value"><xsl:value-of select="organization/shortname"/></xsl:attribute>
									</input>
								</xsl:if>
								<xsl:if test="currentapp != 'booking'">
									<input id="field_shortname" name="shortname" readonly="true" type="text">
										<xsl:attribute name="value"><xsl:value-of select="organization/shortname"/></xsl:attribute>
									</input>
								</xsl:if>
							</div>
							<!--2-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label for="field_street"><xsl:value-of select="php:function('lang', 'Street')"/></label>
								<input id="field_street" name="street" type="text" value="{organization/street}"/>
							</div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label>
									<label for="field_organization_number"><xsl:value-of select="php:function('lang', 'Organization number')" /></label>
								</label>
								<xsl:if test="currentapp = 'booking'">
									<input id="field_organization_number" name="organization_number" type="text" value="{organization/organization_number}"/>
								</xsl:if>
								<xsl:if test="currentapp != 'booking'">
									<input id="field_organization_number" name="organization_number" type="text" readonly="true" value="{organization/organization_number}"/>
								</xsl:if>
							</div>
							<!--2-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label for="field_zip_code"><xsl:value-of select="php:function('lang', 'Zip code')"/></label>
								<input type="text" name="zip_code" id="field_zip_code" value="{organization/zip_code}"/>
							</div>	
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label>
									<label for="field_customer_number"><xsl:value-of select="php:function('lang', 'Customer number')" /></label>
								</label>
								<xsl:if test="currentapp = 'booking'">
									<input name="customer_number" type="text" id="field_customer_number" value="{organization/customer_number}"/>
								</xsl:if>
								<xsl:if test="currentapp != 'booking'">
									<input name="customer_number" type="text" id="field_customer_number" readonly="true" value="{organization/customer_number}"/>
								</xsl:if>
							</div>
							<!--2-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label for="field_city"><xsl:value-of select="php:function('lang', 'Postal City')"/></label>
								<input type="text" name="city" id="field_city" value="{organization/city}"/>
							</div>	
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label>
									<label for="field_homepage"><xsl:value-of select="php:function('lang', 'Homepage')" /></label>
								</label>
								<input id="field_homepage" name="homepage" type="text">
									<xsl:attribute name="value"><xsl:value-of select="organization/homepage"/></xsl:attribute>
								</input>
							</div>	
							<!--2-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label for='field_district'><xsl:value-of select="php:function('lang', 'District')"/></label>
								<xsl:if test="currentapp = 'booking'">
									<input type="text" name="district" id="field_district" value="{organization/district}"/>
								</xsl:if>
								<xsl:if test="currentapp != 'booking'">
									<input type="text" name="district" id="field_district" readonly="true" value="{organization/district}"/>
								</xsl:if>
							</div>	
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label>
									<label for="field_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
								</label>
								<input id="field_phone" name="phone" type="text">
									<xsl:attribute name="value"><xsl:value-of select="organization/phone"/></xsl:attribute>
								</input>
							</div>	
							<!--2-->
							<div class="pure-u-1 pure-u-md-1-5">
								<xsl:if test="not(new_form) and (currentapp = 'booking')">
									<label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label>
									<select id="field_active" name="active">
										<option value="1">
										 <xsl:if test="organization/active=1">
											 <xsl:attribute name="selected">checked</xsl:attribute>
										 </xsl:if>
											<xsl:value-of select="php:function('lang', 'Active')"/>
										</option>
										<option value="0">
										 <xsl:if test="organization/active=0">
											 <xsl:attribute name="selected">checked</xsl:attribute>
										 </xsl:if>
											<xsl:value-of select="php:function('lang', 'Inactive')"/>
										</option>
									</select>
								</xsl:if>
							</div>	
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label>
									<label for="field_email"><xsl:value-of select="php:function('lang', 'Email')" /></label>
								</label>
								<input id="field_email" name="email" type="text">
									<xsl:attribute name="value"><xsl:value-of select="organization/email"/></xsl:attribute>
								</input>
							</div>
							<!--2-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label for="field_show_in_portal"><xsl:value-of select="php:function('lang', 'Show in portal')"/></label>
								<select id="field_show_in_portal" name="show_in_portal">
									<option value="0">
									 <xsl:if test="organization/show_in_portal=0">
										 <xsl:attribute name="selected">checked</xsl:attribute>
									 </xsl:if>
										<xsl:value-of select="php:function('lang', 'No')"/>
									</option>
									<option value="1">
									 <xsl:if test="organization/show_in_portal=1">
										 <xsl:attribute name="selected">checked</xsl:attribute>
									 </xsl:if>
										<xsl:value-of select="php:function('lang', 'Yes')"/>
									</option>
								</select>
							</div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label for="field_activity"><xsl:value-of select="php:function('lang', 'Activity')" /></label>
								<select name="activity_id" id="field_activity">
									<option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
									<xsl:for-each select="activities">
										<option>
											<xsl:if test="../organization/activity_id = id">
												<xsl:attribute name="selected">selected</xsl:attribute>
											</xsl:if>
											<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
											<xsl:value-of select="name"/>
										</option>
									</xsl:for-each>
								</select>
							</div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<!--1-->
							<div class="pure-u-1 pure-u-md-1-5">
								<label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label>
								<textarea id="field-description" name="description" type="text"><xsl:value-of select="organization/description"/></textarea>
							</div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							<div class="pure-u-1 pure-u-md-1-5"></div>
							
							<xsl:if test='new_form or organization/permission/write'>
								<div class="pure-u-1 pure-u-md-1-5" style="border-bottom:1px solid #000;">
									<xsl:value-of select="php:function('lang', 'Admin 1')" />
								</div>
								<div class="pure-u-1 pure-u-md-1-5" style="border-bottom:1px solid #000;">
									<xsl:value-of select="php:function('lang', 'Admin 2')" />
								</div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								
								<div class="pure-u-1 pure-u-md-1-5">
									<label for="field_admin_name_1"><xsl:value-of select="php:function('lang', 'Name')" /></label>
									<input type='text' id='field_admin_name_1' name="contacts[0][name]" value='{organization/contacts[1]/name}'/>
									<input type="hidden" name="contacts[0][ssn]" value=""/>
								</div>
								<div class="pure-u-1 pure-u-md-1-5">
									<label for="field_admin_name_2"><xsl:value-of select="php:function('lang', 'Name')" /></label>
									<input type='text' id='field_admin_name_2' name="contacts[1][name]" value='{organization/contacts[2]/name}'/>
									<input type="hidden" name="contacts[1][ssn]" value=""/>
								</div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								
								<div class="pure-u-1 pure-u-md-1-5">
									<label for="field_admin_email_1"><xsl:value-of select="php:function('lang', 'Email')" /></label>
									<input type='text' id='field_admin_email_1' name="contacts[0][email]" value='{organization/contacts[1]/email}'/>
								</div>
								<div class="pure-u-1 pure-u-md-1-5">
									<label for="field_admin_email_2"><xsl:value-of select="php:function('lang', 'Email')" /></label>
									<input type='text' id='field_admin_email_2' name="contacts[1][email]" value='{organization/contacts[2]/email}'/>
								</div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								
								<div class="pure-u-1 pure-u-md-1-5">
									<label for="field_admin_phone_1"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
									<input type='text' id='field_admin_phone_1' name="contacts[0][phone]" value='{organization/contacts[1]/phone}'/>
								</div>
								<div class="pure-u-1 pure-u-md-1-5">
									<label for="field_admin_phone_2"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
									<input type='text' id='field_admin_phone_2' name="contacts[1][phone]" value='{organization/contacts[2]/phone}'/>
								</div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								<div class="pure-u-1 pure-u-md-1-5"></div>
								
							</xsl:if>
						</div>
					</fieldset>
				</div>
			</div>



<script type="text/javascript">
var endpoint = '<xsl:value-of select="module" />';
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
</script>
        <div class="form-buttons">
            <input type="submit" class="pure-button pure-button-primary">
				<xsl:if test="new_form">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')" /></xsl:attribute>
				</xsl:if>
				<xsl:if test="not(new_form)">
					<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Save')" /></xsl:attribute>
				</xsl:if>
			</input>
            <a class="cancel">
                <xsl:attribute name="href"><xsl:value-of select="organization/cancel_link"/></xsl:attribute>
                <xsl:value-of select="php:function('lang', 'Cancel')" />
            </a>
        </div>
    </form>
    </div>

</xsl:template>


