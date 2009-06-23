<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        
	<dl class="form">
    	<dt class="heading">
			<xsl:if test="new_form">
				<xsl:value-of select="php:function('lang', 'New Organization')" />
			</xsl:if>
			<xsl:if test="not(new_form)">
				<xsl:value-of select="php:function('lang', 'Edit Organization')" />
			</xsl:if>
		</dt>
	</dl>
	
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<form action="" method="POST">
		<dl class="form-col">
			<dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Organization')" /></label></dt>
			<dd>
			    <input id="inputs" name="name" type="text">
			        <xsl:attribute name="value"><xsl:value-of select="organization/name"/></xsl:attribute>
			    </input>
			</dd>
			<dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Organization number')" /></label></dt>
			<dd>
			    <input type="text"/>
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
		<dl class="form-col">
			<dt><label for="field_street"><xsl:value-of select="php:function('lang', 'Street')"/></label></dt>
			<dd><input id="field_street" name="street" type="text" value="{organization/street}"/></dd>

			<dt><label for="field_zip_code"><xsl:value-of select="php:function('lang', 'Zip code')"/></label></dt>
			<dd><input type="text" name="zip_code" id="field_zip_code" value="{organization/zip_code}"/></dd>

			<dt><label for="field_city"><xsl:value-of select="php:function('lang', 'City')"/></label></dt>
			<dd><input type="text" name="city" id="field_city" value="{organization/city}"/></dd>

			<dt><label for='field_district'><xsl:value-of select="php:function('lang', 'District')"/></label></dt>
			<dd><input type="text" name="district" id="field_district" value="{organization/district}"/></dd>

			<xsl:if test="not(new_form)">
			<dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
			<dd>
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
			</dd>
			</xsl:if>
		</dl>

		<div style='clear:left; padding:0; margin:0'/>
		
		<xsl:if test='new_form or organization/permission/write/contacts'>
		
			<dl class="form-col" style='margin-top:0'>
				<dt class='heading'><xsl:value-of select="php:function('lang', 'Admin 1')" /></dt>
			
				<dt><label for="field_admin_name_1"><xsl:value-of select="php:function('lang', 'Name')" /></label><br /></dt>
				<dd><input type='text' id='field_admin_name_1' name="contacts[0][name]" value='{organization/contacts[1]/name}'/></dd>
			
				<dt><label for="field_admin_ssn_1"><xsl:value-of select="php:function('lang', 'Social Security Number')" /></label><br /></dt>
				<dd><input type='text' id='field_admin_ssn_1' name="contacts[0][ssn]" value='{organization/contacts[1]/ssn}'/></dd>
			
				<dt><label for="field_admin_email_1"><xsl:value-of select="php:function('lang', 'Email')" /></label><br /></dt>
				<dd><input type='text' id='field_admin_email_1' name="contacts[0][email]" value='{organization/contacts[1]/email}'/></dd>
			
				<dt><label for="field_admin_phone_1"><xsl:value-of select="php:function('lang', 'Phone')" /></label><br /></dt>
				<dd><input type='text' id='field_admin_phone_1' name="contacts[0][phone]" value='{organization/contacts[1]/phone}'/></dd>
			</dl>

			<dl class="form-col" style='margin-top:0'>
				<dt class='heading'><xsl:value-of select="php:function('lang', 'Admin 2')" /></dt>
			
				<dt><label for="field_admin_name_2"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
				<dd><input type='text' id='field_admin_name_2' name="contacts[1][name]" value='{organization/contacts[2]/name}'/></dd>
			
				<dt><label for="field_admin_ssn_2"><xsl:value-of select="php:function('lang', 'Social Security Number')" /></label><br /></dt>
				<dd><input type='text' id='field_admin_ssn_2' name="contacts[1][ssn]" value='{organization/contacts[2]/ssn}'/></dd>
			
				<dt><label for="field_admin_email_2"><xsl:value-of select="php:function('lang', 'Email')" /></label><br /></dt>
				<dd><input type='text' id='field_admin_email_2' name="contacts[1][email]" value='{organization/contacts[2]/email}'/></dd>
			
				<dt><label for="field_admin_phone_2"><xsl:value-of select="php:function('lang', 'Phone')" /></label><br /></dt>
				<dd><input type='text' id='field_admin_phone_2' name="contacts[1][phone]" value='{organization/contacts[2]/phone}'/></dd>
			</dl>
		
		</xsl:if>

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

]]>
</script>
        <div class="form-buttons">
            <input type="submit">
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


